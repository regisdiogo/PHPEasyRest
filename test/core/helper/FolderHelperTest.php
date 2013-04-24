<?php
use core\helper\FolderHelper;

class FolderHelperTest extends \PHPUnit_Framework_TestCase {

    public function testEmpty() {
        $files = FolderHelper::listFilesByType(ROOT_PATH, '.php');
        $this->assertNotNull($files, 'Cannon be null');
        $this->assertEquals(1, count($files), 'Has to be equal to 1');
        return $files;
    }

    /**
     * @depends testEmpty
     */
    public function testFileExists(array $files) {
        foreach ($files as $file) {
            $this->assertFileExists(ROOT_PATH.DIRECTORY_SEPARATOR.$file, 'File must exists');
        }
    }
}
?>
