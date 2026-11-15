<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

include_once __DIR__ . '/../Expression.php';
include_once 'AuthorAnalyzer.php';

class AuthorExpression extends Expression {
    
    public static function parse(string $text) {

        $analyzer = new AuthorAnalyzer();
        return $analyzer->analyze($text);

    }
}