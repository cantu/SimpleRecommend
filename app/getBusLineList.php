<?php
/**
 * Created by PhpStorm.
 * User: tusion
 * Date: 14-12-16
 * Time: 下午3:03
 */

require_once('./tools/simple_html_dom.php');


/*
 * 获取北京所有公交路名称数组,只是获取了数字开头的公交线路，运通和郊区线路没有采集
 * http://bus.mapbar.com/beijing/xianlu_Y/
 */
function parseSubwayName(){
    $bus_line_array = array();

    for($i=1; $i<=1; $i++){
        $busline_url = "http://bus.mapbar.com/beijing/xianlu_".$i;
        $html = file_get_html($busline_url);
        $table = $html->find('dd');
        var_dump( $table );
        /*
        foreach ($table->find('a') as $line) {
            //$id = $line->first_child()->class;
            $name_str = $line->innertext;
            //$name = "地铁" . iconv('GB2312', "UTF-8", $name_str);
            echo "$name_str\n";
            $subway_array[] = $name_str;
        }
        echo "cyclye $i, taotal get" . count($bus_line_array);
        */
    }
    echo "taotal get number" . count($bus_line_array);
    return $bus_line_array;
}



parseSubwayName();

?>