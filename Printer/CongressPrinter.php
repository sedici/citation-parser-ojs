<?php
include_once 'GenericPrinter.php';
class CongressPrinter extends GenericPrinter{

    public function toPlainText(): string{
        //'Apellido Autor, N. N. (10 de abril de 2023). Título del trabajo. (3ª ed., Vol. 4). Editorial.';
        return $this->reference['book'].' ('.$this->reference['edicion'].'). '.$this->reference['editorial'].'.';
    }

    public function getPublisherName(): string{
        return $this->get('publishername');
    }

    public function getTitle(): string{
        return $this->get('title');
    }

    public function getSource(): string{
        return $this->get('title');
    }

    public function getPublisherLoc(): string{
        return $this->get('publisherloc');
    }

    public function getEvent(): string{
        return $this->get('event');
    }

    public function getComment(): string{
        return $this->get('comment');
    }

    public function createXMLElements(): array {
        $elements = [];

        $sourceElement = $this->createElement('source',$this->getSource());
        $elements[] = $sourceElement;

        $commentElement = $this->createElement('comment',$this->getComment());
        $elements[] = $commentElement;

        $publisherLocElement = $this->createElement('publisher-loc',$this->getPublisherLoc());
        $elements[] = $publisherLocElement;

        $publisherNameElement = $this->createElement('publisher-name',$this->getPublisherName());
        $elements[] = $publisherNameElement;

        return $elements;
    }
}