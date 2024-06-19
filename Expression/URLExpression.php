<?php
include_once 'Expression.php';
class URLExpression extends Expression {
    
    public static function parse($text) {
            
        //------------ URL ------------//

        $url = '(?P<url>https?:\/\/[^\s]+)';
        $opcionalurl = '(\s'. $url .')?/';

        $url = '/(?P<url>https?:\/\/[^\s]+)/'; 
        $doi = '/(?P<doi>https?:\/\/doi.org\/[^\s]+)$/';
        $doi = '/(?P<doi>https?:\/\/doi.org\/[^\s]+)$/';
        // https://google.com | http://wikipedia.com

        //--------------------------------------------------------------//

        $expressions = array(
            '/(?P<url>https?:\/\/doi.org\/(?P<doi>(?P<prefix>([^\s]+))\/(?P<subfix>([^\s]+))))$/' => 'DOI',
            '/(?P<url>https?:\/\/[^\s\/]+\/(?P<handle>handle\/((?P<prefix>([^\s]+))\/(?P<subfix>([^\s]+)))))$/' => 'HANDLE',
            '/(?P<url>https?:\/\/[^\s]+)/' => 'URL',
         );

        foreach ($expressions as $pattern => $name) {
            if (preg_match($pattern, $text, $matches)) {
                return array('expression' => $name, 'value' => $matches);
            }
        }

        return array('expression' => 'No match found', 'value' => '');

    }
}

