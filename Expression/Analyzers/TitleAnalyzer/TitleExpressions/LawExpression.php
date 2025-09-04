<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

class LawExpression{
    private static $lawExpression = [
        '/\)\.\s(?P<source>[A-Z][A-Za-zÀ-ÿ\s:,\;\-]+)\.\s(?P<article_title>[A-Z][A-Za-zÀ-ÿ\s:,\;\-]+.*?B\.O\..*?\.)/' => 'webpage'
    ];

    public static function getPattern()
    {
        return self::$lawExpression;
    }
}