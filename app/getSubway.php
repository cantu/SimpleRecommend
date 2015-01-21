<?php
/**
 *
 *
 * User: tusion
 * Date: 14-12-11
 * Time: 上午11:39
 */
    require_once('./class/simple_html_dom.php');
    require_once('./class/aMapApi.class.php');


    function __autoload( $class_name)
    {
        require_once( $class_name.'.php');
        require_once('./tools/'.$class_name.'.php');
    }
    /*
     * 获取北京所有的地铁线路名称数组
     */
    function parseSubwayName(){
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

    /*
     * 获取地铁路线的地理信息,请求高德公交路线接口
     */
    function getSubwayPoi( $subway_array){
        foreach( $subway_array as $i=>$subway_name ){
            print"---------- $subway_name -------------------\n";
            $data = getBusLine( $subway_name );
            printBusLineInfo( $data );
        }

    }


$subway_array = parseSubwayName();
getSubwayPoi( $subway_array);


