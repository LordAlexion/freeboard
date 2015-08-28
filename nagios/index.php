<?php


$jsonData = file_get_contents('http://nagiosjson.elucas.dev/statusJson.php');

if ($jsonData == false) {
  echo "Error getting data";
  die();
}

$dataArray = json_decode($jsonData, true);

//print_r($dataArray);

$program = $dataArray['programStatus'];
$hosts = $dataArray['hosts'];
$services = $dataArray['services'];

$totalHosts = count($hosts);
$totalServices = count($services) + count($services["localhost"]) - 1;

$liveHostCount = 0;
$liveServiceCount = 0;
$downHostCount = 0;
$warningServiceCount = 0;
$criticalServiceCount = 0;

$ol = "<ol style='color: #bbbbbb; font-weight: bold; padding: 0; margin-left: 1em'>";
$downHostNames = $ol;
$warningServiceNames = $ol;
$criticalServiceNames = $ol;

foreach($hosts as $hostname => $info) {
  foreach($info as $process => $status) {
    if($process == "host_name" || $process == "current_state" || $process == "plugin_output") {
      $cutHosts[$hostname][$process] = $status;
    }
  }
}
foreach($services as $hostname => $httpx) {
  foreach($httpx as $http => $info) {
    foreach($info as $process => $status) {
      if($process == "host_name" || $process == "current_state" || $process == "plugin_output") {
        $cutServices[$hostname][$process] = $status;
      }
    }
  }
}
foreach($hosts as $hostname) {
  if($hostname["current_state"] == "0") {
    $liveHostCount++;
  }
  else {
    $downHostCount++;
    $downHostNames .= "<br /><li><div style='color:red'>Host</div> ".$hostname["host_name"]."</li>";
  }
}
foreach($services as $hostname) {
  foreach($hostname as $key => $servicename) {
    if($servicename["current_state"] == "0") {
      $liveServiceCount++;
    }
    elseif($servicename["current_state"] == "1") {
      $warningServiceCount++;
      $warningServiceNames .= "<br /><li><div style='color:orange'>".$key."</div> ".$servicename["host_name"]."</li>";
    }
    elseif($servicename["current_state"] == "2") {
      $criticalServiceCount++;
      $criticalServiceNames .= "<br /><li><div style='color:red'>".$key."</div> ".$servicename["host_name"]."</li>";
    }
  }
}

$downHostNames .= "</ol>";
$warningServiceNames .= "</ol>";
$criticalServiceNames .= "</ol>";


$freeboard = json_encode(array(
	'totalhosts' => $totalHosts,
	'totalservices' => $totalServices,
	'livehosts' => $liveHostCount,
	'liveservices' => $liveServiceCount,
	'c.downhosts' => $downHostCount,
	'c.warningservices' => $warningServiceCount,
	'c.criticalservices' => $criticalServiceCount,
	'n.downhosts' => $downHostNames,
	'n.warningservices' => $warningServiceNames,
	'n.criticalservices' => $criticalServiceNames,
));

echo $freeboard;

?>
