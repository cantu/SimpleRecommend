<?php
namespace Youche\SimpleRecommend;
/**
 *
 * 主要存放高德地图的接口
 *
 * User: tusion
 * Date: 14-12-5
 * Time: 上午11:43
 */


use Symfony\Component\Config\Definition\Exception\Exception;

require_once('HttpUtils.class.php');

Class AMapAPI {

    const AMAP_JS_KEY = "0bb05a448c9198ae767ef649e5e16549";

    private $HttpUtils;

    function __construct()
    {
        $this->HttpUtils = new HttpUtils();

    }

    function __destruct()
    {
        unset( $this->HttpUtils);
    }

    /*
     * 高德接口: 输入公交编号, 输出公交路径的地图信息
     * //http://restapi.amap.com/v3/bus/linename?s=rsv3&extensions=all&key=0bb05a448c9198ae767ef649e5e16549&output=json&city=%E5%8C%97%E4%BA%AC&keywords=300
     *  输入: 300, 特9, 地铁2号线.
     */
    function getBusLineInfo( $bus_name )
    {
        $Bus_Url = "http://restapi.amap.com/v3/bus/linename";
        $parameters= array( 's'=>'rsv3',
                            'extensions'=>'all',
                            'key'=> self::AMAP_JS_KEY,
                            'output'=> 'json',
                            'city'=>'北京',
                            'keywords'=>$bus_name);


        $json = $this->HttpUtils->getRequestWithParameter($Bus_Url, $parameters);

        if( $json != null )
        {
            $json_array = json_decode($json, true);
            if( $json_array['status'] == 1)
            {

                if( $json_array['count'] > 0)
                {
                    return $json_array;
                }
                else
                {
                    throw new \Exception("Error: getBusLineInfo() return json not contain validate data.\n");
                }
            }
            else
            {
                throw new \Exception("Error: getBusLineInfo() return json status error \n");
            }
        }


    }

    /**
     * 打印公交路线详情,输入公交路线请求但会的json
     */
    function printBusLineInfo( $json_array )
    {

        if( isset( $json_array ) )
        {
            //$json = json_decode($data, true);
            if( $json_array['status'] == 1 and $json_array['count']>0 )
            {
                $count = 0;
                foreach( $json_array['buslines'] as $line )
                {
                    $format = "%2d, Route:%s,Distance:%-4.2f\n";
                    printf($format, $count++, $line['name'], floatval($line['distance']) );
                }
            }

        }
        else
        {
            echo "error: not get json";
        }
    }


    /*
     *
     *逆地理编码
     *输入一个点的经纬度，返回街道名称
     * http://restapi.amap.com/v3/geocode/regeo?location=116.396574,39.992706&key=0bb05a448c9198ae767ef649e5e16549&s=rsv3&radius=1000&extensions=all
     * 天安门(高德)坐标:116.397428,39.90923
     * $lng = 经度(Longtitude)
     * $lat = 纬度(Latitude)
     */

    function regeoDecode( $lng, $lat)
    {
        $url = "http://restapi.amap.com/v3/geocode/regeo";
        $parameters = array(    's' => 'rsv3',
                                'extensions' => 'base', //all
                                'key' => self::AMAP_JS_KEY,
                                'redius' => '1000',
                                'location' => strval($lng) . ',' . strval($lat));

        $data = $this->HttpUtils->getRequestWithParameter($url, $parameters);

        if ($data != null) {
            $json = json_decode($data, true);
            if ($json['status'] == 1 ) {
                echo "Address: " . $json['regeocode']['formatted_address'] . "\n";
            }

        } else {
            echo "error: not get json";
        }
    }

    /*
     *驾车导航，获取两点间的行车路径
     *http://restapi.amap.com/v3/direction/driving?origin=116.495,39.94&destination=116.4,39.805&strategy=0&s=rsv3&extensions=base&key=0bb05a448c9198ae767ef649e5e16549
     *
     *配置参数详见：http://lbs.amap.com/api/javascript-api/reference/search_plugin/
     * 默认值：base，返回基本地址信息
     * 当取值为：all，返回DriveStep基本信息+DriveStep详细信息
     */
    function searchDriverRoute( $start_lng, $start_lat, $end_lng, $end_lat ){
        $url = "http://restapi.amap.com/v3/direction/driving";
        $parameters=array(  's'=>'rsv3',
                            'extensions'=>'base', //all
                            'key'=> self::AMAP_JS_KEY,
                            'strategy' => '0', //速度最快策略
                            'origin' => strval($start_lng).','.strval($start_lat),
                            'destination' => strval($end_lng).','.strval($end_lat));
        $data = $this->HttpUtils->getRequestWithParameter($url, $parameters);

        if( $data != null ){
            $json = json_decode($data, true);
            if( $json['status'] == 1){
                echo"Route distance:". $json['route']['paths'][0]['distance']."\n";
                echo"Route duration:". $json['route']['paths'][0]['duration']."\n";
            }

        }else{
            echo "error: not get json";

        }
    }


    function test()
    {
        $data =  $this->getBusLine(300);
        $this->printBusLineInfo( $data);
        //$this->getBusLine('特9');
        $this->searchDriverRoute(116.495, 39.94, 116.4, 39.805);
        $this->regeoDecode(116.397428,39.90923); //天安门(高德)坐标:116.397428,39.90923

        $this->printAllBusLines();

    }


}