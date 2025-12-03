<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
include_once 'URLPrinter.php';
class HANDLEPrinter extends URLPrinter{

    /**
     * Create the XML elements for the HANDLE citation part.
     *
     * @return array An array of XML elements representing the HANDLE citation part.
     */
    public function createXMLElements(): array {
        $elements = parent::createXMLElements();

        $pub_id = $this->createElement('pub-id', $this->getHandle());
        $pub_id->setAttribute('pub-id-type','handle');
        $elements[] = $pub_id;
        
        return $elements;
    }

    public function getHandle(): string{
        return $this->get('handle');
    }

    public function getPrefix(): string{
        return $this->get('prefix');
    }

    public function getSufix(): string{
        return $this->get('sufix');
    } 
    
}