<?php

use Symfony\Component\PropertyAccess\PropertyAccess;

class JSONManager
{
    public static function getJsonData($path){
        if(!file_exists($path)){
            $fp = fopen($path, 'w');
            $dataStructure = [
                "spring-repo" => [
                    "token" => null,
                ],
                "authorkit" => [
                    "token" => null,
                ],
            ];
            fwrite($fp, json_encode($dataStructure, JSON_PRETTY_PRINT));
            fclose($fp);
        }
        $string = file_get_contents($path);
        $json_a = json_decode($string, true);
        return $json_a;
    }

    public static function saveJsonData($data, $path){
        $fp = fopen($path, 'w');
        fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
        fclose($fp);
    }

    public static function setKeyValue($key, $value, $path){
        $data = self::getJsonData($path);
        
        $accessor = PropertyAccess::createPropertyAccessor();
        $accessor->setValue($data, $key, $value);

        $fp = fopen($path, 'w');
        fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
        fclose($fp);
    }
}
