<?php

class Lane {
	
	private $_name;
	private $_summoner;
	
	public function __construct($name){
		$this->_name = $name;
		$this->_summoner = null;
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function setName($name){
		$this->_name = $name;
	}
	
	public function getSummoner(){
		return $this->_summoner;
	}
	
	public function setSummoner($summoner){
		$this->_summoner = $summoner;
	}

}