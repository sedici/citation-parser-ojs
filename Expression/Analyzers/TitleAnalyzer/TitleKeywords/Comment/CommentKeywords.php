<?php

include_once('Es_Keywords.php');
include_once('En_Keywords.php');

    class CommentKeywords {

        public static function buildArrayKeywords(string $language) {

            $getMethodName = 'get' . ucfirst($language) . 'Keywords';
            $keywordsClassname = ucfirst($language) . '_Keywords';

            $arrayKeywords = $keywordsClassname::$getMethodName();

            return $arrayKeywords;
        }

    }