<?php
namespace action;
if (!defined('ROOT_PATH')) die('Not allowed');
/**
 * @Route(mapper="/helloworld",method="showMessage",type="POST")
 */
class HelloWorldAction extends \core\BaseAction {

    public function showMessage($args) {
        $hello = 'This was your message: "'.$args['your-message'].'".';
        return $this->makeResponseJson(array('message'=>$hello));
    }

}
?>