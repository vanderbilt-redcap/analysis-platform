<?php
namespace Vanderbilt\AnalysisPlatformExternalModule;
require_once (dirname(__FILE__)."/classes/ProjectData.php");

$timestamp = strtotime(date("Y-m-d H:i:s"));
$_SESSION[$_GET['pid']."_dash_timestamp"] = $timestamp;
$_SESSION[$_GET['pid']."_dash_outcome_var"] = $_POST['outcomevar'];
$_SESSION[$_GET['pid']."_dash_outcome_val"] = $_POST['outcomeval'];
$_SESSION[$_GET['pid']."_dash_filter_var"] = $_POST['filtervar'];
$_SESSION[$_GET['pid']."_dash_filter_val"] = $_POST['filterval'];
$_SESSION[$_GET['pid']."_dash_condition1_var"] = $_POST['condition1var'];
$_SESSION[$_GET['pid']."_dash_condition1_val"] = $_POST['condition1val'];
$_SESSION[$_GET['pid']."_dash_condition2_var"] = $_POST['condition2var'];
$_SESSION[$_GET['pid']."_dash_condition2_val"] = $_POST['condition2val'];
$_SESSION[$_GET['pid']."_dash_multiple1"] = $_POST['multiple1'];
$_SESSION[$_GET['pid']."_dash_multiple2"] = $_POST['multiple2'];

$codeCrypt = ProjectData::getCrypt("start_".$timestamp,'e',$secret_key,$secret_iv);

echo json_encode($codeCrypt);
?>