<?php

    include_once('UrlPatterns.php');

    class UrlAnalyzer{

        private $patterns;

        public function __construct() { 
            $this->patterns = UrlPatterns::getPatterns();
        }

        public function analyze(string $text) {
            foreach ($this->patterns as $pattern => $name) {
                if (preg_match($pattern, $text, $matches)) {
                    return array('expression' => $name, 'value' => $matches);
                }
            }

            return array('expression' => 'No match found', 'value' => '');
        }

    }