<?php

include 'player.class.php';
include 'team.class.php';
include 'lane.class.php';
include 'lock.class.php';

include_once 'functions.php';

$lanes = array("top", "jungle", "mid", "adc", "support");
$fails = 0;
$player_list = createPlayerPopulation(60);
$team_list = buildTeams($player_list);
$lock_list = array();
define('LOCK_TIME', 2);
define('MAX_FAILS', 50);

foreach ($team_list as $team){
	echo "TEAM rank : ".$team->getRank().PHP_EOL;
}

echo "---- EQUILIBRAGE ----".PHP_EOL;
equilibrateTeams($team_list, $lanes, $lock_list, $fails);
//var_dump($team_list);
foreach ($team_list as $team){
	echo $team->getName()."\trank : ".$team->getRank().PHP_EOL;
}