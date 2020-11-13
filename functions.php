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
?>