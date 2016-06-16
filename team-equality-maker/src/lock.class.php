<?php
class Lock {
	
	private $_item;
	private $_time;
	
	public function __construct($item, $time) {
		$this->_item = $item;
		$this->_time = $time;
	}
	
	public function getItem() {
		return $this->_item;
	}
	
	public function setItem($item) {
		$this->_item = $item;
	}
	
	public function getTime() {
		return $this->_time;
	}
	
	public function setTime($time) {
		$this->_time = $time;
	}
	
	public function decrement() {
		if($this->_time > 0) {
			$this->_time--;
			return true;
		} else {
			return false;
		}
	}
	
	public function toString(){
		echo "[lock]\t";
		foreach ($this->_item as $team){
			echo $team->getName()." - ";
		}
		echo "\ttime: ".$this->_time.PHP_EOL;
	}
}