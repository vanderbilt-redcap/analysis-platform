<?php
function std_deviation($my_arr)
{
    $no_element = count($my_arr);
    $var = 0.0;
    $avg = array_sum($my_arr)/$no_element;
    foreach($my_arr as $i)
    {
        $var += pow(($i - $avg), 2);
    }
    return (float)sqrt($var/$no_element);
}
function isTopScore($value,$topScoreMax){
    if($topScoreMax == 5 && $value == 5){
        return true;
    }else if($topScoreMax == 10 && ($value == 9 || $value == 10)) {
        return true;
    }
    return false;
}

function getParamOnType($field_name,$index){
    $type = \REDCap::getFieldType($field_name);
    if($type == "checkbox"){
        return "[".$field_name."(".$index.")] = '1'";
    }
    return "[".$field_name."] = '".$index."'";
}

function getCalculations($records,$topScoreMax){
    $multiples_total = 0;
    $array_mean = array();
    $missing = 0;
    $total_cond1 = 0;
    $top_score = 0;
    foreach ($records as $record) {
        if(!array_key_exists($_SESSION[$_GET['pid']."_dash_outcome_var"],$record) || $record[$_SESSION[$_GET['pid']."_dash_outcome_var"]] == ""){
            #MISSING
            $missing += 1;
        }else{
            #TOTAL COND 1 COUNT
            $total_cond1 += 1;
            array_push($array_mean,$record[$_SESSION[$_GET['pid']."_dash_outcome_var"]]);
            if(isTopScore($record[$_SESSION[$_GET['pid']."_dash_outcome_var"]],$topScoreMax)){
                $top_score += 1;
            }
        }
    }

    $average = 0;
    $std_deviation = 0;
    if(!empty($array_mean)) {
        #AVERAGE
        $average = number_format(array_sum($array_mean) / count($array_mean), 2);

        #STANDARD DEVIATION
        $std_deviation = number_format(std_deviation($array_mean), 2);
    }
    $calc = $average." (".$std_deviation.") (".$total_cond1.",".$missing.")";
    $total_score_percent = number_format((($top_score/$total_cond1)*100),2);
    return array("total" => $total_cond1, "calc" => $calc, "missing" => $missing, "total_score_percent" => $total_score_percent);
}
?>