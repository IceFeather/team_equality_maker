<?php

class Lane {
	
	private $_name;
	private $_player;
	
	public function __construct($name){
		$this->_name = $name;
		$this->_player = null;
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function setName($name){
		$this->_name = $name;
	}
	
	public function getPlayer(){
		return $this->_player;
	}
	
	public function setPlayer($player){
		$this->_player = $player;
	}

}