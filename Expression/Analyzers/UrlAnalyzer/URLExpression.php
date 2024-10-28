<?php

include_once __DIR__ . '/../Expression.php';
include_once 'UrlAnalyzer.php';

class URLExpression extends Expression {
    
    public static function parse($text) {

        $analyzer = new UrlAnalyzer();
        return $analyzer->analyze($text);

    }
}