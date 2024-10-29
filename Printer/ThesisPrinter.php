<?php
include_once 'GenericPrinter.php';
class ThesisPrinter extends TitlePrinter{

    public function toPlainText(): string{
        //'Apellido Autor, N. N. (10 de abril de 2023). Título del trabajo. (3ª ed., Vol. 4). Editorial.';
        return "";
    }

    public function createXMLElements(): array {
        /*
            <publisher-name>Arizona State University</publisher-name>
            <part-title>Part 2, Space medicine</part-title>
            <source>Human factors: aerospace medicine and the origins of manned space flight in the United States</source>
            <comment>[dissertation]</comment>

            <publisher-loc>Santiago, Chile</publisher-loc>
            <publisher-name>Facultad de Medicina, Escuela de Salud pública, Universidad Mayor</publisher-name>
            <comment>
        */

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

    public function getSource(){
        return $this->getTitle();
    }

    public function getComment(){
        return $this->get('comment');
    }

    public function getPublisherName(){
        return $this->get('publisher-name');
    }

    public function getPublisherLoc(){
        return $this->get('publisher-loc');
    }
}