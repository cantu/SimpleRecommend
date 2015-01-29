<?php
namespace Youche\SimpleRecommend;
/**
 * Created by PhpStorm.
 * User: tusion
 * Date: 14-12-8
 * Time: 下午3:29
 */


class HttpUtils{

    private $request_url;   //请求地址＋请求参数的组合，可以在浏览器中验证的版本
    private $get_time_out;

    function __construct(){

        $this->request_url = '';
        $this->get_time_out = 5;
    }

    function __destruct(){
            ;
    }

    function geUrl(){
        return $this->$request_url;
    }

    function printHttpTimeOut(){
        echo "time out: ".$this->$get_time_out;
    }


    /**
     *HTTP GET请求, 参数以数组方式输入, 用curl链接获取数据
     */
    function getRequestWithParameter($url, $para_array){
        if( ! is_string($url) or !isset($url)){
            echo "error, url is not set";
            return null;
        }
        if( !is_array($para_array) ) {
            echo "error, http get parameters array is not set";
            return null;
        }

        $encoded = '';
        // include GET as well as POST variables; your needs may vary.
        foreach($para_array as $name => $value) {
            $encoded .= urlencode($name).'='.urlencode($value).'&';
        }
        //将参数中的逗号还原
        //eg;
        //str_replace('%2C', ',', $encoded);

        // 去掉最后一个多余的＆
        $encoded = substr($encoded, 0, strlen($encoded)-1);

        $this->request_url = $url.'?'.$encoded;
        //echo "$request_url \n";

        try{
            $data = $this->curlData( $this->request_url );
            return $data;
        }catch( Exception $e){
            echo "error, in class: ".$this->$name.", Message: ".$e->getMessage();
        }

    }

    /*
     * reference: http://codular.com/curl-with-php
     * http://www.youranshare.com/blog/sid/64.html
     */
    private function curlData( $url ){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);              // 要访问的地址
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);   //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $this->get_time_out);
        //执行命令
        $result = curl_exec($curl);
        //显示获得的数据
        //var_dump($data);


        if ($result === false ) {
            throw new Exception("cURL Error: " . curl_error($curl) );
        }else{
            return $result;
        }

        //关闭URL请求
        curl_close($curl);
    }
}