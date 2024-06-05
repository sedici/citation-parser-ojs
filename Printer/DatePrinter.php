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
        return $this->get('month');
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
        
        if ($date = $this->getDate()) {
            $elements[] = $this->createElement('year', $date);
        }

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