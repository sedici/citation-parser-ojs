<?php

include_once('GenericExpression.php');

    class ThesisExpression extends GenericExpression{

        private static $thesisExpression = ['/(?P<title>[A-Z][0-9A-Za-zÀ-ÿ\s:,;()]+)\s\[(?P<comment>[^,]*,\s(?P<publishername>[^]]+))\]./' => 'thesis'];

        public static function getPattern(){
            return self::$thesisExpression;
        }

    }