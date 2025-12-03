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

        /**
         * Analyzes the given reference text to identify URL patterns.
         * @param string $reference The reference text to analyze.
         * @return array An array containing the expression type and matched values.
         */
        public function analyze(string $reference): array {
            foreach ($this->patterns as $pattern => $name) {
                if (preg_match($pattern, $reference, $matches)) {
                    return array('expression' => $name, 'value' => $matches);
                }
            }

            return array('expression' => null, 'value' => '');
        }

    }