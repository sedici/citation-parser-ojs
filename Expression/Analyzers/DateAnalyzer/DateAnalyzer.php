<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

include_once ('DatePatterns.php');

    class DateAnalyzer {

        private $patterns;

        public function __construct (){
            $this->patterns = DatePatterns::getPatterns();
        }

        /**
         * Analyzes a text to identify date expressions.
         * @param string $text The text to analyze.
         * @return array An associative array with 'expression' and 'value' keys.
         *
         * if $name is 'periodday' or 'periodmonth', the expression is set to 'congress' because these represent date ranges
         * (they are uniquely used for congress dates).
         */
        public function analyze(string $text){
            foreach ($this->patterns as $pattern => $name){
                if (preg_match($pattern, $text, $matches)) {
                    if ($name === 'periodday' || $name === 'periodmonth') {
                        return array('expression' => 'congress', 'value' => $matches);
                    } else {
                        return array('expression' => $name, 'value' => $matches);
                    }
                }
            }
            return (array('expression' => null, 'value' => ''));
        }
    }