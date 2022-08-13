<?php
require 'vendor/autoload.php';

use Sse\SSE;

$defaultHandler = new \App\DefaultHandler();

$eventName = $_SERVER['PATH_INFO'] ?? '';
if($eventName) {
	$defaultHandler->eventName = $eventName;
}


if($_SERVER['REQUEST_METHOD'] == 'GET'){
	$sse = new SSE();
	$sse->addEventListener($defaultHandler->eventName, $defaultHandler);
	$sse->start();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$data = $_POST;
	if(!array_count($data)){
		$data = json_decode(file_get_contents('php://input'),TRUE);	
	}
	if($data){
		$defaultHandler->setOrGet($data);
	}
}
