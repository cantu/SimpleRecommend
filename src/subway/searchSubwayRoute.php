<?php
namespace Youche\SimpleRecommend\subway;

require_once(__DIR__ . "/Subway.php");
$subway = new Subway();
$subway->setDebug(true);
$subway->connect();
$subway->createDataBase();
$subway->createTable();
//$subway->parseSubway();

$subway->getALLSubwayDataToDB();