<?php declare(strict_types = 1);
namespace App;

use Sse\Event;
use Sse\Data;

class DefaultHandler implements Event {

	public $eventName = 'default';

	private $storage;
    private $data;


	public function __construct() {
        $this->storage = new Data('file', ['path' => 'tmp']);
    }

	public function update(){
		return json_encode($this->data);
	}

	public function check(){
        $this->data = $this->setOrGet();
     	if(!$this->data) return false;

    	if($this->data->time > time() - 2) {
			return true;
		}

        return false;
	}

	public function setOrGet($data = null){
		if($data) {
			$this->storage->set($this->eventName, json_encode([
				'body' => is_string($data) ? htmlentities($data) : $data,
	            'time' => time(),
			]));
		}

		return json_decode($this->storage->get($this->eventName) ?? '');
	}

	function isSseRequest() {
		return strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'text/event-stream') !== false;
	}
}
