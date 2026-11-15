<?php

/* 
 * Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
 * Licensed under GPLv3: see LICENSE file for details.
 */

require_once __DIR__ . '/../ReferenceStrategyInterface.php';
include_once __DIR__ . '/AuthorAnalyzer/AuthorExpression.php';
include_once __DIR__ . '/DateAnalyzer/DateExpression.php';
include_once __DIR__ . '/TitleAnalyzer/TitleExpression.php';
include_once __DIR__ . '/UrlAnalyzer/URLExpression.php';

class ApaReferenceStrategy implements ReferenceStrategyInterface {

    /**
     * Parse APA 7 reference components using standard Expression Analyzers.
     *
     * @param string $plainTextReference
     * @return array
     */
    public function parse(string $plainTextReference): array {
        $type = [];

        // 1. Author
        $authorExpression = AuthorExpression::parse($plainTextReference);
        $author = $authorExpression['value'];
        $authorType = $authorExpression['expression'];

        // 2. Date
        $dateExpression = DateExpression::parse($plainTextReference);
        $date = $dateExpression['value'];
        $dateType = $dateExpression['expression'];

        if ($dateExpression['expression'] === 'congress') {
            $type[] = $dateExpression['expression'];
        }

        // 3. URL
        $URLExpression = URLExpression::parse($plainTextReference);
        $url = $URLExpression['value'];
        $urlType = $URLExpression['expression'];

        // 4. Title
        $titleExpression = TitleExpression::parse($plainTextReference, $type);
        $title = $titleExpression['value'];
        $titleType = $titleExpression['expression'];
        $type = $titleExpression['expression'];

        return [
            'author' => $author,
            'authorType' => $authorType,
            'date' => $date,
            'dateType' => $dateType,
            'url' => $url,
            'urlType' => $urlType,
            'title' => $title,
            'titleType' => $titleType,
            'type' => $type,
        ];
    }
}
