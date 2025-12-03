<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
include_once 'GenericPrinter.php';
class URLPrinter extends GenericPrinter{

    public function toPlainText(): string{
        return $this->getURL();
    }

    public function getURL(){ 
        return $this->get('url');
    }

    /** 
     * Creates the XML elements representing the URL in JATS format.
     * @return array An array of XML elements.
     */
    public function createXMLElements(): array {
        $elements = [];
        $ext_link = $this->createElement('ext-link', $this->getURL());
        $ext_link->setAttribute('ext-link-type','uri');
        $ext_link->setAttribute('xlink:href',$this->getURL());
        $elements[] = $ext_link;

        $uriElement = $this->createElement('uri', $this->getURL());
        $elements[] = $uriElement;

        return $elements;
    }
}