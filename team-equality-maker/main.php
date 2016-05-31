<?php

include 'summoner.class.php';
include 'team.class.php';
include 'lane.class.php';

$lanes = array("top", "jungle", "mid", "adc", "support");
$summoner_list = createSummmonerPopulation(30);
$team_list = buildTeams($summoner_list);
$lock = array();
define('LOCK_TIME', 2);

foreach ($team_list as $team){
	echo "TEAM rank : ".$team->getRank().PHP_EOL;
}

echo "---- EQUILIBRAGE ----".PHP_EOL;
equilibrateTeams($team_list);
//var_dump($team_list);
foreach ($team_list as $team){
	echo "TEAM rank : ".$team->getRank().PHP_EOL;
}

function buildTeams($summoner_list) {
	$team_list = array();
	$i = 1;
	foreach ($summoner_list as $summoner){
		if (!$summoner->assignToLane($team_list, $summoner->getFirstLane())){
			if (countUnassignedSummoners($summoner_list) - countAvailableSlots($team_list) >= 5) {
				$team = new Team();
				$team->setName("team".$i++);
				$team_list[] = $team;
				$summoner->assignToLane($team_list, $summoner->getFirstLane());
			} else {
				if (!$summoner->assignToLane($team_list, $summoner->getSecondLane())){
					if (!$summoner->assignToLane($team_list, firstSlotAvailable($team_list))){
					}
				}
			} 
		}
	}
	return $team_list;
}

function equilibrateTeams(&$team_list){
	$processed = true;
	while($processed){
		$processed = equilibrate($team_list);
	}
}

function createSummmonerPopulation($nb) {
	global $lanes;
	$name = "A";
	for ($i = 1; $i <= $nb; $i++) {
		$new_summoner = new Summoner();
		$new_summoner->setName($name++);
		$temp_lanes = $lanes;
		$lane_number = mt_rand(0, count($temp_lanes) - 1);
		$first_lane = $temp_lanes[$lane_number];
		unset($temp_lanes[$lane_number]);
		$temp_lanes = array_values($temp_lanes);
		$new_summoner->setFirstLane($first_lane);
		$lane_number = mt_rand(0, count($temp_lanes) - 1);
		$new_summoner->setSecondLane($temp_lanes[$lane_number]);
		$new_summoner->setRank(mt_rand(1, 6));
		$summoner_list[] = $new_summoner;
	}
	return $summoner_list;
}

function countUnassignedSummoners($summonerList){
	$count = 0;
	foreach ($summonerList as $summoner){
		if(!$summoner->isAssigned()){
			$count++;
		}
	}
	return $count;
}

function countAvailableSlots($team_list){
	$count = 0;
	foreach ($team_list as $team){
		$count += $team->countEmptyLanes();
	}
	return $count;
}

function firstSlotAvailable($team_list){
	foreach ($team_list as $team){
		foreach ($team->getLanes() as $lane){
			if (empty($lane->getSummoner())){
				return $lane->getName();
			}
		}
	}
	return false;
}

function getTeamToNerf($team_list){
	$team_temp = new Team();
	foreach ($team_list as $team){
		if ($team->getRank() > $team_temp->getRank()){
			$team_temp = $team;
		}
	}
	return $team_temp;
}

function getTeamToUp($team_list){
	$team_temp = getTeamToNerf($team_list);
	foreach ($team_list as $team){
		if ($team->getRank() < $team_temp->getRank()){
			$team_temp = $team;
		}
	}
	return $team_temp;
}

function equilibrate(&$team_list){
	global $lanes;
	global $lock;
	$team_to_up = getTeamToUp($team_list);
	$team_to_nerf = getTeamToNerf($team_list);
	echo $team_to_nerf->getRank()." - ".$team_to_up->getRank().PHP_EOL; 
	$team_delta_rank = $team_to_nerf->getRank() - $team_to_up->getRank();
	$team_diff_rank = round($team_delta_rank / 2);
	$smtg_done = false;
	foreach ($lanes as $lane){
		if (!isLocked($team_to_nerf->getLaneByName($lane)) || !isLocked($team_to_up->getLaneByName($lane), $lock)){
			$lane_delta_rank = $team_to_nerf->getLaneByName($lane)->getSummoner()->getRank() - $team_to_up->getLaneByName($lane)->getSummoner()->getRank();
			echo "lane_delta_rank : ".$lane_delta_rank." / team_diff_rank : ".$team_diff_rank.PHP_EOL;
			if ($lane_delta_rank > 0 && $lane_delta_rank <= $team_diff_rank){
				$dual_summoner = $team_to_nerf->getLaneByName($lane)->getSummoner();
				echo "SWITCH lane : ".$lane." / ".$team_to_nerf->getName()." <> ".$team_to_up->getName().PHP_EOL;
				$team_to_nerf->getLaneByName($lane)->setSummoner($team_to_up->getLaneByName($lane)->getSummoner());
				$team_to_up->getLaneByName($lane)->setSummoner($dual_summoner);
				$team_diff_rank -= $lane_delta_rank;
				$lock[] = array('lane' => $team_to_nerf->getLaneByName($lane), 'time' => LOCK_TIME);
				$lock[] = array('lane' => $team_to_up->getLaneByName($lane), 'time' => LOCK_TIME);
				$smtg_done = true;
			}
		} else {
			echo "VERROU !".PHP_EOL;
		}
	}
	decrementLock();
	return $smtg_done;
}

function isLocked($lane){
	global $lock;
	foreach ($lock as $l){
		if ($l['lane'] == $lane){
			return true;
		}
	}
	return false;
}

function decrementLock(){
	global $lock;
	foreach ($lock as $l){
		if($l['time'] > 0){
			$l['time']--;
		} else {
			unset($l);
		}
	}
}