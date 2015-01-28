<?php
/**
 * 地图计算的相关工具
 *
 * User: tusion
 * Date: 14-12-11
 * Time: 下午2:39
 */
require_once('./simple_html_dom.php');
require_once('./aMapAPI.class.php');

Class MapUtils {

    /**
     * 根据两点间的经纬度计算距离
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @return float
     */
    function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6367000; //approximate radius of earth in meters

        /*
          Convert these degrees to radians
          to work with the formula
        */

        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;

        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;

        /*
          Using the
          Haversine formula

          http://en.wikipedia.org/wiki/Haversine_formula

          calculate the distance
        */

        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return round($calculatedDistance);
    }



    /**
     * 获取北京所有公交路名称数组,只是获取了数字开头的公交线路，运通和郊区线路没有采集
     * http://bus.mapbar.com/beijing/xianlu_Y/
     * @return array
     */
    function getBusLineName(){
        $bus_line_array = array();

        for($i=1; $i<=9; $i++){
            $busline_url = "http://bus.mapbar.com/beijing/xianlu_".$i;
            $html = file_get_html($busline_url);
            $bus_lines = $html->find("dl.ChinaTxt dd")[0];
            //var_dump( $bus_lines);


            foreach ($bus_lines->children() as $line) {
                $name_str = $line->innertext;
                //$name_str= iconv('GB2312', "UTF-8", $name_str);
                $bus_line_array[] = $name_str;
            }
            echo "cyclye $i, taotal get" . count($bus_line_array)."\n";

        }
        echo "taotal get number" . count($bus_line_array);
        //var_dump( $bus_line_array);
        return $bus_line_array;
    }

    /**
     * 获取北京所有的地铁线路名称数组
     * http://www.bjsubway.com/station/xltcx/
     * @return array
     */
    function getSubwayName(){
        $subway_url = "http://www.bjsubway.com/station/xltcx/";
        //$html = new simple_html_dom();
        //$html->load_file( $subway_url );
        $html = file_get_html($subway_url);

        $subway_array = array();
        foreach ($html->find('div[class=line_name]') as $line) {
            //$id = $line->first_child()->class;
            $name_str = $line->first_child()->innertext;
            $name = "地铁" . iconv('GB2312', "UTF-8", $name_str);
            //echo "$id : $name\n";
            $subway_array[] = $name;
        }

        //var_dump($subway_array);
        return $subway_array;
    }




    function test(){

        $subway_array = $this->getSubwayName();

        $aMap = new aMapApi();

        //获取地铁路线的地理信息,请求高德公交路线接口
        foreach( $subway_array as $i=>$subway_name ){
            print"---------- $subway_name -------------------\n";
            $data = $aMap->getBusLine( $subway_name );
            $aMap->printBusLineInfo( $data );
        }


       // $busline_array = $this->getBusLineName();

    }
}

