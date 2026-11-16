<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
include_once 'GenericPrinter.php';
class DatePrinter extends GenericPrinter{

    public function toPlainText(): string{
        return '('.$this->getDate().'). ';
    }

    public function getYear(){ 
        return $this->get('year');
    }


    public function getMonth(){ 
        $months = array(
            'enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4,
            'mayo' => 5, 'junio' => 6, 'julio' => 7, 'agosto' => 8,
            'septiembre' => 9, 'octubre' => 10, 'noviembre' => 11, 'diciembre' => 12,
            'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
            'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
            'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12,
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4,
            'jun' => 6, 'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12,
            'ene' => 1, 'abr' => 4, 'ago' => 8, 'set' => 9, 'dic' => 12
        );
        $month = $this->get('month');
        if (!$month) {
            return false;
        }
        if (is_numeric($month)) {
            $num = (int)$month;
            return ($num >= 1 && $num <= 12) ? $num : false;
        }
        $monthLower = strtolower(trim((string)$month, '.'));
        return $months[$monthLower] ?? false;
    }


    public function getDay(){ 
        return $this->get('day');
    }


    public function getDate(){ 
        return $this->get('date');
    }

    public function createXMLElements(): array {
        //$dateElement = $this->dom->createElement('date');
        $elements = [];

        if ($year = $this->getYear()) {
            $elements[] = $this->createElement('year', $year);
        }

        if ($month = $this->getMonth()) {
            $elements[] = $this->createElement('month', $month);
        }

        if ($day = $this->getDay()) {
            $elements[] = $this->createElement('day', $day);
        }

        return $elements;
    }
}