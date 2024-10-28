<?php
include_once __DIR__ . '/../Expression.php';
include_once 'TitleAnalyzer.php';

class TitleExpression extends Expression{

    public static function parse($text, array $types = []) {

        $analyzer = new TitleAnalyzer($types);
        return $analyzer->analyze($text, $types);
    }

}