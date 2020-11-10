<?php
namespace Vanderbilt\RppsReportingExternalModule;
require_once (dirname(__FILE__)."/classes/ProjectData.php");
$project_id = $_GET['pid'];
$max = $module->getProjectSetting('max');
$outcome = $module->getProjectSetting('outcome-field');
$filterby = $module->getProjectSetting('filterby-field');
$condition = $module->getProjectSetting('condition-field');
$condition_multiple = $module->getProjectSetting('condition-multiple');

?>
<script>
    $(document).ready(function() {
        var filterBy = document.getElementById('filterBy');
        filterBy.getElementsByClassName('anchor')[0].onclick = function (evt) {
            if (filterBy.classList.contains('visible'))
                filterBy.classList.remove('visible');
            else
                filterBy.classList.add('visible');
        }
    });
</script>
<div class="optionSelect">
    <h3>Dashboard</h3>
    <div class="alert alert-danger fade in col-md-12" id="errMsgContainerModal" style="display:none"></div>
    <div style="padding-bottom: 10px">
        <select class="form-control" id="outcome">
            <?php
            $selected = "";
            if(empty($_SESSION[$_GET['pid']."_dash_outcome_val"])){
                echo "HELLO";
                $selected = "selected";
            }?>
            <option <?=$selected?>>Outcome</option>
            <?php
            foreach ($outcome as $index=>$out){
                $selected = "";
                if($_SESSION[$_GET['pid']."_dash_outcome_val"] == $index && array_key_exists('dash',$_GET)){
                    $selected = "selected";
                }
                echo '<option name="'.$out.'" value="'.$index.'" '.$selected.'>'.$module->getFieldLabel($out).'</option>';
            }
            ?>
        </select>
    </div>
    <?php
    $visible = "";
    if(!empty($_SESSION[$_GET['pid']."_dash_filter_val"]) && array_key_exists('dash',$_GET)){
        $visible = "visible";
    }
    ?>
    <div id="filterBy" style="padding-bottom: 10px;width:100%" class="dropdown-check-list <?=$visible?>" tabindex="100">
        <span class="anchor form-control">Filter By...</span>
        <ul class="items" id="filter">
            <?php
            foreach ($filterby as $index=>$filter){
                $filter_options = $module->getChoiceLabels($filter, $project_id);
                echo '<label class="select-header">'.$module->getFieldLabel($filter).'</label>';
                foreach ($filter_options as $key => $option){
                    $checked = "";
                    if(!empty($_SESSION[$_GET['pid'] . "_dash_filter_val"]) && array_key_exists('dash',$_GET)) {
                        $filters = explode(',', $_SESSION[$_GET['pid'] . "_dash_filter_val"]);
                        if($filter == $_SESSION[$_GET['pid'] . "_dash_filter_var"]){
                            $checked = "";
                            foreach ($filters as $fvalue){
                                if($fvalue == $key){
                                    $checked = "checked";
                                }
                            }
                        }
                    }

                    echo "<li><input type='checkbox' onclick='selectOnlyOneGroup(this)' name='".$filter."' value='".$key."' ".$checked."> ".$option."</li>";
                }

            }
            ?>
        </ul>
    </div>
    <div style="padding-bottom: 10px">
        <select class="form-control" id="condition1">
            <option>Condition 1</option>
            <?php
            foreach ($condition as $index=>$cond){
                $selected = "";
                if($_SESSION[$_GET['pid']."_dash_condition1_val"] == $index && array_key_exists('dash',$_GET)){
                    $selected = "selected";
                }
                echo '<option name="'.$cond.'" value="'.$index.'" multiple1="'.$condition_multiple[$index].'" '.$selected.'>'.$module->getFieldLabel($cond).'</option>';
            }
            ?>
        </select>
    </div>
    <div style="padding-bottom: 10px">
        <select class="form-control" id="condition2">
            <option>Condition 2</option>
            <?php
            foreach ($condition as $index=>$cond){
                $selected = "";
                if($_SESSION[$_GET['pid']."_dash_condition2_val"] == $index && array_key_exists('dash',$_GET)){
                    $selected = "selected";
                }
                echo '<option name="'.$cond.'" value="'.$index.'" multiple2="'.$condition_multiple[$index].'" '.$selected.'>'.$module->getFieldLabel($cond).'</option>';
            }
            ?>
        </select>
    </div>
    <button onclick='loadTable(<?=json_encode($module->getUrl("loadTable.php"))?>);' class="btn btn-primary" style="float:right" id="loadTablebtn">Load Table</button>
</div>
<div class="optionSelect" style="padding-top: 20px" id="loadTable">
        <?php
        if(!empty($_GET['dash']) && ProjectData::startTest($_GET['dash'], $secret_key, $secret_iv, $_SESSION[$project_id."_dash_timestamp"])){
            $project_id = $_GET['pid'];
            $condition1_var = $module->getChoiceLabels($_SESSION[$_GET['pid']."_dash_condition1_var"], $project_id);
            $condition2_var = $module->getChoiceLabels($_SESSION[$_GET['pid']."_dash_condition2_var"], $project_id);

            $params = "";
            if(!empty($_SESSION[$_GET['pid'] . "_dash_filter_val"])) {
                $filters = explode(',', $_SESSION[$_GET['pid'] . "_dash_filter_val"]);
                foreach ($filters as $fvalue) {
                    $params .= "[" . $_SESSION[$_GET['pid'] . "_dash_filter_var"] . "] = '" . $fvalue . "' AND ";
                }
            }

            $table = '<table class="table table-bordered" id="table_archive"><thead>
            <tr><th> </th>';
            foreach ($condition2_var as $index2 => $cond2){
                $table .= "<th>".$index2.", ".$cond2."</th>";
            }

            $table .= "</tr></thead><tbody>";

            $missing_total = array();
            foreach ($condition1_var as $index1 => $cond1) {
                $table .= "<tr><td><strong>".$index1.", ".$cond1."</strong></td>";
                foreach ($condition2_var as $index2 => $cond2){
                    $RecordSet = \REDCap::getData($project_id, 'array', null,null,null,null,false,false,false,
                        $params." 
                        [".$_SESSION[$_GET['pid']."_dash_condition1_var"]."(".$index1.")] = '1' AND
                        [".$_SESSION[$_GET['pid']."_dash_condition2_var"]."] = '".$index2."'
                        ");
                    $records = ProjectData::getProjectInfoArray($RecordSet);
                    $array_mean = array();
                    $missing = 0;
                    $total_cond1 = 0;
                    foreach ($records as $record){
                        if(!array_key_exists($_SESSION[$_GET['pid']."_dash_outcome_var"],$record) || $record[$_SESSION[$_GET['pid']."_dash_outcome_var"]] == ""){
                            #MISSING
                            $missing += 1;
                        }else{
                            #TOTAL COND 1 COUNT
                            $total_cond1 += 1;
                            array_push($array_mean,$record[$_SESSION[$_GET['pid']."_dash_outcome_var"]]);
                        }
                    }

                    $average = 0;
                    $std_deviation = 0;
                    if(!empty($array_mean)){
                        #AVERAGE
                        $average = number_format(array_sum($array_mean)/count($array_mean),2);

                        #STANDARD DEVIATION
                        $std_deviation = number_format(std_deviation($array_mean),2);
                    }
                    $missing_total[$index2] = $missing_total[$index2]+$missing;

                    if($total_cond1 < $max){
                        $table .= "<td>NULL (<".$max.")</td>";
                    }else{
                        $table .= "<td><span class='mean'>".$average." (".$std_deviation.") (".$total_cond1.",".$missing.")</span><span class='topscore'></span></td>";
                    }

                }
                $table .= "</tr>";
            }
            $table .= "<tr><td><strong>MISSING</strong></td>";
            foreach ($condition2_var as $index2 => $cond2){
                if($missing_total[$index2] < $max){
                    $table .= "<td>NULL (<".$max.")</td>";
                }else{
                    $table .= "<td><span class='mean'>".$missing_total[$index2]."</span><span class='topscore'></span></td>";
                }
            }
            if($_SESSION[$_GET['pid']."_dash_multiple1"] == "1"){
                $table .= "<tr><td><strong>MUTIPLE</strong></td>";
                foreach ($condition2_var as $index2 => $cond2){
                    $table .= "<td></td>";
                }
            }
            $table .= "</tbody></table>";
            echo $table;
        }

        ?>
    </table>
</div>
