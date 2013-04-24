<?php
namespace action;

if (!defined('ROOT_PATH')) die('Not allowed');

/**
 * @Route(mapper="/user/{id}",method="getItem",type="GET")
 */
class UserAction extends \core\BaseAction {

    public function getItem($args) {
        $user = new User();
        $user->setId($args['id']);
        $user->setName('This user name');
        $user->setEmail('user@test.com');
        return $this->makeResponseJson(array('user'=>$user));
    }

}

class User {
    private $id;
    private $name;
    private $email;
    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function getName() {
        return $this->name;
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function getEmail() {
        return $this->email;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
}
?>