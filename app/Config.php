<?php
/**
 * Created by PhpStorm.
 * User: tusion
 * Date: 14-12-12
 * Time: 上午6:56
 */

/**
 * mysql config
 */
static $config = array();

$config['mysql'] = array();
$config['mysql']['host'] = 'localhost';
$config['mysql']['user']= 'root';
$config['mysql']['password'] = 'root';

/**
 * for subway demo project
 */
$config['mysql']['subway'] = array();
$config['mysql']['subway']['db'] = 'subway_db';
$config['mysql']['subway']['line_tb'] = 'subway_line_tb';
$config['mysql']['subway']['station_tb'] = 'station_tb';
$config['mysql']['subway']['search_tb'] = 'search_tb';

$debug = true;

//$config['mysql']['db'] = 'simple_recommend_db';
//$config['mysql']['area_tb'] = 'area_tb';
//$config['mysql']['bus_line_tb']= 'bus_line_tb';






