<?php

include_once('GenericExpression.php');

    class CongressExpression extends GenericExpression{

        private static $congressPattern = ['/\)\.\s(?P<title>.+?)\s\[(?P<comment>[^,\]]+)\]\.\s(?P<event>[A-Z][^\.]*)\.\s(?P<publishername>[A-Z][^\.!,]*\.)?\s?(?P<publisherloc>[A-Z][^\.]*)?/' => 'confproc'];

        public static function getPattern(){
            return self::$congressPattern;
        }

    }