<?php
class Player {

	private $_name;
	/*
	 * rank :
	 * bronze	= 1,2,3,4,5
	 * silver	= 6,7,8,9,10
	 * gold 	= 11,12,13,14,15
	 * platine	= 16,17,18,19,20
	 * diamond	= 21,22,23,24,25
	 * challenger = 26
	 */
	private $_rank;
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
				if ($team->getLaneByName($lane)->getPlayer() == null){
					$team->getLaneByName($lane)->setPlayer($this);
					$this->_assigned = true;
					return true;
				}
			}
		}
		return false;
	}

}