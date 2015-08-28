<?php

header("Access-Control-Allow-Origin: *");

date_default_timezone_set('Europe/London');
$datetime = date("D, d M, H:i");

$giga = pow(10, 9);

$freespace = disk_free_space('/');
$freespace = round(($freespace / $giga), 2);

$totalspace = disk_total_space('/');
$totalspace = round(($totalspace / $giga), 2);

$prcntspace = round(((($totalspace - $freespace) / $totalspace) * 100), 1);

$load = sys_getloadavg();

$status = true;

$data = array('status'=>$status, 'date'=>$datetime, 'free'=>$freespace, 'total'=>$totalspace, 'percent'=>$prcntspace, 'load'=>$load[0]);

//$fp = fopen('data.json', 'w');
//fwrite($fp, json_encode($data));
//fclose($fp);

echo json_encode($data);

//echo '<pre>';
//print_r($data);
//echo '</pre><br />';

//var_dump($_SERVER);







?>
