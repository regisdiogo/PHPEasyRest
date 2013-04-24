<?php
namespace core\helper;

if (!defined('ROOT_PATH')) die('Not allowed');

class StringHelper {

    public static function endsWith($haystack, $needle) {
        $StrLen = strlen($needle);
        $tempHaystack = substr($haystack, strlen($haystack) - $StrLen);
        return $tempHaystack == $needle;
    }

}
?>