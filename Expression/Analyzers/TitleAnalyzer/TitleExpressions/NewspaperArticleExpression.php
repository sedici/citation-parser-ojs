<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

include_once('GenericExpression.php');

class NewspaperArticleExpression extends GenericExpression
{
    private static $newspaperArticleExpression = [
        '/\).\s(?P<source>[A-Z][A-Za-zÀ-ÿ\s\:\;\-]+(,\sp.(\s)?\d+)?)(.\s(https|http)?:\/\/[^\s]+)/' => 'webpage',
    ];

    /**
     * Get the pattern for newspaper article expressions.
     *
     * @return array The array of regex patterns for newspaper article expressions.
     */
    public static function getPattern()
    {
        return self::$newspaperArticleExpression;
    }
}