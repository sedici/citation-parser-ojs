<?php
include_once __DIR__ . '/../Expression.php';
include_once 'DateAnalyzer.php';

class DateExpression extends Expression {
    
    public static function parse($text) {
    
        $analyzer = new DateAnalyzer();
        return $analyzer->analyze($text);
    
    }
}

