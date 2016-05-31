<?php
class Summoner {

	private $_name;
	private $_rank; // int : bronze = 1, silver = 2, gold = 3, platine = 4, diamond = 5, challenger = 6
	private $_first_lane; 
	private $_second_lane;  
	private $_assigned; //boolean
	
	public function __construct(){
		$this->_assigned = false;
	}

	public function getName(){
		return $this->_name;
	}
	
	public function setName($name){
		$this->_name = $name;
	}
	
	public function getRank(){
		return $this->_rank;
	}
	
	public function setRank($rank){
		$this->_rank = $rank;
	}
	
	public function getFirstLane(){
		return $this->_first_lane;
	}
	
	public function setFirstLane($first_lane){
		$this->_first_lane = $first_lane;
	}
	
	public function getSecondLane(){
		return $this->_second_lane;
	}
	
	public function setSecondLane($second_lane){
		$this->_second_lane = $second_lane;
	}

	public function isAssigned(){
		return $this->_assigned;
	}
	
	public function setAssigned($assigned){
		$this->_assigned = $assigned;
	}
	
	public function assignToLane(&$team_list, $lane){
		if ($lane != null){
			foreach ($team_list as $team){
				if ($team->getLaneByName($lane)->getSummoner() == null){
					$team->getLaneByName($lane)->setSummoner($this);
					$this->_assigned = true;
					return true;
				}
			}
		}
		return false;
	}

}