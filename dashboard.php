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
                $selected = "selected";

            }?>
            <option <?=$selected?>>Outcome</option>
            <?php
            foreach ($outcome as $index=>$out){
                $selected = "";
                if($_SESSION[$_GET['pid']."_dash_outcome_val"] != "" && $_SESSION[$_GET['pid']."_dash_outcome_val"] == $index && array_key_exists('dash',$_GET)){
                    $selected = "selected";
                }
                echo '<option name="'.$out.'" value="'.$index.'" '.$selected.'>'.$module->getFieldLabel($out).'</option>';
            }
            ?>
        </select>
    </div>
    <div id="filterBy" style="padding-bottom: 10px;width:100%" class="dropdown-check-list" tabindex="100">
        <span class="anchor form-control">Filter By...</span>
        <ul class="items" id="filter">
            <?php
            foreach ($filterby as $index=>$filter){
                $filter_options = $module->getChoiceLabels($filter, $project_id);
                echo '<label class="select-header">'.$module->getFieldLabel($filter).'</label>';
                foreach ($filter_options as $key => $option){
                    $checked = "";
                    if(!array_key_exists('dash',$_GET) || (array_key_exists('dash',$_GET) && !array_key_exists($_GET['pid'] . "_dash_filter_val",$_SESSION) && empty($_SESSION[$_GET['pid'] . "_dash_filter_val"]))){
                        $checked = "checked";
                    }else if(array_key_exists('dash',$_GET) && array_key_exists($_GET['pid'] . "_dash_filter_val",$_SESSION) && $_SESSION[$_GET['pid'] . "_dash_filter_val"] == ""){
                        $checked = "";
                    }else if(!empty($_SESSION[$_GET['pid'] . "_dash_filter_val"]) && array_key_exists('dash',$_GET)) {
                        $filters = explode(',', $_SESSION[$_GET['pid'] . "_dash_filter_val"]);
                        $checked = "";
                        foreach ($filters as $index => $sfilter) {
                            $filter_data= explode('*', $sfilter);
                            if($filter == $filter_data[0]){
                                if($filter_data[1] == $key){
                                    $checked = "checked";
                                }

                            }
                        }
                    }
                    echo "<li><input type='checkbox' name='".$filter."' value='".$filter."*".$key."' ".$checked."> ".$option."</li>";
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
                if($_SESSION[$_GET['pid']."_dash_condition1_val"] != "" && $_SESSION[$_GET['pid']."_dash_condition1_val"] == $index && array_key_exists('dash',$_GET)){
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
                if($_SESSION[$_GET['pid']."_dash_condition2_val"] != "" && $_SESSION[$_GET['pid']."_dash_condition2_val"] == $index && array_key_exists('dash',$_GET)){
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
    <script>
        $(document).ready(function() {
            $("#options td").click(function(){
                if($(this).attr('id') == "mean"){
                    $('.mean').show();
                    $('.topscore').hide();
                    $('#topscore').removeClass('selected');
                    $('#mean').addClass('selected');
                }else if($(this).attr('id') == "topscore"){
                    $('.mean').hide();
                    $('.topscore').show();
                    $('#topscore').addClass('selected');
                    $('#mean').removeClass('selected');
                }
            });
            });
    </script>
        <?php
        if(!empty($_GET['dash']) && ProjectData::startTest($_GET['dash'], $secret_key, $secret_iv, $_SESSION[$project_id."_dash_timestamp"])){
            $project_id = $_GET['pid'];
            $condition1_var = $module->getChoiceLabels($_SESSION[$_GET['pid']."_dash_condition1_var"], $project_id);
            $condition2_var = $module->getChoiceLabels($_SESSION[$_GET['pid']."_dash_condition2_var"], $project_id);
            $outcome_labels = $module->getChoiceLabels($_SESSION[$_GET['pid']."_dash_outcome_var"], $project_id);
            $topScoreMax = explode(" ",$outcome_labels[count($outcome_labels)-1])[0];

            $params = "(";
            if(!empty($_SESSION[$_GET['pid'] . "_dash_filter_val"])) {
                $filters = explode(',', $_SESSION[$_GET['pid'] . "_dash_filter_val"]);
                $aux_var_name = "";
                $numItems = count($filters);
                $i = 0;
                foreach ($filters as $index => $filter) {
                    $filter_data= explode('*', $filter);
                    $option = " OR ";
                    if($aux_var_name == ""){
                        $aux_var_name = $filter_data[0];
                        $option = "";
                    }
                    if($aux_var_name != $filter_data[0]){
                        $aux_var_name = $filter_data[0];
                        $option = ") AND (";
                    }
                    if(++$i === $numItems && $aux_var_name != $filter_data[0]) {
                        $option = ") AND (";
                    }
                    $params .= $option.getParamOnType($filter_data[0],$filter_data[1]);
                }
                $params .= ")";
            }

            $table = '<table class="table table-bordered pull-left" id="table_archive"><thead>
            <tr><th> </th>';
            foreach ($condition2_var as $index2 => $cond2){
                $table .= "<th>".$index2.", ".$cond2."</th>";
            }
            $table .= "<th>MISSING</th>";

            $table .= "</tr></thead><tbody>";

            $RecordSetParams = \REDCap::getData($project_id, 'array', null, null, null, null, false, false, false,
                $params
            );
            $recordsParams = ProjectData::getProjectInfoArray($RecordSetParams);

            $missing_total = array();
            foreach ($condition1_var as $index1 => $cond1) {
                $table .= "<tr><td><strong>".$index1.", ".$cond1."</strong></td>";
                $condition1 = getParamOnType($_SESSION[$_GET['pid']."_dash_condition1_var"],$index1);
                foreach ($condition2_var as $index2 => $cond2){
                    $condition2 = getParamOnType($_SESSION[$_GET['pid']."_dash_condition2_var"],$index2);
                    $RecordSet = \REDCap::getData($project_id, 'array', null, null, null, null, false, false, false,
                     $condition1." AND ".
                     $condition2
                    );
                    $records = ProjectData::getProjectInfoArray($RecordSet);
                    $arrayResult = array();

                    foreach ($recordsParams as $index => $paramRecord) {
                        foreach ($records as $record){
                             if($record['record_id'] == $paramRecord['record_id']){
                                 array_push($arrayResult,$record);
                             }
                         }
                    }
                    $calculations = getCalculations($arrayResult, $topScoreMax);
                    $missing_total[$index2] = $missing_total[$index2] + $calculations['missing'];
                    if ($calculations['total'] < $max) {
                     $table .= "<td>NULL (<" . $max . ")</td>";
                    } else {
                     $table .= "<td><span class='mean'>" . $calculations['calc'] . "</span><span class='topscore'>" . $calculations['total_score_percent'] . " %</span></td>";
                    }
                }
                #MISSING BY COLUMN
                $RecordSetMissingRow = \REDCap::getData($project_id, 'array', null, null, null, null, false, false, false,
                    $condition1
                );
                $recordsMissingRow = ProjectData::getProjectInfoArray($RecordSetMissingRow);
                $arrayResult = array();
                foreach ($recordsParams as $index => $paramRecord) {
                    foreach ($recordsMissingRow as $record){
                        if($record['record_id'] == $paramRecord['record_id'] && array_count_values($record[$_SESSION[$_GET['pid']."_dash_condition1_var"]])[1] == 0){
                            array_push($arrayResult,$record);
                        }
                    }
                }
                $calculations = getCalculations($arrayResult, $topScoreMax);
                if ($calculations['total'] < $max) {
                    $table .= "<td>NULL (<" . $max . ")</td>";
                } else {
                    $table .= "<td><span class='mean'>" . $calculations['calc'] . "</span><span class='topscore'>" . $calculations['total_score_percent'] . " %</span></td>";
                }
                $table .= "</tr>";
            }

            #MISSING BY ROW
            $table .= "<tr><td><strong>MISSING</strong></td>";
            foreach ($condition2_var as $index2 => $cond2){
                $condition2 = getParamOnType($_SESSION[$_GET['pid']."_dash_condition2_var"],$index2);
                $RecordSetMultiple = \REDCap::getData($project_id, 'array', null, null, null, null, false, false, false,
                    $condition2
                );
                $recordsMultiple = ProjectData::getProjectInfoArray($RecordSetMultiple);
                $arrayResult = array();
                foreach ($recordsParams as $index => $paramRecord) {
                    foreach ($recordsMultiple as $record){
                        if($record['record_id'] == $paramRecord['record_id'] && array_count_values($record[$_SESSION[$_GET['pid']."_dash_condition2_var"]])[1] == 0){
                            array_push($arrayResult,$record);
                        }
                    }
                }
                $calculations = getCalculations($arrayResult, $topScoreMax);
                if ($calculations['total'] < $max) {
                    $table .= "<td>NULL (<" . $max . ")</td>";
                } else {
                    $table .= "<td><span class='mean'>" . $calculations['calc'] . "</span><span class='topscore'>" . $calculations['total_score_percent'] . " %</span></td>";
                }
            }
            $arrayResult = array();
            foreach ($recordsParams as $index => $paramRecord) {
               if(array_count_values($record[$_SESSION[$_GET['pid']."_dash_condition1_var"]])[1] == 0 && array_count_values($record[$_SESSION[$_GET['pid']."_dash_condition2_var"]])[1] == 0){
                   array_push($arrayResult,$record);
               }
            }
            $calculations = getCalculations($arrayResult, $topScoreMax);
            if ($calculations['total'] < $max) {
                $table .= "<td>NULL (<" . $max . ")</td>";
            } else {
                $table .= "<td><span class='mean'>" . $calculations['calc'] . "</span><span class='topscore'>" . $calculations['total_score_percent'] . " %</span></td>";
            }
            $table .= "</tr>";
            if($_SESSION[$_GET['pid']."_dash_multiple1"] == "1"){
                $table .= "<tr><td><strong>MUTIPLE</strong></td>";
                foreach ($condition2_var as $index2 => $cond2) {
                    $condition2 = getParamOnType($_SESSION[$_GET['pid']."_dash_condition2_var"],$index2);
                    $RecordSetMultiple = \REDCap::getData($project_id, 'array', null, null, null, null, false, false, false,
                        $condition2
                    );
                    $recordsMultiple = ProjectData::getProjectInfoArray($RecordSetMultiple);
                    $arrayResult = array();
                    foreach ($recordsParams as $index => $paramRecord) {
                        foreach ($recordsMultiple as $record){
                            if($record['record_id'] == $paramRecord['record_id'] && array_count_values($record[$_SESSION[$_GET['pid']."_dash_condition1_var"]])[1] > 1){
                                array_push($arrayResult,$record);
                            }
                        }
                    }
                    $calculations = getCalculations($arrayResult, $topScoreMax);
                    if ($calculations['total'] < $max) {
                        $table .= "<td>NULL (<" . $max . ")</td>";
                    } else {
                        $table .= "<td><span class='mean'>" . $calculations['calc'] . "</span><span class='topscore'>" . $calculations['total_score_percent'] . " %</span></td>";
                    }
                }
                $table .= "</tr>";
            }
            $table .= "</tbody></table>";
            $table .="<table class='table table-bordered pull-right' id='options'>
                    <tr>
                        <td class='selected' id='mean'>Mean (SD) (n, Missing)</td>
                    </tr>
                    <tr>
                        <td id='topscore'>% Top score</td>
                    </tr>
                    
                    </table>";
            echo $table;

        }

        ?>
</div>
