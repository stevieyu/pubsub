<?php declare(strict_types = 1);
namespace App;

use Sse\Event;
use Sse\Data;

class DefaultHandler implements Event {

	public $eventName = 'default';

	private $storage;
	private $cache = 0;
    private $data;


	public function __construct() {
        $this->storage = new Data('file', ['path' => '/tmp/pubsub']);
    }

	public function update(){
		return json_encode($this->data);
	}

	public function check(){
        // Fetch data from the data instance
        $this->data = $this->setOrGet();
     	if(!$this->data) return false;

        // Check if this connection is a reconnect. If it is, just
        // record last message's time to prevent repeatly sending messages
        if(!$this->cache){
            $this->cache = $this->data->time;
            return false;
        }

        if($this->data->time !== $this->cache){
            $this->cache = $this->data->time;
            return true;
        }

        return false;
	}

	public function setOrGet($data = null){
		if($data) {
			$this->storage->set($this->eventName, json_encode([
				'body' => htmlentities($data),
	            'time' => time(),
			]));
		}

		return json_decode($this->storage->get($this->eventName) ?? '');
	}

	function isSseRequest() {
		if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'text/event-stream') !== false) {
			if (isset($_SERVER['HTTP_X_EVENT_SOURCE']) && $_SERVER['HTTP_X_EVENT_SOURCE'] !== '') {
				return true;
			}
		}
		return false;
	}
}
