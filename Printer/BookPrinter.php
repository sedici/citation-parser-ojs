<?php
include_once 'TitlePrinter.php';
class BookPrinter extends TitlePrinter{

    public function getSource(){
        return $this->getBook();
    }

    public function toPlainText(): string{
        return $this->getBook().' ('.$this->getEdition().'). '.$this->getEditorial().'.';
    }

    public function getBook(){
        return $this->get('title');
    }

    public function getEdition(){
        return $this->get('edicion');
    }

    public function getEditionNumber(){
        return $this->get('nedicion');
    }

    public function getVolumen(){
        return $this->get('volumen');
    }

    public function getEditorial(){
        return $this->get('editorial');
    }
}