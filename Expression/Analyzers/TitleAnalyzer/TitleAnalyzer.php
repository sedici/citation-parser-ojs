<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
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


        /**
         * Analyzes the given reference to identify title patterns.
         * @param string $reference The reference to analyze.
         * @return array An array containing the expression type and matched values.
         */
        public function analyze(string $reference) {
            foreach ($this->patterns as $pattern => $name) {
                if (preg_match($pattern, $reference, $matches)) {
                    return array('expression' => $name, 'value' => $matches);
                }
            }

            return array('expression' => null, 'value' => '');
        }

    }