<?php
namespace Vanderbilt\RppsReportingExternalModule;
require_once (dirname(__FILE__)."/classes/ProjectData.php");
$project_id = $_GET['pid'];
$max = $module->getProjectSetting('max');

#Outcome
$rpps_s_q_57 = $module->getChoiceLabels('rpps_s_q57', $project_id);

#Filter By
$rpps_s_q60 = $module->getChoiceLabels('rpps_s_q60', $project_id);

#Comparisson Vars
$rpps_s_q61 = $module->getChoiceLabels('rpps_s_q61', $project_id);
$rpps_s_q15 = $module->getChoiceLabels('rpps_s_q15', $project_id);

?>
<script>
    $(document).ready(function() {
        // $('#table_archive').dataTable( {"paging": false, "searching": false, "bInfo": false, "order": [0, "desc"]});

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
            <option>Outcome</option>
            <?php
            $selected = "";
            if($_SESSION[$_GET['pid']."_dash_outcome_val"] == "0"){
                $selected = "selected";
            }
            ?>
            <option name="rpps_s_q57" value="0" <?=$selected;?>>Please use the scale below to rate your overall experience in the research study, where 0 is the worst possible experience, and 10 is the best possible experience.</option>
        </select>
    </div>
    <?php
    $visible = "";
    if(!empty($_SESSION[$_GET['pid']."_dash_filter_val"])){
        $visible = "visible";
    }
    ?>
    <div id="filterBy" style="padding-bottom: 10px;width:100%" class="dropdown-check-list <?=$visible?>" tabindex="100">
        <span class="anchor form-control">Filter By...</span>
        <ul class="items" id="filter">
            <?php
            foreach ($rpps_s_q60 as $index => $option){
                $selected = "";
                if($_SESSION[$_GET['pid']."_dash_filter_val"] == $index){
                    $selected = "checked";
                }
                echo "<li><input type='checkbox' name='rpps_s_q60' value='".$index."' ".$selected."> ".$option."</li>";
            }
            ?>
        </ul>
    </div>
    <div style="padding-bottom: 10px">
        <select class="form-control" id="condition1">
            <option name="rpps_s_q61">Condition 1</option>
            <?php
            $selected = "";
            if($_SESSION[$_GET['pid']."_dash_condition1_val"] == "0"){
                 $selected = "selected";
            }
            ?>
            <option name="rpps_s_q61" value="0" <?=$selected;?>>Race</option>
        </select>
    </div>
    <div style="padding-bottom: 10px">
        <select class="form-control" id="condition2">
            <option name="rpps_s_q15">Condition 2</option>
            <?php
            $selected = "";
            if($_SESSION[$_GET['pid']."_dash_condition2_val"] == "0"){
            $selected = "selected";
            }
            ?>
            <option name="rpps_s_q15" value="0" <?=$selected;?>>Did the study require that you already have a disease or condition in order to enroll?</option>
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

            $filters = explode(',',$_SESSION[$_GET['pid']."_dash_filter_val"]);
            $params = "";
            foreach ($filters as $fvalue){
             $params .= "[".$_SESSION[$_GET['pid']."_dash_filter_var"]."] = '".$fvalue."' AND";
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
                        $table .= "<td>".$average." (".$std_deviation.") (".$total_cond1.",".$missing.")</td>";
                    }

                }
                $table .= "</tr>";
            }
            $table .= "<tr><td><strong>MISSING</strong></td>";
            foreach ($condition2_var as $index2 => $cond2){
                if($missing_total[$index2] < $max){
                    $table .= "<td>NULL (<".$max.")</td>";
                }else{
                    $table .= "<td>".$missing_total[$index2]."</td>";
                }
            }
            $table .= "</tbody></table>";
            echo $table;
        }

        ?>
    </table>
</div>
