<?php
include_once 'GenericPrinter.php';
class URLPrinter extends GenericPrinter{

    public function toPlainText(): string{
        return $this->getURL();
    }

    public function getURL(){ 
        return $this->get('url');
    }

    public function createXMLElements(): array {
        $elements = [];
        $ext_link = $this->createElement('ext-link', $this->getURL());
        $ext_link->setAttribute('ext-link-type','uri');
        $ext_link->setAttribute('xlink:href',$this->getURL());
        $elements[] = $ext_link;
        return $elements;
    }
}