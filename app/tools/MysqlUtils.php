<?php
/**
 * Created by PhpStorm.
 * User: tusion
 * Date: 14-12-12
 * Time: 上午7:27
 */

    //require_once('../Config.php');

/**
 * @param string $host
 * @param string $user
 * @param string $password
 * @param int $port
 * @return mysqli
 */
function connect_mysql_host( $host="locahost", $user="root", $password="root", $port=3306){
    $connection = new mysqli($host, $user, $password, $port);
    if ($connection->error) {
        die("Mysql connect failed: ".$connection->error );
    }else {
        return $connection;
    }

}

/**
 * @param $connection
 * @param $db_name
 * @return bool
 */
function create_mysql_db($connection, $db_name){
    $sql = "CREATE DATABASE $db_name IF NOT EXIST";
    if ( $connection->query($sql) === TRUE)
    {
        return TRUE;
    }
    else
    {
        echo"creat mysql database error: ".$connection->error;
    }
}

function select_mysql_db( $connection, $db_name){
    $db_selected = mysql_select_db( $db_name, $connection);
}
?>