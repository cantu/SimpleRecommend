<?php
/**
 * 地图计算的相关工具
 *
 * User: tusion
 * Date: 14-12-11
 * Time: 下午2:39
 */

   /**
    * 对北京范围内简化为网格,网格大小为:85m(经度0.001度) *111m(纬度0.001度)
    * 天安门(高德)坐标:116.397428,39.90923
    * $point['lng'] = 经度(Longtitude)
    * $point['lat'] = 纬度(Latitude)
    *经度(Longtitude)的小数点第三位(0.001度)大概距离是85M
    *纬度(Latitude)     的小数点第三位(0.001度)大概距离是111M
    *http://www.storyday.com/wp-content/uploads/2008/09/latlung_dis.html
    */
    function simplePoint( $point ){
        $simple_lng = round( $point['lng'] , 3);
        $simple_lat = round( $point['lat'] , 3);
        $simple_point = array( 'lng'=> $simple_lng,
                                'lat'=> $simple_lat);
        return $simple_point;
    }

    /**
     * 网格名称:以网格左下方点(即网格内经度最小和纬度最小的点)的经纬度组合取名
     */
    function getPointLoationAreaName( $point){
        if( !is_array($point) or !in_array('lng', $point) or !in_array('lat',$point) ){
            echo"Error, input location lat or lng error, pls check";
            return null;
        }
        $simple_point = simplePoint( $point );
        $formate = "%3.3f_%2.3f";
        $area_name = sprintf( $format, $simple_point['lnt'], $simple_point['lat']);

        return $area_name;
    }




?>