<?php
/**
 *  获取北京市的主要路网的地理信息,包括2,3,4,5环线 和主要高速路
 *
 * Created by PhpStorm.
 * User: tusion
 * Date: 14-12-11
 *
 */
    require_once( 'aMap.php');



    /*
     * 三环:公交300路, 总长48km
     */
    $bus_line = "300";
    $json = getBusline( $bus_line );
    printBusLineInfo( $json );


    /*
     * 四环:公交特9, 总长:66km
     */
    $bus_line = "特9";
    $json = getBusline( $bus_line );
    printBusLineInfo( $json );

    /*
     * 五环:多点构成环路的导航路径,总长122km
     */






?>
