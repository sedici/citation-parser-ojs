<?php

include_once('TitleExpressions/JournalExpression.php');
include_once('TitleExpressions/BookExpression.php');
include_once('TitleExpressions/CongressExpression.php');
include_once('TitleExpressions/ChapterExpression.php');
include_once('TitleExpressions/ThesisExpression.php');
include_once('TitleExpressions/WebsiteExpression.php');
include_once('TitleExpressions/NewspaperArticleExpression.php');
include_once('TitleExpressions/LawExpression.php');

    class TitlePatterns {

    public static function getAllPatterns() {

        // Get congress references PATTERN.
        $congressPattern = CongressExpression::getPattern();
        
        // Get book references PATTERN.
        $bookPattern = BookExpression::getPattern();
    
        // Get book's chapter reference PATTERN.
        $chapterPattern = ChapterExpression::getPattern();
    
        // Get journal references PATTERN.
        $journalPattern = JournalExpression::getPattern();
    
        // Get thesis references PATTERN.
        $thesisPattern = ThesisExpression::getPattern();

        // Get website references PATTERN.
        $webpagePattern = WebsiteExpression::getPattern();
    
        // Get newspaper article references PATTERN.
        $newspaperArticlePattern = NewspaperArticleExpression::getPattern();

        // Get law references PATTERN.
        $lawPattern = LawExpression::getPattern();

        // Merge all patterns into a single array.
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

    //--------------------------------------> SPECIFIC PATTERN GETTERS <--------------------------------------------------//

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