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

    public function getFPages(): string{
        return $this->get('fpage');
    }

    public function getLPages(): string{
        return $this->get('lpage');
    }

    public function getArticleNumber(): string{
        return null;//
    }

    public function getSource(): string{
        return $this->getJournal();
    }

    public function createXMLElements(): array {
        $elements = [];
        
        //<source> tag creation
        $sourceElement = $this->createElement('source',$this->getSource());
        $elements[] = $sourceElement;

        //<article-title> tag creation
        $articleTitleElement = $this->createElement('article-title',$this->getTitle());
        $elements[] = $articleTitleElement;

        //<volume> tag creation
        $volumeElement = $this->createElement('volume',$this->getVolume());
        $elements[] = $volumeElement;

        //<issue> tag creation
        $editionElement = $this->createElement('issue',$this->getEdition());
        $elements[] = $editionElement;

        //<fpage> tag creation
        $fpageElement = $this->createElement('fpage',$this->getFPages());
        $elements[] = $fpageElement;

        //<lpage> tag creation
        $lpageElement = $this->createElement('lpage',$this->getLPages());
        $elements[] = $lpageElement;

        return $elements;
    }


}
