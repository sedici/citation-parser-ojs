<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

include_once('GenericExpression.php');

    class JournalExpression extends GenericExpression{

        private static $journalExpression = ['/(?P<title>[A-Z][A-Za-zÀ-ÿ\s\:\,\;\-]+)\.\s(?P<revista>[A-Z][A-Za-zÀ-ÿ\s\:\.]+)\,\s(?P<nedicio>([IVXLCDM]+|\d{1,3}))?(\((?P<volumen>\d{1,3})\))?(\,\s(?P<paginas>((?P<fpage>\d{1,4})(\–|\-)(?P<lpage>\d{1,4}))))?\./' => 'journal'];

        public static function getPattern(){
            return self::$journalExpression;
        }

    }