<?php
include_once 'GenericPrinter.php';
abstract class TitlePrinter extends GenericPrinter{

    public function getTitle(): string{
        return $this->get('title');
    }   

    public abstract function getSource();
}