<?php
/**
 * Created by PhpStorm.
 * User: tusion
 * Date: 14-12-16
 * Time: 下午4:55
 */
require_once('./tools/aMap.php');
require_once('Config.php');

function createDataBase( $db_name, $connection){
    $check_db_sql = "CREATE DATABASE ".$db_name;
    if ( $connection->query($check_db_sql) == FALSE ){
        echo "database:$db_name is already setup";
    }else{
        echo"database; $db_name is not exist, set up now.";
    }
}


function createBusStopDataTable( $tale, $conenction ){
    $create_table_sql = " CREATE TABLE  $table(
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    busstop_id VARCHAR(10) NOT NULL COMMENT='高德的公交站编号',
    busstop_name VARCHAR(20) NOT NULL,
    location_lat DECIMAL(3.6) UNSIGNED NOT NULL,
    location_lng DECIMAL (3.6) UNSIGNED NOT NULL,
    update_time TIMESTAMP  NOT NULL
 )";


}




function createBusLineDataTable( $table, $connection ){
    $create_table_sql = "CREATE TABLE $table(
    id  INT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    busline_id INT(12) UNSIGNED NOT NULL,
    busline_name VARCHAR(40) NOT NULL,
    distance FLOAT(8.3) UNSIGNED NOT NULL,
    bounds VACHAR(100) NOT NULL COMMENT='线路的范围，矩形',
    stops_num INT(2) UNSIGNED NOT NULL COMMENT='站的数量',
    bus_stops TEXT NOT NULL COMMENT='路线站点组合的ｊｓｏｎ',
    ployline  TEXT NOT NULL COMMENT='地图展现的轨迹点'
    )COMMENT='高德地图公交路线请求结果'
    ";

}

function getBusLine( ){

    for( $bus_line=1; $bus_line<999; $bus_line++){
        $data = getBusLine( $bus_line);
        $json = json_decode($data, true);
        if( $json['status'] == 1){
            if( $json['count'] >0){
                foreach ( $json['buslines'] as $bus_line) {

                    saveBusLine($bus_line);
                }
            }else{
                echo "error, bus line $bus_line is not exist";
            }
        }else{
            echo "error: not get  json data ";
        }
    }
}

/**
 * @param $json_array
 * @param $connection
 */
function saveBusLineData( $json_array, $connection){

}


/**
 * main()
 */
$connection = new mysqli( $sMysql_host, $sMysql_user, $sMysql_password, $sMysql_db );
if( $connection->error){
    die("mysql connect error: ".$connection->connect_error);
}
createDataBase( $sMysql_db, $connection);
createBusLineDataTable( $sBusLineTable, $connection);




?>