<?php
/**
 * 主要存放友车的接口
 *
 * User: tusion
 * Date: 14-12-6
 * Time: 上午9:07
 */


require_once('./tools/HttpUtils.php');


/*
 * page = 10,20,100
 * "http://api.52youche.com/v2/route/list?offset=$offset&page=$page";
 */
function getAllRoute( $offset=0, $page=100)
{
    $ROUTE_LIST_URL = "http://api.52youche.com/v2/route/list";
    $parameters= array('offset'=>$offset,
                        'page'=> $page);

    $data = HttpGetWithParameter($ROUTE_LIST_URL, $parameters);

    if( $data != null )
    {
        $json = json_decode($data, true);
        $count = 0;
        foreach( $json['return']['routes'] as $route )
        {
            $route_id = $route['route_id'];
            $count++;
            echo"[$count] Route:". $route_id."\n";
        }
    }else
    {
        echo "error: not get json";
    }

}

/*
 *
 */
getAllRoute( 0, 10);


?>



