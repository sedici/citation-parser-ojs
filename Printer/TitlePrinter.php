<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
include_once 'GenericPrinter.php';
abstract class TitlePrinter extends GenericPrinter{

    public function getTitle(): string{
        return $this->get('title');
    }   

    public abstract function getSource();
}