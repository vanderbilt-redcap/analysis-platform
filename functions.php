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
?>