<?php
/**
 * Created by PhpStorm.
 * User: tusion
 * Date: 15-1-28
 * Time: 下午4:56
 */



class Subway {
    //Global  $debug;
    private $db_connect ;
    private $db_config = array();

    function __construct( $db_server='localhost',
                          $db_user='root',
                          $db_password='root',
                          $db_name= 'subway_db' )
    {
        $this->db_config['server'] = $db_server;
        $this->db_config['user'] = $db_user;
        $this->db_config['password'] = $db_password;
        $this->db_config['db'] = $db_name;

        require_once('../Config.php');
        if( $debug){
            echo( 'subway construct \n');
        }

    }

    function  __destruct()
    {
        //release database connection
        if( $this->db_connect )
        {
            mysql_close( $this->db_connect );
        }
        unset( $this->db_config);

    }


    function connect()
    {
        if( !isset($this->mysql_connect))
        {
            $this->db_connect = mysql_connect(
                    $this->db_config['server'],
                    $this->db_config['user'],
                    $this->db_config['password']);
            if( !$this->db_connect )
            {
                die('Could not connect: '.mysql_error());
            }
        }
    }


    function createDataBase(){
        //CREATE DATABASE `test' CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';
        $create_db_sql = "CREATE DATABASE ".$this->db_config['db']." IF NOT EXIST
        CHARACTER SET'utf8' COLLATE 'utf8_general_ci";

        if( $debug){
            echo( $create_db_sql.'\n');
        }
        if ( mysql_query($create_db_sql, $this->db_connect ) )
        {
            echo 'database: '.$this->db_config['db'].'is already setup';
        }else{
            echo 'database: '.$this->db_config['db'].'is not exist, set up now.';
        }
    }







    function createTable( ){
        $subway_line_sql = " CREATE TABLE  subway_line_tb(
        id INT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,

        name VARCHAR(20) NOT NULL COMMENT='地铁名称,同一线路会有多条方向相反的名称',
        ployline TEXT NOT NULL COMMENT='线路上的关键点,保证两点之间连线是直线,组合起来拟合路线',
        start_stop INT(3) UNSIGNED COMMENT='起点站编号,对应表staion_tb的id列',
        end_stop INT(3)  UNSIGEND COMMENT='终点站编号,对应表staion_tb的id列',
        company VARCHAR(20) NOT NULL,
        distance DECIMAL(3.5) UNSIGNED NOT NULL,
        basic_price DECIMAL(3.2) UNSIGEND NOT NULL,
        total_price DECIMAL(3.2) UNSIGEND NOT NULL,
        bounds VARCHAR(40) NOT NULL COMMENT='路线形状的范围对应东北角和西南角',
        busstops_squence VARCHAR(200) NOT NULL COMMENT='途径站点. 顺序编号:站点编号, 站点坐标一定包含在ployline中',
        subway_id VARCHAR(10) NOT NULL COMMENT='高德的公交线路编号id',
        update_time TIMESTAMP  NOT NULL
        )ENGINE=InnoDB DEFAULT CHARSET=utf8";


        $station_tb_sql = " CREATE TABLE  station_tb(
        id INT(5) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        name VARCHAR(20) NOT NULL,
        location_lat DECIMAL(3.6) UNSIGNED NOT NULL,
        location_lng DECIMAL (3.6) UNSIGNED NOT NULL,
        busstop_id VARCHAR(10) NOT NULL COMMENT='高德的公交站唯一编号',
        transation VARCHAR(400) COMMENT='此站点和其它站点的连通性. 站点编号:距离',
        update_time TIMESTAMP  NOT NULL
        )ENGINE=InnoDB DEFAULT CHARSET=utf8";


        $search_tb_sql=" CREATE TABLE  search_tb(
        id INT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        KEY VARCHAR(20) NOT NULL COMMENT='起点站高德ID_终点站高德ID',
        cost DECIMAL(3.2) UNSIGEND NOT NULL,
        ployline TEXT NOT NULL COMMENT='线路上的关键点,保证两点之间连线是直线,组合起来拟合路线',
        bounds VARCHAR(40) NOT NULL COMMENT='路线形状的范围对应东北角和西南角',
        busstops_squence VARCHAR(200) NOT NULL COMMENT='途径站点. 顺序编号:站点编号, 站点坐标一定包含在ployline中',
        update_time TIMESTAMP  NOT NULL
        )ENGINE=InnoDB DEFAULT CHARSET=utf8";

    }



    function debug()
    {

    }






}