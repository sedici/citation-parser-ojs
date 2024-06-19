<?php
include_once 'URLPrinter.php';
class DOIPrinter extends URLPrinter{

    public function createXMLElements(): array {
        $elements = parent::createXMLElements();

        $pub_id = $this->createElement('pub-id', $this->getDOI());
        $pub_id->setAttribute('pub-id-type','doi');
        $elements[] = $pub_id;
        
        return $elements;
    }

    public function getDOI(): string{
        return $this->get('doi');
    }

    public function getPrefix(): string{
        return $this->get('prefix');
    }

    public function getSufix(): string{
        return $this->get('sufix');
    } 
    
}