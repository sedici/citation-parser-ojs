<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
include_once __DIR__ . '/../Expression.php';
include_once 'UrlAnalyzer.php';

class URLExpression extends Expression {
    
    public static function parse($text) {

        $analyzer = new UrlAnalyzer();
        return $analyzer->analyze($text);

    }
}