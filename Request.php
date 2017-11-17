<?php

class Request {

    const TYPE_GET=0;
    const TYPE_POST=1;
    const TYPE_COOKIE=2;
    const TYPE_PUT=3;
    const TYPE_DELETE=4;
    const TYPE_SESSION=5;
    const TYPE_UNDEFINED=-1;

    public static function getGET($key=null, $default=""){
        return self::get($key, $default, self::TYPE_GET);
    }

    public static function setGET($key, $value){
        self::set($key, $value, self::TYPE_GET);
    }

    public static function getPOST($key=null, $default=""){
        return self::get($key, $default, self::TYPE_POST);
    }

    public static function setPOST($key, $value){
        self::set($key, $value, self::TYPE_POST);
    }

    public static function getCookie($key=null, $default=""){
        return self::get($key, $default, self::TYPE_COOKIE);
    }

    public static function getSession($key=null, $default=""){
        return self::get($key, $default, self::TYPE_SESSION);
    }

    public static function setSession($key, $value){
        self::set($key, $value, self::TYPE_SESSION);
    }

    public static function getVarValue(&$var, $key, &$flag){
        if($key === null){
            $flag=1;
            return $var;
        }
        return isset($var[$key]) ? $var[$key] : null;
    }

    public static function setVarValue(&$var, $key, $value, &$flag){
        if($key === null){
            $var=$value;
            $flag=1;
        }else $var[$key]=$value;
    }

    public static function get($key=null, $default="", $type=self::TYPE_UNDEFINED){
        $ret=null;
        $flag=0;
        switch($type){
            case self::TYPE_GET:
                $ret=self::getVarValue($_GET, $key, $flag);
                break;
            case self::TYPE_POST:
                $ret=self::getVarValue($_POST, $key, $flag);
                break;
            case self::TYPE_COOKIE:
                $ret=self::getVarValue($_COOKIE, $key, $flag);
                break;
            case self::TYPE_SESSION:
                $ret=self::getVarValue($_SESSION, $key, $flag);
                break;
            case self::TYPE_UNDEFINED:
                $global_vars=array("GET" => &$_GET, "POST" => &$_POST, "COOKIE" => &$_COOKIE, "SESSION" => &$_SESSION);
                if($key !== null){
                    foreach($global_vars as $var){
                        $value=self::getVarValue($var, $key, $flag);
                        if($value !== null){
                            $ret=$value;
                            break;
                        }
                    }
                }else{
                    $flag=1;
                    $ret=$global_vars;
                }
                break;
        }
        if($flag === 1) return $ret;
        return $ret !== null ? get_typecasted_var($ret, $default) : $default;
    }

    public static function set($key, $value, $type=self::TYPE_UNDEFINED){
        if($key === null) return false;
        $flag=0;
        switch($type){
            case self::TYPE_GET:
                self::setVarValue($_GET, $key, $value, $flag);
                break;
            case self::TYPE_POST:
                self::setVarValue($_POST, $key, $value, $flag);
                break;
            case self::TYPE_COOKIE:
                self::setVarValue($_COOKIE, $key, $value, $flag);
                break;
            case self::TYPE_SESSION:
                self::setVarValue($_SESSION, $key, $value, $flag);
                break;
            case self::TYPE_UNDEFINED:
                $global_vars=array(&$_GET, &$_POST, &$_COOKIE, &$_SESSION);
                foreach($global_vars as &$var) self::setVarValue($var, $key, $value, $flag);
                break;
        }
        return true;
    }

}