<?php
namespace Youche\SimpleRecommend;
/**
 * Created by PhpStorm.
 * User: tusion
 * Date: 15-1-20
 * Time: 下午4:54
 */


class Location
{

    private $longtitude;
    private $latitude;
    private $simple_lng;
    private $simple_lat;
    private $location_name;
    //private $geo_name;
    //private $id;

    /**
     * 如果我们需要定义一个特殊的结构体，是强类型时采用类，弱类型时采用数组
     */
    function __construct( $lng, $lat)
    {
        settype( $this->longtitude, 'float');
        settype( $this->latitude,   'float');
        settype( $this->simple_lat, 'float');
        settype( $this->simple_lng, 'float');
        settype( $this->location_name, 'string');
        //settype( $this->geo_name,   'string');
        //settype( $this->id,          'int');

        $this->setLongtitude( $lng );
        $this->setLatitude( $lat );
        $this->simpleLoctionToArea();
        $this->generateLocationName();
        //echo "construct Location";
    }

    function __destruct()
    {
        unset( $this->longtitude);
        unset( $this->latitude);
        unset( $this->simple_lng);
        unset( $this->simple_lat);
        unset( $this->location_name);
    }
    /**
     * 对北京范围内简化为网格,网格大小为:85m(经度0.001度) *111m(纬度0.001度)
     * 天安门(高德)坐标:116.397428,39.90923
     * $point['lng'] = 经度(Longtitude)
     * $point['lat'] = 纬度(Latitude)
     *经度(Longtitude)的小数点第三位(0.001度)大概距离是85M
     *纬度(Latitude)     的小数点第三位(0.001度)大概距离是111M
     *http://www.storyday.com/wp-content/uploads/2008/09/latlung_dis.html
     */
    function simpleLoctionToArea()
    {
        if( isset($this->longtitude) and isset( $this->latitude) )
        {
            $this->simple_lng = round($this->longtitude, 3);
            $this->simple_lat = round($this->latitude, 3);
        }
        else
        {
            throw new Exception( 'error in simple_lng_lat_100M() ');
        }


    }

    /**
     * 网格名称:以网格左下方点(即网格内经度最小和纬度最小的点)的经纬度组合取名
     */
    function generateLocationName()
    {
        if( !isset( $this->simple_key)){
            if( !isset($this->simple_lng) or !isset( $this->simple_lat))
            {
                $this->simpleLoctionToArea();
            }
            $format = "%3.3f_%2.3f";
            $this->location_name = sprintf($format, $this->simple_lng, $this->simple_lat );

        }
    }


    function setLongtitude( $lng)
    {
        if( is_float( floatval($lng) ))
        {
            $this->longtitude = $lng;
        }
        else
        {
            throw new Exception( 'input longtitude is '.gettype($lng).', is not a float.');
        }
    }

    function setLatitude( $lat)
    {
        if( is_float( floatval($lat)))
        {
            $this->latitude = $lat;
        }
        else
        {
            throw new Exception( 'input latitude is '.gettype($lat).', is not a float.');
        }
    }


    /*
    function getLocationName(){
        return $this->location_name;
    }

    function getLongtitude(){
        return $this->longtitude;
    }

    function getLattitude(){
        return $this->latitude;
    }
    */



    function __get ($property_name )
    {
        if ( isset ($this->$property_name))
        {
            return( $this->$property_name);
        } else {
            return NULL;
        }
    }

    function __set($property_name, $value)
    {
        $this->$property_name = $value;
    }

    function __isset( $property_name)
    {
        return (isset($this->$property_name));
    }

    function __unset( $property_name)
    {
         unset($this->$property_name);
    }


    /**
     * 单元测试
     */
    function test()
    {
        //$point = new Location('116.123456789',  '24.123456789');
        //var_dump( $point->get_location_name());
    }

}
