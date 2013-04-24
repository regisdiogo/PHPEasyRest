<?php
define('ROOT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR);

spl_autoload_register(function($className) {
    $namespaces = array('core', 'action');
    foreach ($namespaces as $namespace) {
        if (!$namespace && strpos($className, $namespace.'\\') !== 0) {
            continue;
        }
        $classFile = ROOT_PATH.$className.'.php';
        if (file_exists($classFile)) {
            require $classFile;
            return;
        }
    }
});
?>