<?php
include_once 'Expression.php';
class TitleExpression extends Expression{

    public static function parse($text) {
        $expressions = array(
            '/(?P<chapter>[A-Z][A-Za-zÀ-ÿ\s]+\.)\sEn\s(?P<author>(?P<nombres>(\p{Lu}\.\s?)+)\s(?P<apellido>\p{L}+(\s\p{L}+)*) (?P<role>(\((Ed.|Coord.|Comp.)\)))?((?:,\s|y\s))?)+(?P<roles>(\((Eds.|Coords.|Comps.)\)))?(?P<book>[A-Z][A-Za-zÀ-ÿ\s]+)\s(\((?P<edicion>((?P<nedicion>[0-9]+ᵃ)\sed\.,\s)?(Vol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?(,\s)?(pp.\s(?P<paginas>(\d{1,4}-\d{1,4})))?)\))\.\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)(\s(?P<url>https?:\/\/[^\s]+))?$/' => 'chapter',
            '/(?P<book>[A-Z][A-Za-zÀ-ÿ\s]+\.)(\s\((?P<edicion>(?P<nedicion>[0-9]+ª)\sed\.(,\sVol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?)\)\.)?\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)/' => 'book',
            '/(?P<journal>[A-Z][A-Za-zÀ-ÿ\s\:]+)\.\s(?P<revista>[A-Z][A-Za-zÀ-ÿ\s\:\.]+)\,\s(?P<nedicio>\d{1,3})\((?P<volumen>\d{1,3})\)\,\s(?P<paginas>(\d{1,4}–\d{1,4}))\./' => 'journal'
        );
    
        foreach ($expressions as $pattern => $name) {
            if (preg_match($pattern, $text, $matches)) {
                return array('expression' => $name, 'value' => $matches);
            }
        }
    
        return array('expression' => 'No match found', 'value' => '');
    }

}