<?php

namespace Example;

/**
 * Created by PhpStorm.
 * User: CIR
 * Date: 8/5/15
 * Time: 10:12
 */
use DateTime;

// define database info
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_USERNAME', 'homestead');
DEFINE ('DB_PASSWORD', 'secret');
DEFINE ('DB_DBNAME', 'blog');

class DBManager
{
    private static $instnce;
    private static $dbc;

    private function __construct(){
        // connect to mysql
        self::$dbc = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DBNAME)
        or die ('could not connect to mysql'.mysqli_connect_error());
        mysqli_set_charset(self::$dbc, 'utf-8');
    }

    // get the sington instance
    public static function getInstance(){
        if(!self::$instnce){
            self::$instnce = new self();
        }
        return self::$instnce;
    }

    // insert picture
    public function insertPic($url, DateTime $dateTime){
        $table_name = 'pictures';
        $attr1 = 'url';
        $attr2 = 'created_at';
        $url = "'".$url."'";
        $date = "'".$dateTime->format("Y-m-d H:i:s")."'";

        $insert_str = "INSERT INTO $table_name ($attr1, $attr2) values ($url, $date)";
        mysqli_query(self::$dbc, $insert_str);
    }

    // close the db
    public function closeDB(){
        self::$instnce = null;
        mysqli_close(self::$dbc);
    }
}
