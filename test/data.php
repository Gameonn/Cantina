<?php


$data = $_POST['data'];

var_dump($data);

foreach ($data as $dat) {
	
	$dat2= json_decode($dat);
	echo $dat2[2] . "</br>";	
	print_r($dat2);
}




?> 