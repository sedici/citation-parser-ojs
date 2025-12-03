<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

class DatePatterns {

    /**
     * Returns an array of regex patterns for date extraction.
     * @return array Associative array of regex patterns mapped to their identifiers. 
     */
    public static function getPatterns() {
        $year = '/(?P<year>\d{4})/';
        $noYear = '/\((((?P<noyear>[^0-9]*)))\)/';
        $periodYear = '/\((?P<fyear>\d{4})\-(?P<lyear>\d{4})\)/';
        $specificDate = '(?P<day>(\d{2}))\sde\s(?P<month>([a-z]{5,10}))\sde\s(?P<year>(\d{4}))';
        $dateComplete = '/\(' . $specificDate . '\)/'; 
        $periodDay = '/\((?P<fday>(\d{2}))-(?P<lday>(\d{2}))\sde\s(?P<month>([a-z]{5,10}))\sde\s(?P<year>(\d{4}))\)/';
        $periodMonth = '/\((?P<fday>(\d{2}))\sde\s(?P<fmonth>([a-z]{5,10}))-(?P<lday>(\d{2}))\sde\s(?P<lmonth>([a-z]{5,10}))\sde\s(?P<year>(\d{4}))\)/';
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