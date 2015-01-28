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
require_once('./MapUtil.class.php');
require_once('./Subway.class.php');

//$Map = new aMapApi();
//$Map->test();


//$point = new Location('116.123456789',  '24.123456789');
//var_dump( $point->getLocationName());
//$point = new Location('116.123456789',  '24.123456789');
//var_dump( $point->__get( "location_name"));

//$Youche = new YoucheAPI();
//$Youche->test();

//$map = new MapUtils();
//$map->test();

$subway = new Subway();
