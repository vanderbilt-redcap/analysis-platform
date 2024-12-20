<?php
namespace Vanderbilt\AnalysisPlatformExternalModule;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;
?>
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php echo $module->loadREDCapJS(); ?>
<script type="text/javascript" src="<?=$module->getUrl('js/jquery-ui.min.js')?>"></script>
<script type="text/javascript" src="<?=$module->getUrl('bootstrap-3.3.7/js/bootstrap.min.js')?>"></script>

<script type="text/javascript" src="<?=$module->getUrl('js/jquery.tablesorter.min.js')?>"></script>
<script type="text/javascript" src="<?=$module->getUrl('js/sortable.min.js')?>"></script>
<script type="text/javascript" src="<?=$module->getUrl('js/Chart.min.js')?>"></script>

<script type="text/javascript" src="<?=$module->getUrl('js/jquery.dataTables.min.js')?>"></script>
<script type="text/javascript" src="<?=$module->getUrl('js/dataTables.buttons.min.js')?>"></script>

<script type="text/javascript" src="<?=$module->getUrl('js/functions.js')?>"></script>

<link type='text/css' href='<?=$module->getUrl('bootstrap-3.3.7/css/bootstrap.min.css')?>' rel='stylesheet' media='screen' />
<link type='text/css' href='<?=$module->getUrl('css/sortable-theme-bootstrap.css')?>' rel='stylesheet' media='screen' />
<link type='text/css' href='<?=$module->getUrl('css/style.css')?>' rel='stylesheet' media='screen' />
<link type='text/css' href='<?=$module->getUrl('css/jquery-ui.min.css')?>' rel='stylesheet' media='screen' />
<link type='text/css' href='<?=$module->getUrl('css/jquery.dataTables.min.css')?>' rel='stylesheet' media='screen' />

<?php include_once ("functions.php");?>

<script type='text/javascript'>
    var app_path_webroot = '<?=APP_PATH_WEBROOT?>';
    var app_path_webroot_full = '<?=APP_PATH_WEBROOT_FULL?>';
    var app_path_images = '<?=APP_PATH_IMAGES?>';
    $(document).ready(function() {
        Sortable.init();
        $('[data-toggle="tooltip"]').tooltip();
    } );
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta http-equiv="Cache-control" content="public">
    <meta name="theme-color" content="#fff">
    <link rel="icon" href="">

    <title>Analysis Platform</title>
</head>
<body>
    <div class="container">
        <h3 class="header">Analysis Platform</h3>
        <?php include_once ('dashboard.php');?>
    </div>
</body>
</html>