<?php
$name=date("Y-m-d h:i:sa");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\" $name.csv\"");
$data=stripcslashes($_REQUEST['csv_text']);
echo $data; 
?>