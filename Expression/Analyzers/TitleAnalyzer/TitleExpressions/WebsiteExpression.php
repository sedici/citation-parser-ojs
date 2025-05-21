<?php

include_once('GenericExpression.php');

    class WebsiteExpression extends GenericExpression{

        private static $websiteExpression = [
            // WEBPAGE PATTERN FOR EXAMPLE: Observatorio de la Seguridad Social. (s.f.). ANSES. Recuperado el 7 de marzo de 2024 de https://www.anses.gob.ar/observatorio/
            '/\)\.\s(?P<source>[A-Z][A-Za-zÀ-ÿ\s\:\,\;\-]+)\.\sRecuperado\sel\s(?P<date_in_citation>(?P<citation_day>\d{1,2})\sde\s(?P<citation_month>[a-z]+)\sde\s(?P<citation_year>\d{4}))/i' => 'webpage',

            //WEBPAGE PATTERN FOR EXAMPLE: Observatorio de la Seguridad Social. (s.f.). ANSES. Instituto Cultural de la Provincia de Buenos Aires. (s.f). Museos. Gobierno de la Provincia de Buenos Aires. https://www.gba.gob.ar/cultura/museos
            '/\(([^0-9]*)\)\.\s(?P<source>[A-Z][A-Za-zÀ-ÿ\s\:\,\;\-]+)\.\s(?P<article_title>[A-Z][A-Za-zÀ-ÿ\s\:\,\;\-]+)\.\s(https|http)?:\/\/[^\s]+/' => 'webpage',
        ];

        public static function getPattern(){
            return self::$websiteExpression;
        }

    }