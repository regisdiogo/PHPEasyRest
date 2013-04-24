<?php
namespace core;

if (!defined('ROOT_PATH')) die('Not allowed');

class Annotation {

    /* CONTROLS */
    const CLAZZ = 'class';
    const BEHAVIOR = 'behavior';
    const VALUES = 'values';

    /* OPTIONS */
    const ROUTE = 'route';
    const MAPPER = 'mapper';
    const METHOD = 'method';
    const TYPE = 'type';

    public static function extractAnnotations($className) {
        try {
            if (is_object($className)) {
                $className = get_class($className);
            }

            $listAnnotations = CachedAnnotation::getInstance()->getAnnotations($className);
            if ($listAnnotations) {
                return $listAnnotations;
            }

            $listAnnotations = array();
            $reflectionClass = new \ReflectionClass($className);

            // Class doc annotations
            if ($reflectionClass->getDocComment()) {
                $annotations = self::extractAnnotationsFromDocComment($reflectionClass->getDocComment());
                $listAnnotations[self::CLAZZ] = $annotations;
            }

            // Cache list of annotations
            CachedAnnotation::getInstance()->setAnnotations($className, $listAnnotations);

            return $listAnnotations;

        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    private static function extractAnnotationsFromDocComment($string) {
        $tempString = $string;
        while (true) {
            if (preg_match('/([\s]+|[*])[@](?P<type>[^\s(]*)/', $tempString, $matches, PREG_OFFSET_CAPTURE)) {
                $offset = $matches['type'][1] + 1;
                $tempString = substr($tempString, $offset);
                $listDocComments[] = $matches['type'][0];
            } else {
                break;
            }
        }
        $listAnnotation = array();
        if (isset($listDocComments)) {
            $tempString = $string;
            foreach ($listDocComments as $docComment) {
                $annotation = array();
                if (preg_match("/@".$docComment."\s*[(]\s*(?P<params>.+)[)]/", $tempString, $matches, PREG_OFFSET_CAPTURE)) {
                    $params = preg_split("/[,]/", $matches['params'][0]);
                    $offset = $matches['params'][1] + 1;
                    $tempString = substr($tempString, $offset);
                    $values = array();
                    foreach ($params as $param) {
                        if (preg_match('/(?<key>.*?)\s*=\s*"(?P<value>.+)"\s*/', $param, $paramsMatches)) {
                            $values[$paramsMatches['key']] = $paramsMatches['value'];
                        }
                    }
                    if (count($values)) {
                        $annotation = $values;
                    }
                }
                $listAnnotation[] = array(self::BEHAVIOR => strtolower($docComment), self::VALUES => $annotation);
            }
        }
        return $listAnnotation;
    }
}

class CachedAnnotation {

    private static $instance = null;
    private $annotations = array();

    private function __construct() {
    }

    private function __clone() {
    }

    /**
     * @return CachedAnnotation
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new CachedAnnotation();
        }
        return self::$instance;
    }

    public function getAnnotations($className) {
        if (key_exists($className, $this->annotations)) {
            return $this->annotations[$className];
        } else {
            return null;
        }
    }

    public function setAnnotations($className, $annotations) {
        $this->annotations[$className] = $annotations;
    }
}
?>