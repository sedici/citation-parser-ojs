<?php
include_once __DIR__ . '/../Expression.php';
include_once 'AuthorAnalyzer.php';

class AuthorExpression extends Expression {
    
    public static function parse(string $text) {

        $analyzer = new AuthorAnalyzer();
        return $analyzer->analyze($text);

    }
}

