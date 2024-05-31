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

}