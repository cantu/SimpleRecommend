<?php
namespace Youche\SimpleRecommend\subway;

require_once(__DIR__ . "/BusBasic.class.php");
/**
 * Created by PhpStorm.
 * User: tusion
 * Date: 15-1-28
 * Time: 下午5:37
 */

$subway = new Subway();
$subway->setDebug(true);
$subway->connect();
$subway->createDataBase();
$subway->createTable();
$subway->insertSubwayToDb();