<?php
namespace Youche\SimpleRecommend;
//use Youche\SimpleRecommend\AMapAPI;
//use Youche\SimpleRecommend\MapUtils;

/**
 * Created by PhpStorm.
 * User: tusion
 * Date: 15-1-28
 * Time: 下午4:56
 */

abstract class BusBasic {
    private $db_connect ;
    private $db_config = array();
    private $debug;

    function __construct( $db_server='localhost',
                          $db_user='root',
                          $db_password='root',
                          $db_name= 'bus_line_db',
                          $debug=false)
    {
        $this->db_config['server'] = $db_server;
        $this->db_config['user'] = $db_user;
        $this->db_config['password'] = $db_password;
        $this->db_config['db'] = $db_name;
        $this->debug = $debug;

        if( $this->debug){
            echo( "BusBasic construct \n");
        }


    }

    function  __destruct()
    {
        //release database connection
        if( $this->db_connect )
        {
            mysql_close( $this->db_connect );

            if( $this->debug){
                echo( 'close database '.$this->db_config['db']."\n");
            }
        }
        unset( $this->db_config);

    }

    /**
     * 打开调试信息
     * @param $debug
     */
    function setDebug( $debug )
    {
        $this->debug = $debug;
        if($this->debug)
        {
            echo"Success to enable debug model \n";
        }
    }

    /**
     * 链接数据库
     */
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
            if( $this->debug){
                echo( "success to connect mysql \n");
            }
        }
    }

    /**
     * 建立数据库,如果存在就跳过
     */
    function createDataBase()
    {
        //CREATE DATABASE `test' CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';
        $create_db_sql = "CREATE DATABASE " . $this->db_config['db'] .
        " CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' ";

        if ($this->debug) {
            echo($create_db_sql . "\n");
        }


        if (mysql_query($create_db_sql, $this->db_connect)) {
            echo 'database: ' . $this->db_config['db'] . " is not exist, set up now.\n";
        } else {
            echo 'database: ' . $this->db_config['db'] . " is already setup.\n";
        }

        $select_db_sql = "USE " . $this->db_config['db'];
        if ($this->debug) {
            echo($select_db_sql . "\n");
        }
        if (mysql_query($select_db_sql, $this->db_connect)) {
            echo 'success select database: ' . $this->db_config['db']."\n";
        } else {
            echo 'failed to select database: ' . $this->db_config['db']."\n";
        }

        //解决中文乱码问题
        mysql_query("set names UTF8", $this->db_connect);

    }


    /**
     * 建立四张基础数据表,出错会抛出异常
     * @throws \Exception
     */
    function createTable( ){

        $subway_line_sql = "CREATE TABLE IF NOT EXISTS bus_line_tb(
        id            INT(4) UNSIGNED  NOT NULL AUTO_INCREMENT,
        line_name     VARCHAR(200) NOT NULL COMMENT '地铁名称,同一线路会有多条方向相反的名称',
        line_type     VARCHAR(100) NOT NULL,
        start_stop    VARCHAR(100) NOT NULL COMMENT '起点站中文名字',
        end_stop      VARCHAR(100) NOT NULL COMMENT '终点站中文名字',
        company       VARCHAR(20) NOT NULL,
        distance      DECIMAL(8,5) UNSIGNED NOT NULL,
        basic_price   DECIMAL(5,2) UNSIGNED NOT NULL,
        total_price   DECIMAL(5,2) UNSIGNED NOT NULL,
        bounds        VARCHAR(40) NOT NULL COMMENT '路线形状的范围对应东北角和西南角',
        busstops      VARCHAR(4000) NOT NULL COMMENT '途径站点. 顺序编号:站点编号. 站点坐标一定包含在ployline中',
        bus_line_id   VARCHAR(10) NOT NULL COMMENT '高德的公交线路编号id',
        polyline      VARCHAR(65530) NOT NULL COMMENT '线路上的关键点,保证两点之间连线是直线,组合起来拟合路线',
        update_time   TIMESTAMP  NOT NULL,
        PRIMARY KEY(id)
        )ENGINE=InnoDB DEFAULT CHARSET=utf8 ";

        if( mysql_query($subway_line_sql, $this->db_connect ))
        {
            if( $this->debug)
            {
                echo( "success create table: subway_line_tb \n");
            }
        }
        else
        {
            throw new \Exception("Error: could not create table, sql:\n".$subway_line_sql );
        }


        $station_tb_sql = " CREATE TABLE IF NOT EXISTS station_tb(
        id INT(4) UNSIGNED  NOT NULL AUTO_INCREMENT,
        station_name VARCHAR(100) NOT NULL COMMENT '站点中文名字',
        location_lat DECIMAL(9,6) UNSIGNED NOT NULL,
        location_lng DECIMAL (9,6) UNSIGNED NOT NULL,
        busstop_id VARCHAR(10) NOT NULL COMMENT '高德的公交站唯一编号',
        line_name     VARCHAR(200) NOT NULL COMMENT '地铁名称,同一线路会有多条方向相反的名称',
        transation VARCHAR(400) COMMENT '此站点和其它站点的连通性. 站点编号:距离',
        update_time TIMESTAMP  NOT NULL,
        PRIMARY KEY( id )
        )ENGINE=InnoDB DEFAULT CHARSET=utf8";

        if( mysql_query($station_tb_sql, $this->db_connect ))
        {
            if( $this->debug)
            {
                echo( "success create table: station_tb \n");
            }
        }
        else
        {
            throw new \Exception("Error: could not create table, sql:\n".$station_tb_sql );
        }

        // hash_key        CHAR(32) NOT NULL COMMENT 'hash(起点公交站高德ID_终点公交站高德ID),车站间的链接是双向的',
        //站于站链接是双向的
        $connection_tb_sql = "CREATE TABLE IF NOT EXISTS connection_tb(
        id              INT(9) UNSIGNED NOT NULL AUTO_INCREMENT,
        start_name      VARCHAR(100) NOT NULL COMMENT '站点中文名字',
        end_name         VARCHAR(100) NOT NULL COMMENT '站点中文名字',
        start_stop_id   VARCHAR(10) NOT NULL COMMENT '起点公交站高德ID',
        end_stop_id     VARCHAR(10) NOT NULL COMMENT '终点公交站高德ID',
        distance        INT(10) UNSIGNED NOT NULL COMMENT '两个站之间的真实距离,单位:m',
        hash_key        CHAR(32) NOT NULL COMMENT 'hash(sub_polyline),车站间的链接是双向的',
        start_stop      VARCHAR(20) NOT NULL,
        end_stop        VARCHAR(20) NOT NULL,
        line_name     VARCHAR(200) NOT NULL COMMENT '地铁名称,同一线路会有多条方向相反的名称',
        sub_polyline    VARCHAR(10000) NOT NULL COMMENT '两个站点间线路上的关键点,保证两点之间连线是直线,组合起来拟合路线',
        update_time     TIMESTAMP  NOT NULL,
        PRIMARY KEY( id)
        )ENGINE=InnoDB DEFAULT CHARSET=utf8";

        if( mysql_query($connection_tb_sql, $this->db_connect ))
        {
            if( $this->debug)
            {
                echo( "success create table: connection_tb \n");
            }
        }
        else
        {
            throw new \Exception("Error: could not create table, sql:\n".$connection_tb_sql );
        }


        $search_tb_sql=" CREATE TABLE  IF NOT EXISTS search_tb(
        id INT(6) UNSIGNED NOT NULL AUTO_INCREMENT,
        search_key VARCHAR(20) NOT NULL COMMENT '起点站高德ID_终点站高德ID',
        distance DECIMAL(8,5) UNSIGNED NOT NULL,
        cost DECIMAL(5,2) UNSIGNED NOT NULL,
        ployline TEXT NOT NULL COMMENT '线路上的关键点,保证两点之间连线是直线,组合起来拟合路线',
        sub_line VARCHAR(100)  COMMENT '线路换乘说明',
        bounds VARCHAR(40) NOT NULL COMMENT '路线形状的范围对应东北角和西南角',
        busstops_squence VARCHAR(200) NOT NULL COMMENT '途径站点. 顺序编号:站点编号, 站点坐标一定包含在ployline中',
        update_time TIMESTAMP  NOT NULL,
        PRIMARY KEY( id)
        )ENGINE=InnoDB DEFAULT CHARSET=utf8";

        if( mysql_query($search_tb_sql, $this->db_connect ))
        {
            if( $this->debug)
            {
                echo( "success create table: search_tb \n");
            }
        }
        else
        {
            throw new \Exception("Error: could not create table, sql:\n".$search_tb_sql );
        }

    }

    function executeSql( $sql )
    {
        if( mysql_query($sql, $this->db_connect ) )
        {
            if( $this->debug)
            {
               // echo( "success execute sql: \n $sql \n");
            }
        }
        else
        {
            throw new \Exception("Error: sql:\n $sql \n" );
        }
    }


    function querySql( $sql)
    {
        if( \strlen($sql) > 1 )
        {
            $result = \mysql_query( $sql, $this->db_connect );
            if( $this->debug)
            {
                // echo( "success execute sql: \n $sql \n");
            }
            return $result;
        }


    }

    /*
    function InsertBusLineData( $json_array )
    {
        \var_dump( $json_array);
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
    */

    abstract function insertDataToDB( $json_array );






}