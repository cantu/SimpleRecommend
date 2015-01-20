<?php
/**
 * Created by PhpStorm.
 * User: tusion
 * Date: 14-12-8
 * Time: 下午3:29
 */


/**
 *HTTP GET请求, 参数以数组方式输入, 用curl链接获取数据
 */
function HttpGetWithParameter( $url, $para_array){
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

    $request_url = $url.'?'.$encoded;
    //echo "$request_url \n";

    $data = BasicHttpGetByCurl($request_url);

    if( $data != false ){
        return $data;
    }else{
        echo "error: http get nothing";
        return null;
    }

}

/*
 * reference: http://codular.com/curl-with-php
 * http://www.youranshare.com/blog/sid/64.html
 */
function BasicHttpGetByCurl( $url ){
    $timeout = 5;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_HEADER, 0);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
    //执行命令
    $result = curl_exec($curl);
    //显示获得的数据
    //print_r($data);
    //关闭URL请求
    curl_close($curl);

    if ($result === false ) {
        echo "cURL Error: " . curl_error($curl);
        return null;
    }
    else{

        return $result;
    }


}
