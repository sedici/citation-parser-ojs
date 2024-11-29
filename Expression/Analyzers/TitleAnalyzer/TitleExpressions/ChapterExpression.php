<?php
//original regex: '/\)\.\s(?P<title>[A-Z0-9À-ÿ].+?\.)\sEn\s(?P<author>(?P<nombres>(\p{Lu}\.\s?)+)\s(?P<apellido>\p{L}+(\s\p{L}+)*)\s(?P<role>(\((Ed.|Coord.|Comp.)\)))?((?:,\s|y|&\s))?)+(?P<roles>(\((Eds.|Coords.|Comps.)\)))?(?P<book>[A-Z][A-Za-zÀ-ÿ:,-°\s]+)\s(\((?P<edicion>((?P<nedicion>[0-9]+ᵃ)\sed\.,\s)?(Vol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?(,\s)?(pp.\s(?P<paginas>(\d{1,4}-\d{1,4})))?)\))\.\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)/u' => 'chapter'
include_once('GenericExpression.php');

    class ChapterExpression extends GenericExpression{

        public static function getPattern(){

            //--------------------------> Parts of a chapter Reference <--------------------------:
            $title = "(?P<title>.*?)";
            $editorial = '(?P<editorial>.+?\.)';
            $pluralRole = "(?P<role>(.*(?<=s)\.))"; //Only accepts a word in plural, for example: (Eds.|Comps.|Coords.|etc...).
            $singularRole = "(?P<role>.*(?<!s)\.)"; //Only accepts a word in singular, for example: (Ed.|Comp.|Coord.|etc...).
            $book = "(?P<book>[A-Z][A-Za-zÀ-ÿ:,-°\s]+)";

            $volume = "(?P<volume>(?:[IVXLCDM]+|[0-9]+))";
            $editionNumber = "(?P<nedition>[0-9]+ᵃ)";
            $pages = "(pp.\s(?P<pages>(\d{1,4}-\d{1,4})))?)\)";
            $edition = "(?P<edition>($editionNumber\sed\.,\s)?(Vol\.\s$volume)?(,\s)?$pages";


            // -----------------------> Contributors regex's: <----------------------------------------------------
            $contributorOneFullname = "(?P<contributorname1>(\p{Lu}\.\s?)+)(?P<contributorsurname1>[A-Za-zÀ-ÿñÑ]+(?:\s[A-Za-zÀ-ÿñÑ]+)*)";
            $contributorTwoFullname = "(?P<contributorname2>(\p{Lu}\.\s?)+)(?P<contributorsurname2>[A-Za-zÀ-ÿñÑ]+(?:\s[A-Za-zÀ-ÿñÑ]+)*)";
            $contributorThreeFullname = "(?P<contributorname3>(\p{Lu}\.\s?)+)\s?(?P<contributorsurname3>[A-Za-zÀ-ÿñÑ]+(?:\s[A-Za-zÀ-ÿñÑ]+)*)";

            $oneContributor = "(?P<contributors>$contributorOneFullname)";
            $twoContributors = "(?P<contributors>$contributorOneFullname\s(y|&|and)\s$contributorTwoFullname)";
            $threeContributors = "(?P<contributors>$contributorOneFullname,\s$contributorTwoFullname\s(y|&|and)\s$contributorThreeFullname)";

            //-------------------------------------------------------------------------------------------//
            //-----------------------> Complete regex cases of chapter's reference. <--------------------------//
            //-------------------------------------------------------------------------------------------//

            //chapterRegexCaseOne is for references to chapters that have a single ed.|comp.|coord. Matches array contains only contributorname1 and contributorsurname1.
            //chapterRegexCaseTwo is for references to chapters that have 2 eds.|comps.|coords. Matches array contains contributorname1, contributorsurname1, contributorname2, contributorsurname2.
            //chapterRegexCaseThree is for references to chapters that have 3 eds.|comps.|coords. Matches array contains contributorname1, contributorsurname1, contributorname2, contributorsurname2, contributorname3, contributorsurname3.
            
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