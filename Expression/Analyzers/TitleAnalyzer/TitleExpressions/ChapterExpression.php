<?php

include_once('GenericExpression.php');

    class ChapterExpression extends GenericExpression{

        private static $chapterExpression = ['/(?P<title>[A-Z][A-Za-zÀ-ÿ\s\:,;]+\.)\sEn\s(?P<author>(?P<nombres>(\p{Lu}\.\s?)+)\s(?P<apellido>\p{L}+(\s\p{L}+)*) (?P<role>(\((Ed.|Coord.|Comp.)\)))?((?:,\s|y\s))?)+(?P<roles>(\((Eds.|Coords.|Comps.)\)))?(?P<book>[A-Z][A-Za-zÀ-ÿ\s]+)\s(\((?P<edicion>((?P<nedicion>[0-9]+ᵃ)\sed\.,\s)?(Vol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?(,\s)?(pp.\s(?P<paginas>(\d{1,4}-\d{1,4})))?)\))\.\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)(\s(?P<url>https?:\/\/[^\s]+))?$/' => 'chapter'];

        public static function getPattern(){
            return self::$chapterExpression;
        }

    }