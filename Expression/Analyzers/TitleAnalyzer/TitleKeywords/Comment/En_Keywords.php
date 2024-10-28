<?php

    //This class contains the ENGLISH keywords to determine if a reference is a thesis or a conference. 
    class En_Keywords {

        public static function getEnKeywords() {
            $enThesisKeywords = [',', 'thesis', 'doctoral', 'master', 'final project', 'dissertation', 'thesis paper', 'doctorate', 'degree', 'graduate'];
            $enCongressKeywords = ['conference', 'symposium', 'congress', 'presentation', 'lecture', 'power point slide', 'summary', 'speech', 'release'];

            $arrayKeywords = [
                'thesis' => $enThesisKeywords,
                'congress' => $enCongressKeywords
            ];

            return $arrayKeywords;
        }

    }