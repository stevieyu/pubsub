<?php declare(strict_types = 1);

require 'vendor/autoload.php';



$defaultHandler = new \App\DefaultHandler();
$defaultHandler->eventName = $_GET['event'] ?? '';


if($defaultHandler->eventName){
	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		$sse = new \Sse\SSE();
		$sse->addEventListener('', $defaultHandler);
		if($defaultHandler->isSseRequest()){
			$sse->start();
		}else{
			foreach ($sse->getEventListeners() as $event => $handler) {
				if ($handler->check()) { // Check if the data is avaliable
					$data = $handler->update(); // Get the data
					$id = $sse->getNewId();
					$sse->sendBlock($id, $data, $event);
				}
			}
		}
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$data = $_POST;
		if(!count($data)){
			$data = json_decode(file_get_contents('php://input'),TRUE);
		}
		if($data){
			$defaultHandler->setOrGet($data);
		}
	}
}else{
	echo <<<EOF
	<!--<script src="https://unpkg.com/alpinejs@3" defer></script>-->
	<script src="https://unpkg.com/htmx.org@2" defer></script>
	<script src="https://unpkg.com/htmx-ext-sse@2/sse.js" defer></script>
EOF;

	echo <<<EOF
	<script>
	function updateEventName(event){
		const {target} = event;
		const form = htmx.find("form")

		if (target.value) {
			form.setAttribute('hx-post', '?event=' + target.value);
			htmx.process(form)
		}
	}
 </script>
	<input type="text" value="message"
	hx-trigger="load, keyup changed delay:500ms"
	hx-on:keyup="updateEventName(event)" />

	<form hx-post="?event=public" hx-target="#pub-results">
		<input type="text" name="message" value="default val">
		<button type="submit">Send</button>
	</form>
	<div id="pub-results"></div>
EOF;

	echo <<<EOF
	<script>
	function subar(event){
		const {target} = event;
		target.value = Math.floor(Date.now() / 1000);
		htmx.trigger('#sub', "changed");
	}
 </script>
	<input id="sub" type="search"
	name="time" value=""
	hx-get="?event=message"
	hx-trigger="load, changed delay:1s"
	hx-target="#sub-results"
	hx-on::after-request="subar(event)">
 <div id="sub-results"></div>
EOF;

	// echo <<<EOF
	// <div hx-ext="sse" sse-connect="?event=message" sse-swap="message">
	// 	Contents of this box will be updated in real time
	// 	with every SSE message received from the chatroom.
	// </div>
// EOF;

}



