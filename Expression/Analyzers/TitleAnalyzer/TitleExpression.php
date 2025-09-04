<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

include_once __DIR__ . '/../Expression.php';
include_once 'TitleAnalyzer.php';

class TitleExpression extends Expression{

    public static function parse($text, array $types = []) {

        $analyzer = new TitleAnalyzer($types);
        return $analyzer->analyze($text);
    }

}