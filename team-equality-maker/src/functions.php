<?php
function buildTeams($player_list) {
	$team_list = array();
	$i = 1;
	foreach ($player_list as $player){
		if (!$player->assignToLane($team_list, $player->getFirstLane())){
			if (countUnassignedSummoners($player_list) - countAvailableSlots($team_list) >= 5) {
				$team = new Team();
				$team->setName("team".$i++);
				$team_list[] = $team;
				$player->assignToLane($team_list, $player->getFirstLane());
			} else {
				if (!$player->assignToLane($team_list, $player->getSecondLane())){
					if (!$player->assignToLane($team_list, firstSlotAvailable($team_list))){
					}
				}
			}
		}
	}
	return $team_list;
}

function equilibrateTeams(&$team_list, &$lanes, &$lock_list, &$fails){
	while(equilibrate($team_list, $lanes, $lock_list, $fails)){}
}

function createPlayerPopulation($nb) {
	global $lanes;
	$name = "A";
	for ($i = 1; $i <= $nb; $i++) {
		$new_player = new Player();
		$new_player->setName($name++);
		$temp_lanes = $lanes;
		$lane_number = mt_rand(0, count($temp_lanes) - 1);
		$first_lane = $temp_lanes[$lane_number];
		unset($temp_lanes[$lane_number]);
		$temp_lanes = array_values($temp_lanes);
		$new_player->setFirstLane($first_lane);
		$lane_number = mt_rand(0, count($temp_lanes) - 1);
		$new_player->setSecondLane($temp_lanes[$lane_number]);
		$new_player->setRank(mt_rand(1, 26));
		$player_list[] = $new_player;
	}
	return $player_list;
}

function countUnassignedSummoners($playerList){
	$count = 0;
	foreach ($playerList as $player){
		if(!$player->isAssigned()){
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
			if (empty($lane->getPlayer())){
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

function getTeamToUp($team_list, $team_to_nerf, $lock_list){
	$team_temp = $team_to_nerf;
	foreach ($team_list as $team){
		if ($team != $team_to_nerf && $team->getRank() < $team_temp->getRank()){
			if(!isLocked($lock_list, array($team_to_nerf, $team)) && !isLocked($lock_list, array($team, $team_to_nerf))){
				$team_temp = $team;
			}
		}
	}
	return $team_temp;
}

function equilibrate(&$team_list, &$lanes, &$lock_list, &$fails){
	$team_to_nerf = getTeamToNerf($team_list);
	$team_to_up = getTeamToUp($team_list, $team_to_nerf, $lock_list);
	echo $team_to_nerf->getName() ." (".$team_to_nerf->getRank().") - "
			.$team_to_up->getName()." (".$team_to_up->getRank().")".PHP_EOL;
			$team_delta_rank = $team_to_nerf->getRank() - $team_to_up->getRank();
			$team_diff_rank = round($team_delta_rank / 2);
			$switch_done = false;
			foreach ($lanes as $lane){
				$lane_delta_rank = $team_to_nerf->getLaneByName($lane)->getPlayer()->getRank()
				- $team_to_up->getLaneByName($lane)->getPlayer()->getRank();
				echo $lane."\t lane_delta_rank : ".$lane_delta_rank." / team_diff_rank : ".$team_diff_rank.PHP_EOL;
				if ($lane_delta_rank > 0 && $lane_delta_rank <= $team_diff_rank){
					$dual_player = $team_to_nerf->getLaneByName($lane)->getPlayer();
					echo "SWITCH lane : ".$lane." / ".$team_to_nerf->getName()." <> ".$team_to_up->getName().PHP_EOL;
					$team_to_nerf->getLaneByName($lane)->setPlayer($team_to_up->getLaneByName($lane)->getPlayer());
					$team_to_up->getLaneByName($lane)->setPlayer($dual_player);
					$team_diff_rank -= $lane_delta_rank;
					$switch_done = true;
				}
			}
			decrementLock($lock_list);
			$lock_list[] = new Lock(array($team_to_nerf, $team_to_up), LOCK_TIME);
			if(!$switch_done && $fails < MAX_FAILS){
				$fails++;
				$switch_done = true;
				echo "FAILED ! retry (".$fails.")".PHP_EOL;
				foreach ($lock_list as $lock){
					$lock->toString();
				}
			}
			return $switch_done;
}

function isLocked($lock_list, $item){
	foreach ($lock_list as $lock){
		if ($lock->getItem() == $item){
			return true;
		}
	}
	return false;
}

function decrementLock(&$lock_list){
	foreach ($lock_list as $key => $lock){
	if(!$lock->decrement()){
			unset($lock_list[$key]);
		}
	}
}