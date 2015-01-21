<?php
/**
 * 主要存放友车的接口
 *
 * User: tusion
 * Date: 14-12-6
 * Time: 上午9:07
 */


require_once('./HttpUtils.class.php');


class YoucheAPI
{
    const ROUTE_LIST_URL = "http://api.52youche.com/v2/route/list";


    /*
     * 获取路线列表
     * page = 10,20,100
     * http://api.52youche.com/v2/route/all?type=all&page=100&offset=200
     */
    function getRouteList($offset = 0, $page = 100)
    {
        $Http = new HttpUtils();

        $parameters = array('offset' => $offset, 'page' => $page);

        $data =$Http->getRequestWithParameter ( self::ROUTE_LIST_URL, $parameters);

        if ($data != null) {
            return $data;
        } else {
            throw new Exception('error: getRouteList() do not get json');
        }

    }


    function printRoutList( $data )
    {
        if( $data != null )
        {
            $json = json_decode($data, true);
            $count = 0;
            foreach ($json['return']['routes'] as $route)
            {
                $route_id = $route['route_id'];
                $count++;
                echo "[$count] Route:" . $route_id . "\n";
            }
        }
        else
        {
            throw new Exception('error, printRoutList() get a null input');
        }


    }

    function test()
    {
        $data = $this->getRouteList($offset=0, $page=10);
        $this->printRoutList( $data);
    }


}





