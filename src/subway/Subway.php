<?php
namespace Youche\SimpleRecommend\Subway;
/**
 * Created by PhpStorm.
 * User: tusion
 * Date: 15-1-30
 * Time: 下午6:46
 */

//use Youche\SimpleRecommend;
use Youche\SimpleRecommend\YoucheAPI;

require_once ( __DIR__."/../class/BusBasic.class.php");
require_once ( __DIR__. "/../class/MapUtil.class.php");
require_once(__DIR__ . "/../class/AMapAPI.class.php");

class Subway extends \Youche\SimpleRecommend\BusBasic{
    private $subway_list = array();
    private $mapUtil;
    private $aMap;

    function __construct( $db_server='localhost',
                          $db_user='root',
                          $db_password='root',
                          $db_name= 'bus_line_db',
                          $debug=false)
    {
        parent::__construct( $db_server, $db_user, $db_password, $db_name, $debug);

        if( !isset( $this->mapUtil) )
        {
            $this->mapUtil = new \Youche\SimpleRecommend\MapUtils();
        }

        if( !isset( $this->mapUtil ))
        {
            $this->mapUtil = new \Youche\SimpleRecommend\MapUtils();
        }

    }

    function __destruct()
    {
        parent::__destruct();
        unset($this->aMap);
        unset($this->mapUtil);
    }

    /**
     * 获取所有地铁线路的中文名字.
     */
    private function parseSubwayLines()
    {
        $this->subway_list = $this->mapUtil->getSubwayName();
    }

    /**
     * 获取所有地铁线路的名字,返回地铁的列表
     * @return array 地铁列表
     */
    function getSubwayList()
    {
        if( \count($this->subway_list) == 0)
        {
            $this->parseSubwayLines();
        }

        return $this->subway_list;
    }


    function getALLSubwayDataToDB()
    {
        //判断已经爬取到所有的地铁线路名称表
        if( \count( $this->subway_list) === 0)
        {
            $this->parseSubwayLines();
        }

        if( !isset( $this->aMap))
        {
            $this->aMap = new \Youche\SimpleRecommend\AMapAPI();
        }

        //获取地铁路线的地理信息,请求高德公交路线接口
        foreach ($this->subway_list as $i => $subway_name) {
            print"------ [" . \count($this->subway_list) . "-$i] ---- $subway_name -------------------\n";
            $data = $this->aMap->getBusLineInfo($subway_name);
            $this->insertDataToDB( $data );
        }

    }

    function insertDataToDB( $json_array )
    {

        if( isset( $json_array ) )
        {
            //判断从高德获得的公交数据是否有效
            if( $json_array['status'] == 1 and $json_array['count']>0 )
            {
                $count = 0;
                foreach ($json_array['buslines'] as $line)
                {
                    //筛选出有效的地铁线路,去掉在建的线路
                    if( $line['type'] =='地铁' and !strstr( $line['name'], '在建'))
                    {
                        $format = "%2d, Route:%s,Distance:%-4.2f\n";
                        printf($format, $count++, $line['name'], floatval($line['distance']));

                        //第一步,计算站点间的连通性,存储链接矩阵
                        //var_dump( $line['busstops']);
                        //var_dump( $line['polyline']);
                        //foreach ( $line['busstops'] as $i=>$stop )
                        $line_distance = 0;
                        for( $i=0; $i< \count($line['busstops'])-1; $i++)
                        {
                            $start_stop = $line['busstops'][$i]['location'];
                            $end_stop = $line['busstops'][$i+1]['location'];
                            $sub_polyline = $this->mapUtil->getSubPolyline($start_stop, $end_stop,  $line['polyline'] );
                            $result = $this->mapUtil->parsePolyline( $sub_polyline );
                            $sub_distance = $result['distance'];
                            //var_dump($sub_distance);
                            $line_distance+= $result['distance'];
                            $start_stop_id = $line['busstops'][$i]['id'];
                            $end_stop_id = $line['busstops'][$i+1]['id'];
                            //$hash_key = \md5( $start_stop_id.'_'.$end_stop_id);
                            $hash_key = \md5( $sub_polyline);

                            $connection_insert_sql = ' INSERT INTO connection_tb ( start_stop_id, end_stop_id, distance, hash_key, start_stop, end_stop, sub_polyline )'
                                                    ." VALUES( '$start_stop_id', '$end_stop_id',$sub_distance, '$hash_key', '$start_stop','$end_stop', '$sub_polyline' )";

                            parent::executeSql( $connection_insert_sql);

                        }
                        //$result = $this->mapUtil->parsePolyline( $line['polyline']);
                        //$result = $this->mapUtil->parsePolyline( $sub_polyline );


                        //第二部,存储公交站.
                        foreach( $line['busstops'] as $busstop )
                        {

                            //var_dump( \mb_detect_encoding($busstop['name'], array("ASCII",'UTF-8', 'GB2312', 'GBK', 'BIG5') ) );
                            //$station_name = \iconv( 'GB2312', "UTF-8", $busstop['name']);
                            $station_name = $busstop['name'];
                            $busstop_id = $busstop['id'];
                            $location_lng = \explode(',', $busstop['location'])[0];
                            $location_lat = \explode(',', $busstop['location'])[1];
                            var_dump( $location_lat);
                            $station_insert_sql = ' INSERT INTO  station_tb( station_name, location_lng, location_lat, busstop_id)'
                                                ."VALUES('$station_name', $location_lng, $location_lat, '$busstop_id')";
                            parent::executeSql( $station_insert_sql);
                        }

                    }
                    else
                    {
                        //echo  $line['name']." 不是有效的地铁线路. \n";
                    }
                }
            }
            else
            {
                throw new \Exception("Error: insertDataToDB() json array has not validate data. \n");
            }

        }
        else
        {
            throw new \Exception("Error: insertDataToDB() json array is not set. \n");
        }
    }



    /**
     * 根据地铁的起点站 和 终点站,计算票价
     * @param $start_stop
     * @param $end_stop
     * @return float
     */
    function calculateCost( $start_stop, $end_stop ){
        $cost = 0.0;

        return $cost;
    }



}