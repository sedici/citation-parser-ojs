<?php

include_once('TitleExpressions/JournalExpression.php');
include_once('TitleExpressions/BookExpression.php');
include_once('TitleExpressions/CongressExpression.php');
include_once('TitleExpressions/ChapterExpression.php');
include_once('TitleExpressions/ThesisExpression.php');

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
    
        return array_merge(
            $congressPattern,
            $thesisPattern,
            $chapterPattern,
            $journalPattern,
            $bookPattern,
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

    }