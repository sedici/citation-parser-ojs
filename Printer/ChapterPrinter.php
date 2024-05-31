<?php
include_once 'TitlePrinter.php';
class ChapterPrinter extends TitlePrinter{
    //IMCOMPLETO LA IMPRESION, FALTA LOS AUTORES.

    public function toPlainText(): string{
        return $this->reference['chapter'].' ('.$this->reference['book'].'). '.$this->reference['edicion'].'.'.$this->reference['editorial'].'.';
    }

    public function getSource(){
        return $this->getBook();
    }

    public function getBook(){
        return $this->get('book');
    }

    public function getEdition(){
        return $this->get('edicion');
    }

    public function getEditorial(){
        return $this->get('editorial');
    }
}