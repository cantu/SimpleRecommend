<?php
namespace Youche\SimpleRecommend\Bus;
/**
 * Created by PhpStorm.
 * User: tusion
 * Date: 15-2-2
 * Time: 下午5:10
 */

use Youche\SimpleRecommend;
require_once ( __DIR__."/../class/BusBasic.class.php");
class Bus extends \Youche\SimpleRecommend\BusBasic{




    /**
     * 列出1-999路所有公交车
     */

    function printAllBusLines(){

        for( $i=1; $i<=999; $i++){
            $data =  $this->getBusLine($i);
            $this->printBusLineInfo( $data);
        }
    }
}