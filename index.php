<?php declare(strict_types = 1);

require 'vendor/autoload.php';



$defaultHandler = new \App\DefaultHandler();
$defaultHandler->eventName = $_GET['event'] ?? '';


if($defaultHandler->eventName){
	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		if($defaultHandler->isSseRequest()){
			$sse = new \Sse\SSE();
			$sse->addEventListener($defaultHandler->eventName, $defaultHandler);
			$sse->start();
		}
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
}



