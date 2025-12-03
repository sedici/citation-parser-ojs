<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

include_once('GenericExpression.php');

    /**
     * CongressExpression class
     * 
     * This class defines the regular expression patterns to identify congress proceedings titles in references.
     */
    class CongressExpression extends GenericExpression{

        private static $congressPattern = ['/\)\.\s(?P<title>.+?)\s\[(?P<comment>[^,\]]+)\]\.\s(?P<event>[A-Z][^\.]*)\.\s(?P<publishername>[A-Z][^\.!,]*\.)?\s?(?P<publisherloc>[A-Z][^\.]*)?/' => 'confproc'];

        public static function getPattern(){
            return self::$congressPattern;
        }

    }