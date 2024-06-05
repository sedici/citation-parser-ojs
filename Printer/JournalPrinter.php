<?php

include_once 'TitlePrinter.php';
class JournalPrinter extends TitlePrinter{

    public function toPlainText(): string{
        return $this->getTitle().$this->getJournal().', '.$this->getEdition().'('.$this->getVolume().'), '.$this->getPages().'.';
    }

    public function getJournal(): string{
        return $this->get('revista');
    }

    public function getEdition(): string{
        return $this->get('nedicio');
    }

    public function getVolume(): string{
        return $this->get('volumen');
    }

    public function getPages(): string{
        return $this->get('paginas');
    }

    public function getArticleNumber(): string{
        return null;//
    }

    public function getSource(): string{
        return $this->getJournal();
    }

    public function createXMLElements(): array {
        $elements = [];
        
        $sourceElement = $this->createElement('source',$this->getSource());
        $elements[] = $sourceElement;

        $articleTitleElement = $this->createElement('article-title',$this->getTitle());
        $elements[] = $articleTitleElement;

        $volumeElement = $this->createElement('volume',$this->getVolume());
        $elements[] = $volumeElement;

        $editionElement = $this->createElement('issue',$this->getEdition());
        $elements[] = $editionElement;

        $fpageElement = $this->createElement('fpage',$this->getPages());
        $elements[] = $fpageElement;

        $lpageElement = $this->createElement('lpage',$this->getPages());
        $elements[] = $lpageElement;

        return $elements;
    }


}
