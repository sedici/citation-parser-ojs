<?php

include_once('GenericExpression.php');

    class CongressExpression extends GenericExpression{

        private static $congressPattern = ['/\)\.\s(?P<title>.+?)\s\[(?P<comment>[^,\]]+)\](?:(?:\.\s(?P<evento>[A-Z][^\.]*))?(?:\.\s(?P<publisher>(Universidad|Editorial|Press|Ediciones)[^\.]*))?(?:\.\s(?P<publishername>[A-Z][^\.]*))?)?\.??/' => 'congress'];

        public static function getPattern(){
            return self::$congressPattern;
        }

    }