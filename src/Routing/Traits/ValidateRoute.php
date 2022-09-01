<?php
namespace Tuezy\Routing\Traits;

trait ValidateRoute{

    public static $whiteList = [];

    private function startWith($str, $keyword = '/'){
        return substr($str, 0, 1) == $keyword;
    }
    private function forceStartWith($str, $keyword = '/'){
        return $this->startWith($str, $keyword) ? $str : $keyword.$str;
    }

    private function whiteList($key){
        self::$whiteList[$key] = 1;
    }

}