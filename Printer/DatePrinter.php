<?php
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
            'enero' => 1,
            'febrero' => 2,
            'marzo' => 3,
            'abril' => 4,
            'mayo' => 5,
            'junio' => 6,
            'julio' => 7,
            'agosto' => 8,
            'septiembre' => 9,
            'octubre' => 10,
            'noviembre' => 11,
            'diciembre' => 12
        );
        return $months[$this->get('month')];
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