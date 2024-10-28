<?php

    class UrlPatterns {

        public static function getPatterns(){
            
            //------------ URL ------------//

            $url = '(?P<url>https?:\/\/[^\s]+)';
            $opcionalurl = '(\s'. $url .')?/';
            
            $url = '/(?P<url>https?:\/\/[^\s]+)/'; 
            $doi = '/(?P<doi>https?:\/\/doi.org\/[^\s]+)$/';
            $doi = '/(?P<doi>https?:\/\/doi.org\/[^\s]+)$/';
            // https://google.com | http://wikipedia.com
            
            //--------------------------------------------------------------//
            
            return [
                '/(?P<url>https?:\/\/doi.org\/(?P<doi>(?P<prefix>([^\s]+))\/(?P<subfix>([^\s]+))))$/' => 'DOI',
                '/(?P<url>https?:\/\/[^\s\/]+\/(?P<handle>handle\/((?P<prefix>([^\s]+))\/(?P<subfix>([^\s]+)))))$/' => 'HANDLE',
                '/(?P<url>https?:\/\/[^\s]+)/' => 'URL',
            ];

        }
    }