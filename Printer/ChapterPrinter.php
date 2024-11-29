<?php
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

    public function createXMLElements(): array {
        $elements = [];

        //<chapter-title> tag creation
        $chapterTitleElement = $this->createElement('chapter-title',$this->getTitle());
        $elements[] = $chapterTitleElement;
        
        //<source> tag creation
        $sourceElement = $this->createElement('source',$this->getSource());
        $elements[] = $sourceElement;

        //<edition> tag creation
        $publishernamElement = $this->createElement('publisher-name',$this->getEditorial());
        $elements[] = $publishernamElement;

        //<edition> tag creation if exists
        if (!empty($this->getEdition())) {
            $editionElement = $this->createElement('edition', $this->getEdition());
            $elements[] = $editionElement;   
        }

        //<volume> tag creation if exists
        if (!empty($this->getVolume())) {
            $volumeElement = $this->createElement('volume', $this->getVolume());
            $elements[] = $volumeElement;
        }

        //<person-group-type="editor"> tag creation.
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