<?php

include '../src/player.class.php';
include '../src/team.class.php';
include '../src/lane.class.php';
include '../src/lock.class.php';

include_once '../src/functions.php';

$lock = array();

$team1 = new Team();
$team1->setName("T1");
$team2 = new Team();
$team2->setName("T2");
$team3 = new Team();
$team3->setName("T3");
$team4 = new Team();
$team4->setName("T4");


//Test : Lock
$lock_array = array();
$lock_array[] = new Lock(array($team1, $team2), 2);
$lock_array[] = new Lock(array($team2, $team3), 2);

echo isLocked($lock_array, array($team1, $team2)) ? "true" : "false";