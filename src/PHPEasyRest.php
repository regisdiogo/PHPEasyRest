<?php
if (!defined('ROOT_PATH')) die('Not allowed');

class PHPEasyRest {

    private static $instance = null;

    private function __construct() {
        spl_autoload_register(array($this, 'autoloadClass'));
    }

    private function __clone() {
    }

    /**
     * @return PHPEasyRest
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new PHPEasyRest();
        }
        return self::$instance;
    }

    public function init() {
        try {
            $actionClasses = \core\helper\FolderHelper::listFilesByType(ACTION_PATH, '.php', true);
            $mappingFound = false;
            $listRequestURI = $this->getListRequestURI();
            $args = array();

            if ($_SERVER['QUERY_STRING']) {
                $arrayQueryString = preg_split('/\&/', $_SERVER['QUERY_STRING']);
                foreach ($arrayQueryString as $partQueryString) {
                    $aux = preg_split('/\=/', $partQueryString);
                    if (count($aux) > 1) {
                        $args[$aux[0]] = $aux[1];
                    }
                }
            }

            if (isset($_POST) && count($_POST) > 0) {
                foreach ($_POST as $key=>$value) {
                    $args[$key] = $value;
                }
            }

            foreach ($listRequestURI as $requestURI) {
                $mappingFound = $this->findMapping($actionClasses, $requestURI, $args);
                if ($mappingFound) break;
            }
        } catch (\Exception $e) {
            if ($e->getMessage() == '501') {
                header('Not implemented', null, 501);
            } else if ($e->getMessage() == '404') {
                header('Not Found', null, 404);
            }
        }
    }

    private function findMapping($actionClasses, $requestURI, $args) {
        foreach ($actionClasses as $className) {
            $listAnnotation = \core\Annotation::extractAnnotations('action'.DIRECTORY_SEPARATOR.$className);
            if (isset($listAnnotation[\core\Annotation::CLAZZ])) {
                foreach ($listAnnotation[\core\Annotation::CLAZZ] as $annotation) {
                    if ($annotation[\core\Annotation::BEHAVIOR] == \core\Annotation::ROUTE) {
                        $var = addcslashes($annotation[\core\Annotation::VALUES][\core\Annotation::MAPPER], '/.');
                        $var = '/^'.$var.'$/';
                        $var = str_replace('?', '\?', $var);
                        $var = str_replace('{', '(?\'p', $var);
                        $var = str_replace('}', '\'([^\/])+?)', $var);
                        if (preg_match($var, $requestURI, $matches, PREG_OFFSET_CAPTURE)) {
                            $reflectionClass = new \ReflectionClass('action\\'.$className);
                            $classInstance = $reflectionClass->newInstance();
                            $methodName = $annotation[\core\Annotation::VALUES][\core\Annotation::METHOD];
                            if ($annotation[\core\Annotation::VALUES][\core\Annotation::TYPE] != $_SERVER['REQUEST_METHOD']) {
                                throw new \Exception('501');
                            }
                            if (method_exists($classInstance, $methodName)) {
                                foreach ($matches as $key=>$value) {
                                    if (strstr($key, 'p')) {
                                        $args[substr($key, 1, strlen($key))] = $value[0];
                                    }
                                }
                                $reflectionMethod = new \ReflectionMethod($classInstance, $methodName);
                                echo $reflectionMethod->invoke($classInstance, $args);
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    private function getListRequestURI() {
        $basePrefix = preg_replace('/\/app.php$/', '', $_SERVER['SCRIPT_NAME']);
        $listRequestURI = array();

        $listRequestURI[] = substr($_SERVER['REQUEST_URI'], strlen($basePrefix), strlen($_SERVER['REQUEST_URI']));
        if (strpos($_SERVER['REQUEST_URI'], '?')) {
            $urlArray = preg_split('/\?/', $_SERVER['REQUEST_URI']);
            $listRequestURI[] = substr($urlArray[0], strlen($basePrefix), strlen($urlArray[0]));
        }
        if (preg_match('/(.)*\/(\w)+$/', $_SERVER['REQUEST_URI'])) {
            $listRequestURI[] = substr($_SERVER['REQUEST_URI'].'/', strlen($basePrefix), strlen($_SERVER['REQUEST_URI'].'/'));
        }

        return $listRequestURI;
    }

    private function autoloadClass($className) {
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
    }
}

/**
 * Print preformated strings or objects
 * @param $object
 */
function p($object, $title = '') {
    echo "<div align='left' style='font-size:11px;border:1px solid gray;background-color:#FFF;'><pre>";
    echo "<div style='font-size:12px;'>$title</div>";
    if (!isset($object))
        print_r('null');
    else
        print_r($object);
    echo "</pre></div>";
}
?>