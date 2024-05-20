<?php
include_once 'Expression.php';
class DateExpression extends Expression {
    
    public static function parse($text) {
            
        //--------------------------------------------//
        //            DATE OF PUBLICATION             //
        //--------------------------------------------//

        //Recupera el valor que va dentro de los parentesis que representan la fecha. No chequea que este mal o bien
        /*$daterecolector = '/\.\s\((?P<fecha>(?>(\d.[^)]*|s\.f\.[a-z]?)))/';*/

        $year = '/(?P<fecha>\d{4})/';
        // 2023 
        $periodyear = '/(?P<fecha>\d{4}(\/\d{4})?)/';
        // 2023/2024

        $espesificdate = '\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})';
        $datecomplete = '/(?P<fecha>'.$espesificdate.'/'; 
        // 15 de abril de 2024

        // --- PERIODDAY --- //
        /* Fechas que solo son para congresos */

        $periodday = '/(?P<fecha>\d{2}-'.$espesificdate.'/';
        // 15-17 de abril de 2024

        $periodmonth = '/(?P<fecha>\d{2}\sde\s[a-z]{5,10}-'.$espesificdate.'/';
        // 31 de marzo-2 de abril de 2024

        // -------------------//

        // Fecha de recuperacion
        $recoverydate = '/Recuperado el\s(?P<fecha>\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})\sde\s(?P<url>https?:\/\/[^\s]+)/';
        // ejemplo: Recuperado el 15 de marzo de 2023 de https://google.com

        $expressions = array(
            '/(?P<fecha>\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})/' => 'datecomplete',
            '/(?P<fecha>\d{2}-\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})/' => 'periodday',
            '/(?P<fecha>\d{2}\sde\s[a-z]{5,10}-\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})/' => 'periodmonth',
            '/(?P<fecha>\d{4}\/\d{4})/' => 'periodyear',
            '/(?P<fecha>\d{4})/' => 'year',
        );
    
        foreach ($expressions as $pattern => $name) {
            if (preg_match($pattern, $text, $matches)) {
                return array('expression' => $name, 'value' => $matches['fecha']);
            }
        }
    
        return array('expression' => 'No match found', 'value' => '');
    
    }
}

