<?php
class Team {
	
	private $_name;
	private $_lanes; // array("top"=>Summoner, "jgl"=>Summoner, "mid"=>Summoner, "adc"=>Summoner, "sup"=>Summoner)
	
	public function __construct() {
		$this->_name = "Undefined";
		$this->_lanes = array(
				new Lane("top"),
				new Lane("jungle"),
				new Lane("mid"),
				new Lane("adc"),
				new Lane("support"),
		);
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function setName($name){
		$this->_name = $name;
	}
		
	public function getLanes(){
		return $this->_lanes;
	}
	
	public function getLaneByName($name){
		foreach ($this->_lanes as $lane){
			if ($lane->getName() === $name){
				return $lane;
			}
		}
	}
	
	public function setLanes($lanes){
		$this->_lanes = $lanes;
	}
	
	public function setLane($summoner, $lane){
		$$this->_lanes[$lane] = $summoner;
	}
	
	public function isFull(){
		return countEmptyLanes() == 0; 
	}
	
	public function countEmptyLanes(){
		$count = 0;
		foreach ($this->_lanes as $lane){
			if ($lane->getSummoner() == null){
				$count++;
			}
		}
		return $count;
	}
	
	public function getRank(){
		$rank = 0;
		foreach ($this->_lanes as $lane){
			if ($lane->getSummoner() != null){
				$rank += $lane->getSummoner()->getRank();
			}
		}
		return $rank;
	}
	
}