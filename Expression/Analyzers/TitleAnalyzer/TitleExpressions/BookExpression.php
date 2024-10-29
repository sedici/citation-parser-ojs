<?php

include_once('GenericExpression.php');

    class BookExpression extends GenericExpression{

        private static  $bookExpression = ['/(?P<title>[A-Z0-9][0-9A-Za-zÀ-ÿ\s\:\,\;\-]+\.)(\s\((?P<edicion>(?P<nedicion>[0-9]+ª)\sed\.(,\sVol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?)\)\.)?\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s\:\,\;\-]+\.)/' => 'book'] ;

        public static function getPattern(){
            return self::$bookExpression;
        }

    }