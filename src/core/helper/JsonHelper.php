<?php
namespace core\helper;

if (!defined('ROOT_PATH')) die('Not allowed');

class JsonHelper {

    public static function convertToJson($value) {
        return json_encode(self::convert($value));
    }

    private static function convert($value) {
        $return = array();
        if (is_array($value)) {
            foreach ($value as $key=>$item) {
                $return[$key] = self::convert($item);
            }
        } else if (is_object($value)) {
            $return = self::convertObjectToStdClass($value);
            if (is_object($return)) {
                $return = get_object_vars($return);
            }
        } else {
            $return = $value;
        }
        return $return;
    }

    private static function convertObjectToStdClass($object, $key = null) {
        if (is_array($object)) {
            $array = array();
            foreach ($object as $key=>$value) {
                $array[] = self::convertObjectToStdClass($value, $key);
            }
            return $array;
        }
        $stdObject = new \stdClass();
        if (!is_object($object)) {
            $stdObject->$key = $object;
        } else {
            $rc = new \ReflectionClass($object);
            $methods = $rc->getMethods(\ReflectionMethod::IS_PUBLIC);
            if ($methods) {
                foreach ($methods as $method) {
                    if (strpos($method->name, 'get') !== false) {
                        $rm = new \ReflectionMethod($object, $method->name);
                        $name = strtolower(substr($method->name, 3));
                        $value = $rm->invoke($object);
                        if (is_object($value) || is_array($value)) {
                            $stdObject->$name = self::convertObjectToStdClass($value);
                        } else {
                            $stdObject->$name = $value;
                        }
                    }
                }
            }
        }
        return $stdObject;
    }
}
?>