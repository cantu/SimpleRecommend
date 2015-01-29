<?php
namespace Youche\SimpleRecommend\Test;
use Youche\SimpleRecommend;

require_once ( __DIR__. "/../src/class/AMapAPI.class.php");
require_once ( __DIR__. "/../src/class/MapUtil.class.php");
require_once ( __DIR__. "/../src/class/YoucheAPI.class.php");

//$aMap = new Youche\SimpleRecommend\AMapAPI();
//$aMap->test();

//$MapUtil = new SimpleRecommend\MapUtils();
//$MapUtil->test();

$Youche = new SimpleRecommend\YoucheAPI();
$Youche->test();


