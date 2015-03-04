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
                          $db_name= 'subway_db',
                          $debug=false)
    {
        parent::__construct( $db_server, $db_user, $db_password, $db_name, $debug);

        if( !isset( $this->mapUtil) )
        {
            $this->mapUtil = new \Youche\SimpleRecommend\MapUtils();
        }

        if( !isset( $this->aMap))
        {
            $this->aMap = new \Youche\SimpleRecommend\AMapAPI();
        }

    }

    function __destruct()
    {
        parent::__destruct();
        unset($this->aMap);
        unset($this->mapUtil);
    }


    /**
     * 获取所有地铁线路的名字,返回地铁的列表
     * @return array 地铁列表
     */
    function getSubwayList()
    {
        if( \count($this->subway_list) == 0)
        {
            $this->subway_list = $this->mapUtil->getSubwayName();
        }

        return $this->subway_list;
    }


    function getALLSubwayDataToDB()
    {
        $this->getSubwayList();

        //获取地铁路线的地理信息,请求高德公交路线接口
        foreach ($this->subway_list as $i => $subway_name) {
            print"------ [" . \count($this->subway_list) . "-$i] ---- $subway_name -------------------\n";
            $data = $this->aMap->getBusLineInfo($subway_name);
            $this->insertDataToDB( $data );
        }
        $this->cleanDB();
    }

    function insertDataToDB( $json_array )
    {

        if( isset( $json_array ) )
        {
            //判断从高德获得的公交数据是否有效
            if( $json_array['status'] == 1 and $json_array['count']>0 )
            {
                $count = 0;
                foreach ($json_array['buslines'] as $line) {
                    $line_name = $line['name'];
                    //筛选出有效的地铁线路,去掉在建的线路
                    if ($line['type'] == '地铁' and !strstr($line['name'], '在建')) {
                        $format = "%2d, Route:%s,Distance:%-4.2f\n";
                        printf($format, $count++, $line['name'], floatval($line['distance']));

                        //第一步,计算站点间的连通性,存储链接矩阵
                        $line_distance = 0;
                        for ($i = 0; $i < \count($line['busstops']) - 1; $i++) {
                            $start_stop = $line['busstops'][$i]['location'];
                            $end_stop = $line['busstops'][$i+1]['location'];
                            $sub_polyline = $this->mapUtil->getSubPolyline($start_stop, $end_stop, $line['polyline']);
                            //检查字段的长度是否安全
                            if( \strlen($sub_polyline) > 4000)
                            {
                                throw new \Exception("Error: connection_tb sub_polyline is short,".
                                    " the length of sub_polyline is : ".\strlen($sub_polyline)."\n");
                            }

                            $result = $this->mapUtil->parsePolyline($sub_polyline);
                            $sub_distance = $result['distance'];
                            //var_dump($sub_distance);
                            //自己根据没一段路线计算出的两个站之间的里程
                            $line_distance += $result['distance'];
                            $start_stop_id = $line['busstops'][$i]['id'];
                            $end_stop_id = $line['busstops'][$i + 1]['id'];
                            //$hash_key = \md5( $start_stop_id.'_'.$end_stop_id);
                            $hash_key = \md5($sub_polyline);
                            $start_name = $line['busstops'][$i]['name'];
                            $end_name = $line['busstops'][$i + 1]['name'];

                            $connection_insert_sql = 'INSERT INTO connection_tb ( start_name, end_name, start_stop_id, '
                                .'end_stop_id, distance, hash_key, start_stop, end_stop, line_name, sub_polyline )'
                                . " VALUES( '$start_name', ' $end_name', "
                                ."'$start_stop_id', '$end_stop_id',$sub_distance, '$hash_key', '$start_stop','$end_stop', '$line_name', '$sub_polyline' )";

                            parent::executeSql($connection_insert_sql);

                        }
                        echo( "d= ".$line_distance.", aMap:".$line['distance']."\n" );
                        //$result = $this->mapUtil->parsePolyline( $line['polyline']);
                        //$result = $this->mapUtil->parsePolyline( $sub_polyline );


                        //第二步,存储公交站.
                        $busstop_total ='';
                        foreach ($line['busstops'] as $busstop) {

                            //var_dump( \mb_detect_encoding($busstop['name'], array("ASCII",'UTF-8', 'GB2312', 'GBK', 'BIG5') ) );
                            //$station_name = \iconv( 'GB2312', "UTF-8", $busstop['name']);
                            $busstop_total .= $busstop['sequence'].':'. $busstop['id'].';';
                            $station_name = $busstop['name'];
                            $busstop_id = $busstop['id'];
                            $location_lng = floatval(\explode(',', $busstop['location'])[0]);
                            $location_lat = floatval(\explode(',', $busstop['location'])[1]);
                            $station_insert_sql = "INSERT INTO  station_tb( station_name, location_lng, location_lat, busstop_id, line_name)
                                VALUES('$station_name', $location_lng, $location_lat, '$busstop_id', '$line_name')";
                            parent::executeSql($station_insert_sql);
                        }

                        //echo( \strlen($busstop_total)."   ;  ". \strlen( $line['polyline']) ."\n");

                        //第三步. 存储公交线路
                        //检查字段的长度是否安全
                        if( \strlen($busstop_total) > 4000)
                        {
                            throw new \Exception("Error: bus_line_tb busstops is short,".
                            " the length of busstop_total is : ".\strlen($busstop_total)."\n");
                        }
                        if( \strlen($line['polyline']) > 65530)
                        {
                            throw new \Exception("Error: bus_line_tb polyline is short,".
                                " the length of polyline is : ".\strlen($line['polyline'])."\n");
                        }
                        //有的公交线路没有公司的名称返回"[]",这里默认为数组了.
                        if( is_array($line['company']) )
                        {
                            $line['company']='';
                        }
                        $busline_insert_sql = ' INSERT INTO bus_line_tb ( line_name, line_type, start_stop, end_stop,'
                                            .' company,distance, basic_price, total_price,'
                                            .' bounds, busstops, bus_line_id, polyline)'
                                            ."VALUES( '".$line['name']."', '" .$line['type']. "', '". $line['start_stop'] ."', '".$line['end_stop']."', '"
                                            .$line['company']."', ".$line['distance'].", ".$line['basic_price'].", ".$line['total_price']
                                            .", '".$line['bounds']."', '". $busstop_total ."', ". $line['id'].", '".$line['polyline'] . "' )";
                        //var_dump($busline_insert_sql);
                        parent::executeSql($busline_insert_sql);


                    }
                    else
                    {
                        echo $line['name'] . " 不是有效的地铁线路. \n";
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
     * 对入库数据洗库
     */
    function cleanDB()
    {

        //表 subway_db.connection_tb是干净的不用洗.
        //表 subway_db.station_tb 有重复数据.
        //算鸟.算鸟...还是在取数据的时候自己洗吧

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


    /**
     * 将地铁的路网信息写成json格式的文件,提供给web页面js解析使用
     *
     */
    function outputDataToJson( )
    {
        // station.json
        $stations = array();
        $sql = "select station_name, location_lat, location_lng, busstop_id from station_tb group by station_name order by id;";
        $result = parent::querySql($sql);
        $i=0;
        while( $row = \mysql_fetch_array( $result, MYSQL_ASSOC ) )
        {
            $stations[$i++] = $row;
        }
        $json_data = json_encode( array( "total"=>count($stations), "stations"=>$stations ) );

        $file_name = '../../html/stations.json';
        $file = \fopen( $file_name, 'w');
        \fwrite( $file, $json_data );
        \fclose( $file );
        mysql_free_result( $result );


        // connection.json
        $connections = array();
        $sql = "SELECT * FROM connection_tb";
        //$result = \mysql_query( $sql );
        $result = parent::querySql($sql);
        //var_dump( $result);
        $i=0;
        while( $row = \mysql_fetch_array( $result, MYSQL_ASSOC ) and $i<10)
        //while( $row = \mysql_fetch_array( $result, MYSQL_ASSOC ) )
        {
            $connections[$i++] = $row;
        }
        $json_data = json_encode( $connections );

        $file_name = '../../html/connection.json';
        $file = \fopen( $file_name, 'w');
        \fwrite( $file, $json_data);
        \fclose( $file );
        mysql_free_result( $result );

    }

    /**
     *
     */
    function test()
    {
        //地铁2号线内环
        $p =  "116.373019,39.948703;116.374118,39.948796;116.3752,39.948857;116.377039,39.948906;116.377896,39.94893;116.387418,39.948968;116.390149,39.948968;116.393018,39.948971;116.393776,39.948972;116.393776,39.948972;116.394095,39.948973;116.395683,39.948985;116.402583,39.949111;116.40479,39.949131;116.405984,39.949145;116.407377,39.949172;116.408228,39.949179;116.408228,39.949179;116.409103,39.949187;116.414884,39.949305;116.416347,39.949332;116.417143,39.949334;116.417143,39.949334;116.417975,39.949331;116.418292,39.949342;116.418716,39.949355;116.420272,39.949409;116.421938,39.949499;116.424222,39.949582;116.424223,39.949582;116.426081,39.949643;116.427765,39.949705;116.427766,39.949705;116.427768,39.949705;116.428852,39.949727;116.428853,39.949727;116.428854,39.949727;116.429727,39.949731;116.429728,39.949731;116.42973,39.949731;116.43023,39.949727;116.430566,39.949718;116.430812,39.949705;116.430989,39.949692;116.431166,39.949675;116.431342,39.94965;116.431496,39.949622;116.431617,39.949597;116.431745,39.949565;116.431845,39.949537;116.431936,39.949509;116.432082,39.949464;116.432216,39.949414;116.432444,39.949313;116.432583,39.949232;116.432661,39.949182;116.432723,39.949142;116.432783,39.949099;116.432857,39.949039;116.432934,39.948973;116.433016,39.948898;116.433107,39.948812;116.433163,39.948749;116.433225,39.948673;116.433292,39.948594;116.43336,39.948511;116.433401,39.948452;116.433454,39.948361;116.433511,39.948244;116.433564,39.948096;116.433594,39.947989;116.433614,39.947918;116.433633,39.947832;116.433647,39.947724;116.433655,39.947635;116.43366,39.947534;116.433661,39.947338;116.433548,39.944604;116.433552,39.941098;116.433552,39.941098;116.43355,39.940383;116.434129,39.934872;116.434136,39.933669;116.434136,39.933669;116.434145,39.932844;116.434264,39.930433;116.434405,39.928551;116.43456,39.926342;116.434568,39.925275;116.434584,39.924499;116.434584,39.924499;116.434596,39.923705;116.434835,39.919657;116.434906,39.917933;116.434999,39.915716;116.435075,39.914397;116.435099,39.914046;116.435487,39.91227;116.435749,39.911169;116.43576,39.911088;116.435797,39.910784;116.435801,39.910497;116.435797,39.910344;116.43578,39.90963;116.435783,39.908483;116.435783,39.908483;116.435782,39.907909;116.435797,39.907662;116.435816,39.907196;116.43584,39.906904;116.435869,39.906548;116.435907,39.90607;116.43593,39.905931;116.435954,39.905776;116.435962,39.905691;116.435962,39.90564;116.435958,39.905593;116.435942,39.905513;116.43593,39.905462;116.43591,39.90541;116.435887,39.905372;116.435865,39.905337;116.435836,39.905308;116.435797,39.905269;116.435775,39.905252;116.43576,39.905244;116.435738,39.905236;116.43568,39.905225;116.435609,39.905214;116.435504,39.905199;116.435361,39.905181;116.435192,39.905167;116.435191,39.905166;116.434881,39.905143;116.434757,39.905135;116.434575,39.905126;116.434314,39.905117;116.434313,39.905117;116.4334,39.905083;116.432181,39.905059;116.428489,39.90498;116.427248,39.904981;116.427248,39.904981;116.426136,39.904982;116.425795,39.904968;116.4254,39.90494;116.425203,39.904903;116.425017,39.904857;116.424776,39.90478;116.424526,39.904684;116.424288,39.904582;116.424046,39.904452;116.423603,39.904127;116.423104,39.903684;116.421331,39.902296;116.420843,39.901925;116.420267,39.901579;116.419974,39.901422;116.419741,39.901319;116.419575,39.901272;116.419389,39.901228;116.419198,39.901194;116.418991,39.901157;116.418779,39.901127;116.418597,39.901111;116.418437,39.901101;116.418339,39.901095;116.417042,39.901061;116.417042,39.901061;116.415718,39.901033;116.412026,39.900939;116.408299,39.900846;116.404562,39.900716;116.403414,39.900676;116.402834,39.900645;116.402358,39.900599;116.401572,39.900497;116.400678,39.900357;116.399742,39.900228;116.399452,39.900201;116.399203,39.900196;116.397875,39.900194;116.397875,39.900194;116.396686,39.900192;116.396083,39.900188;116.395439,39.900205;116.394726,39.900248;116.393846,39.900303;116.393145,39.90034;116.39161,39.900337;116.388072,39.900207;116.386592,39.900159;116.385702,39.900132;116.385084,39.900115;116.384132,39.90009;116.384132,39.90009;116.383199,39.900066;116.3822,39.900042;116.380386,39.899977;116.375349,39.899758;116.374314,39.899723;116.374314,39.899723;116.373194,39.899689;116.371422,39.899655;116.370146,39.899631;116.36856,39.899597;116.366753,39.899546;116.365731,39.89953;116.364382,39.899497;116.363354,39.899467;116.363354,39.899467;116.362395,39.899439;116.361846,39.899429;116.361192,39.899429;116.360537,39.899444;116.360065,39.899488;116.359719,39.899573;116.359431,39.899693;116.359224,39.8998;116.358935,39.899945;116.358616,39.900132;116.358358,39.900293;116.35812,39.900483;116.3579,39.900686;116.357719,39.90088;116.3575,39.901129;116.35729,39.901403;116.357128,39.901656;116.357025,39.901846;116.356949,39.902004;116.35691,39.902147;116.356882,39.90228;116.356863,39.902415;116.356676,39.904303;116.356676,39.904306;116.356676,39.904307;116.356676,39.904309;116.356595,39.906474;116.35659,39.907246;116.35659,39.907246;116.356585,39.908103;116.356545,39.91007;116.35654,39.910564;116.356459,39.912261;116.356351,39.914861;116.356268,39.91695;116.356268,39.916951;116.356167,39.919994;116.356129,39.921077;116.35603,39.92234;116.356012,39.922773;116.356012,39.923501;116.356012,39.923501;116.356012,39.923502;116.356004,39.924505;116.355868,39.931671;116.355868,39.932385;116.355868,39.932385;116.355868,39.933024;116.355762,39.933719;116.355676,39.934658;116.355575,39.936084;116.355498,39.937495;116.355498,39.937498;116.355498,39.937499;116.355498,39.9375;116.355425,39.939513;116.355426,39.940474;116.355426,39.940474;116.355426,39.941045;116.355444,39.9417;116.355463,39.942025;116.355478,39.942261;116.355502,39.942507;116.355529,39.942675;116.355586,39.9429;116.355655,39.943061;116.355733,39.943183;116.355801,39.943272;116.355888,39.94338;116.355996,39.943488;116.356109,39.943596;116.356253,39.943702;116.356494,39.943836;116.356684,39.943925;116.356913,39.944024;116.357344,39.94417;116.358041,39.94438;116.35898,39.944667;116.359331,39.944775;116.359821,39.944924;116.361265,39.945367;116.361266,39.945367;116.361562,39.945456;116.361564,39.945456;116.362475,39.945717;116.362476,39.945718;116.362934,39.945844;116.362936,39.945844;116.364784,39.946325;116.36642,39.94687;116.367259,39.947222;116.368464,39.947704;116.369077,39.947922;116.369588,39.94809;116.369946,39.948213;116.370486,39.948353;116.371001,39.948468;116.371355,39.948538;116.371618,39.948574;116.371925,39.948609;116.372131,39.948626;116.373019,39.948703";
        $start_stop = "116.355426,39.940474"; //西直门
        $end_stop = "116.373019,39.948703"; //积水潭

        $sub_polyline = $this->mapUtil->getSubPolyline($start_stop, $end_stop, $p);
    }

}