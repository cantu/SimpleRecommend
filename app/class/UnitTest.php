<?php
/**
 * Created by PhpStorm.
 * User: tusion
 * Date: 15-1-21
 * Time: 下午2:04
 */

require_once('./aMapAPI.class.php');
require_once('./HttpUtils.class.php');
require_once('./Location.class.php');
require_once('./simple_html_dom.php');
require_once('./YoucheAPI.class.php');

//$Map = new aMapApi();
//$Map->test();


//$point = new Location('116.123456789',  '24.123456789');
//var_dump( $point->getLocationName());

$Youche = new YoucheAPI();
$Youche->test();
