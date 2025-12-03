<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
    class UrlPatterns {

        /**
         * Returns an array of URL patterns and their corresponding names.
         * @return array An associative array of regex patterns and names.
         */
        public static function getPatterns(){
            return [
                '/(?P<url>https?:\/\/doi.org\/(?P<doi>(?P<prefix>([^\s]+))\/(?P<subfix>([^\s]+))))$/' => 'DOI',
                '/(?P<url>https?:\/\/[^\s\/]+\/(?P<handle>handle\/((?P<prefix>([^\s]+))\/(?P<subfix>([^\s]+)))))$/' => 'HANDLE',
                '/(?P<url>https?:\/\/[^\s]+)/' => 'URL',
            ];

        }
    }