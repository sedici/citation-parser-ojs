<?php
include_once 'Reference.php';

class JATSReference {
    public $reference;

    public function __construct(Reference $reference){
        $this->reference = $reference;
    }
}