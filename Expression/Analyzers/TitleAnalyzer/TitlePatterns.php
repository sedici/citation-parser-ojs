<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
include_once('TitleExpressions/JournalExpression.php');
include_once('TitleExpressions/BookExpression.php');
include_once('TitleExpressions/CongressExpression.php');
include_once('TitleExpressions/ChapterExpression.php');
include_once('TitleExpressions/ThesisExpression.php');
include_once('TitleExpressions/WebsiteExpression.php');
include_once('TitleExpressions/NewspaperArticleExpression.php');
include_once('TitleExpressions/LawExpression.php');

    class TitlePatterns {

    /**
     * Returns all title patterns from different expression classes.
     * The title will help to identify the type of reference
     * (e.g., journal, book, thesis, webpage, newspaper article, law, congress, etc.).
     * 
     * @return array An array containing all title patterns.
     */
    public static function getAllPatterns() {

        $congressPattern = CongressExpression::getPattern();
        $bookPattern = BookExpression::getPattern();
        $chapterPattern = ChapterExpression::getPattern();
        $journalPattern = JournalExpression::getPattern();
        $thesisPattern = ThesisExpression::getPattern();
        $webpagePattern = WebsiteExpression::getPattern();
        $newspaperArticlePattern = NewspaperArticleExpression::getPattern();
        $lawPattern = LawExpression::getPattern();
        
        return array_merge(
            $lawPattern,
            $webpagePattern,
            $congressPattern,
            $thesisPattern,
            $chapterPattern,
            $journalPattern,
            $bookPattern,
            $newspaperArticlePattern,
        );
    }

        public static function getCongressPatterns(){
            return CongressExpression::getPattern();;
        }

        public static function getBookPatterns(){
            return BookExpression::getPattern();
        }

        public static function getJournalPatterns(){
            return JournalExpression::getPattern();
        }

        public static function getChapterPatterns(){
            return ChapterExpression::getPattern();
        }

        public static function getThesisPatterns(){
            return ThesisExpression::getPattern();
        }

        public static function getWebpagePatterns(){
            return WebsiteExpression::getPattern();
        }

        public static function getNewspaperArticlePatterns(){
            return NewspaperArticleExpression::getPattern();
        }

        public static function getLawPatterns(){
            return LawExpression::getPattern();
        }

    }