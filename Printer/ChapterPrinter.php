<?php
include_once 'TitlePrinter.php';
class ChapterPrinter extends TitlePrinter{
    //IMCOMPLETO LA IMPRESION, FALTA LOS AUTORES.

    public function toPlainText(): string{
        return $this->getChapter().' ('.$this->getBook().'). '.$this->getEdition().'.'.$this->getEditorial().'.';
    }

    public function getSource(){
        return $this->getBook();
    }

    public function getChapter(){
        return $this->getTitle();
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

    public function createXMLElements(): array {
        $elements = [];

        $parttitleElement = $this->createElement('part-title',$this->getTitle());
        $elements[] = $parttitleElement;
        
        $sourceElement = $this->createElement('source',$this->getSource());
        $elements[] = $sourceElement;

        $publishernamElement = $this->createElement('publisher-name',$this->getEditorial());
        $elements[] = $publishernamElement;

        return $elements;
    }
}