<?php
namespace core;

if (!defined('ROOT_PATH')) die('Not allowed');

abstract class BaseAction {

    protected function makeResponseJson($value) {
        if (!isset($value)) {
            $value = array('success'=>'true');
        }
        header('content-type: application/json');
        return \core\helper\JsonHelper::convertToJson($value);
    }

}
?>