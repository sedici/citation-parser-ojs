<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

include_once('GenericExpression.php');

    class ChapterExpression extends GenericExpression{

        /**
         * Returns the regular expression patterns to identify chapter titles in references.
         *
         * @return array An associative array where keys are regex patterns and values are the type 'chapter'.
         * 
         * CASES HANDLED:
            * 1. One contributor (singular role), e.g. "Ed."
            * 2. Two contributors (plural role), e.g. "Eds."
            * 3. Three contributors (plural role), e.g. "Eds."
         */

        public static function getPattern(){

            $title = "(?P<title>.*?)";
            $editorial = '(?P<editorial>.+?\.)';
            $pluralRole = "(?P<role>(.*(?<=s)\.))"; //Only accepts a word in plural, for example: (Eds.|Comps.|Coords.|etc...).
            $singularRole = "(?P<role>.*(?<!s)\.)"; //Only accepts a word in singular, for example: (Ed.|Comp.|Coord.|etc...).
            $book = "(?P<book>[A-Z][A-Za-zÀ-ÿ:,-°\s]+)";

            $volume = "(?P<volume>(?:[IVXLCDM]+|[0-9]+))";
            $editionNumber = "(?P<nedition>[0-9]+ᵃ)";
            $pages = "(pp.\s(?P<pages>(\d{1,4}-\d{1,4})))?)\)";
            $edition = "(?P<edition>($editionNumber\sed\.,\s)?(Vol\.\s$volume)?(,\s)?$pages";

            $contributorOneFullname = "(?P<contributorname1>(\p{Lu}\.\s?)+)(?P<contributorsurname1>[A-Za-zÀ-ÿñÑ]+(?:\s[A-Za-zÀ-ÿñÑ]+)*)";
            $contributorTwoFullname = "(?P<contributorname2>(\p{Lu}\.\s?)+)(?P<contributorsurname2>[A-Za-zÀ-ÿñÑ]+(?:\s[A-Za-zÀ-ÿñÑ]+)*)";
            $contributorThreeFullname = "(?P<contributorname3>(\p{Lu}\.\s?)+)\s?(?P<contributorsurname3>[A-Za-zÀ-ÿñÑ]+(?:\s[A-Za-zÀ-ÿñÑ]+)*)";

            $oneContributor = "(?P<contributors>$contributorOneFullname)";
            $twoContributors = "(?P<contributors>$contributorOneFullname\s(y|&|and)\s$contributorTwoFullname)";
            $threeContributors = "(?P<contributors>$contributorOneFullname,\s$contributorTwoFullname\s(y|&|and)\s$contributorThreeFullname)";
            
            $chapterRegexCaseOne = "/\)\.\s$title\s(En|In|)\s$oneContributor\s\($singularRole\),\s$book\s(\($edition)\.\s$editorial/u";
            $chapterRegexCaseTwo = "/\)\.\s$title\s(En|In|)\s$twoContributors\s\($pluralRole\),\s$book\s(\($edition)\.\s$editorial/u";
            $chapterRegexCaseThree = "/\)\.\s$title\s(En|In)\s$threeContributors\s\($pluralRole\),\s$book\s(\($edition)\.\s$editorial/u";
            
            return array(
                $chapterRegexCaseOne => 'chapter',
                $chapterRegexCaseTwo => 'chapter',
                $chapterRegexCaseThree => 'chapter'
            );
        }
    }