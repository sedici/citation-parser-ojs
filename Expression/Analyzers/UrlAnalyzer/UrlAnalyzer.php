<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
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

            return array('expression' => null, 'value' => '');
        }

    }