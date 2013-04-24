<?php
class JsonHelperTest extends \PHPUnit_Framework_TestCase {

    public function testArrayConversion() {
        $array = $this->createArray();
        $this->assertArrayHasKey('a', $array);
    }

    public function createArray() {
        return array(
                'a'=>'Yellow'
        );
    }

}
?>