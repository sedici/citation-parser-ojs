<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

class LawExpression{

    
    private static $lawExpression = [
        '/\)\.\s(?P<source>[A-Z][A-Za-zÀ-ÿ\s:,\;\-]+)\.\s(?P<article_title>[A-Z][A-Za-zÀ-ÿ\s:,\;\-]+.*?B\.O\..*?\.)/' => 'webpage'
    ];

    /**
     * Get the pattern for law expressions.
     *
     * @return array The array of regex patterns for law expressions.
     */
    public static function getPattern()
    {
        return self::$lawExpression;
    }
}