<?php

class DatePatterns {

    public static function getPatterns() {
        
        // Año específico
        $year = '/(?P<year>\d{4})/';
        
        // Sin año
        $noYear = '/\((((?P<noyear>[^0-9]*)))\)/';

        // Rango de años (2023-2024)
        $periodYear = '/\((?P<fyear>\d{4})\-(?P<lyear>\d{4})\)/';
        
        // Fecha completa (ejemplo: 15 de abril de 2024)
        $specificDate = '(?P<day>(\d{2}))\sde\s(?P<month>([a-z]{5,10}))\sde\s(?P<year>(\d{4}))';
        $dateComplete = '/\(' . $specificDate . '\)/'; 
        
        // Rango de días (ejemplo: 15-17 de abril de 2024)
        $periodDay = '/\((?P<fday>(\d{2}))-(?P<lday>(\d{2}))\sde\s(?P<month>([a-z]{5,10}))\sde\s(?P<year>(\d{4}))\)/';
        
        // Rango de meses (ejemplo: 31 de marzo-2 de abril de 2024)
        $periodMonth = '/\((?P<fday>(\d{2}))\sde\s(?P<fmonth>([a-z]{5,10}))-(?P<lday>(\d{2}))\sde\s(?P<lmonth>([a-z]{5,10}))\sde\s(?P<year>(\d{4}))\)/';
        
        // Fecha recuperada (ejemplo: Recuperado el 15 de marzo de 2023 de https://google.com)
        $recoveryDate = '/Recuperado el\s(?P<fecha>\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})\sde\s(?P<url>https?:\/\/[^\s]+)/';
        
        return [
            $dateComplete => 'datecomplete',
            $periodDay => 'periodday',
            $periodMonth => 'periodmonth',
            $periodYear => 'periodyear',
            $year => 'year',
            $noYear => 'noyear',
            $recoveryDate => 'recoverydate'
        ];
    }
}