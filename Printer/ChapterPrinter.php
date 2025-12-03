<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
include_once 'TitlePrinter.php';
class ChapterPrinter extends TitlePrinter{

    public function toPlainText(): string{
        return $this->getChapter().' ('.$this->getBook().'). '.$this->getEdition().'.'.$this->getEditorial().'.';
    }

    public function getEditorsArray(){
        $editorsArray = array();
        $counter = 1;
        while (isset($this->reference['contributorname' . $counter]) && isset($this->reference['contributorsurname' . $counter])){
            $editorsArray['editor'.$counter] = [
                'name' => $this->reference['contributorname' . $counter],
                'surname' => $this->reference['contributorsurname' . $counter]
            ];
            $counter++;
        }
        return $editorsArray;
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
        return $this->get('nedition');
    }

    public function getEditorial(){
        return $this->get('editorial');
    }

    public function getVolume(){
        return $this->get('volume');
    }

    /**
     * Create XML elements for the chapter citation
     *
     * @return array An array of XML elements representing the chapter citation
     */
    public function createXMLElements(): array {
        $elements = [];

        $chapterTitleElement = $this->createElement('chapter-title',$this->getTitle());
        $elements[] = $chapterTitleElement;
        
        $sourceElement = $this->createElement('source',$this->getSource());
        $elements[] = $sourceElement;

        $publishernamElement = $this->createElement('publisher-name',$this->getEditorial());
        $elements[] = $publishernamElement;

        if (!empty($this->getEdition())) {
            $editionElement = $this->createElement('edition', $this->getEdition());
            $elements[] = $editionElement;   
        }

        if (!empty($this->getVolume())) {
            $volumeElement = $this->createElement('volume', $this->getVolume());
            $elements[] = $volumeElement;
        }

        $editorsElement = $this->dom->createElement('person-group');
        $editorsElement->setAttribute('person-group-type', 'editor');
        $elements[] = $editorsElement;

        $editorsArray = $this->getEditorsArray();

        foreach ($editorsArray as $editor){
            $nameElement = $this->dom->createElement('name');
            $surname = $this->dom->createElement('surname', $editor['surname']);
            $given_names = $this->dom->createElement('given-names', $editor['name']);
            $nameElement->appendChild($surname);
            $nameElement->appendChild($given_names);
            $editorsElement->appendChild($nameElement);
        }
        

        return $elements;
    }
}