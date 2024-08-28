<?php declare(strict_types = 1);

require 'vendor/autoload.php';


$defaultHandler = new \App\DefaultHandler();

$eventName = $_SERVER['PATH_INFO'] ?? '';
if($eventName) {
	$defaultHandler->eventName = $eventName;
}


if($_SERVER['REQUEST_METHOD'] == 'GET' && $defaultHandler->isSseRequest() && $defaultHandler->eventName){
	$sse = new \Sse\SSE();
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
