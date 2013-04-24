<?php
namespace action;

if (!defined('ROOT_PATH')) die('Not allowed');

/**
 * @Route(mapper="/welcome",method="welcome",type="GET")
 * @Route(mapper="/welcome/{name}",method="showWelcomeWithName",type="GET")
 */
class WelcomeAction extends \core\BaseAction {

    public function welcome() {
        return $this->makeResponseJson('welcome');
    }

    public function showName($args) {
        return $this->makeResponseJson($args);
    }

}
?>