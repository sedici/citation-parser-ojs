<?php

include_once 'TitlePrinter.php';
class JournalPrinter extends TitlePrinter{

    public function getReferenceStringOld($reference): string{
        return $reference['journal'].$reference['revista'].', '.$reference['nedicio'].'('.$reference['volumen'].'), '.$reference['paginas'].'.';
    }

    public function toPlainText(): string{
        return $this->getTitle().$this->getJournal().', '.$this->getEdition().'('.$this->getVolume().'), '.$this->getPages().'.';
    }

    public function getJournal(): string{
        return $this->get('journal');
    }

    public function getEdition(): string{
        return $this->get('nedicio');
    }

    public function getVolume(): string{
        return $this->get('volumen');
    }

    public function getPage(): string{
        return $this->get('paginas');
    }

    public function getArticleNumber(): string{
        return null;//
    }

    public function getSource(): string{
        return $this->getJournal();
    }


}

/*
include_once 'ReferencePrinter.php';
class JournalPrinter extends ReferencePrinter{

    public static function getReferenceString($reference): string{
        return $reference['journal'].$reference['revista'].', '.$reference['nedicio'].'('.$reference['volumen'].'), '.$reference['paginas'].'.';
    }

    public static function getSource($reference): string{
        return $reference['revista'];
    }
}*/