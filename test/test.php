<?php
namespace Youche\SimpleRecommend\Test;
use Youche\SimpleRecommend;

require_once ( __DIR__. "/../src/class/AMapAPI.class.php");
require_once ( __DIR__. "/../src/class/MapUtil.class.php");
require_once ( __DIR__. "/../src/class/YoucheAPI.class.php");

/*
 //在命令行模式下,自动加载不能成功
function __autoload($class )
{
    $path =  __DIR__. "/../src/class/".$class.'.class.php';
    if( file_exists( $path ))
    {
        require_once( $path);
        var_dump( $path);
    }
    else
    {
        echo 'file not  found '.$path;
    }

}
*/

//$aMap = new Youche\SimpleRecommend\AMapAPI();
//$aMap->test();

//$MapUtil = new SimpleRecommend\MapUtils();
//$MapUtil->test();

$Youche = new SimpleRecommend\YoucheAPI();
$Youche->test();





