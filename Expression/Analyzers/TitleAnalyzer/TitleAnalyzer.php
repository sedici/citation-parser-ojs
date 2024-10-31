<?php

    include_once('TitlePatterns.php');
    
    class TitleAnalyzer{

        private $patterns;

        public function __construct(array $types = []) { 
            if (empty($types)) {
                $this->patterns = TitlePatterns::getAllPatterns();
            } else {
                $this->patterns = [];
                foreach ($types as $type) {
                    $patternGetter = 'get'.ucfirst($type).'Patterns';
                    $this->patterns = array_merge($this->patterns, TitlePatterns::$patternGetter());
                }
            }
        }



        public function analyze(string $text) {
            foreach ($this->patterns as $pattern => $name) {
                if (preg_match($pattern, $text, $matches)) { 
                    return array('expression' => $name, 'value' => $matches);
                }
            }

            return array('expression' => null, 'value' => '');
        }

    }