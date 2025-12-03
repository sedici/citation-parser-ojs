<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

include_once('GenericExpression.php');

    class ThesisExpression extends GenericExpression{

        private static $thesisExpression = ['/(?P<title>[A-Z][0-9A-Za-zÀ-ÿ\s:,;()]+)\s\[(?P<comment>[^,]*,\s(?P<publishername>[^]]+))\]./' => 'thesis'];

        /**
         * Returns the regex pattern for thesis expressions.
         *
         * @return array The regex pattern for thesis expressions.
         */
        public static function getPattern(){
            return self::$thesisExpression;
        }
        
    }