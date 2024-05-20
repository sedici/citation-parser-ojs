<?php
include_once 'Expression/AuthorExpression.php';
include_once 'Expression/DateExpression.php';
include_once 'Expression/TitleExpression.php';
include_once 'Printer/BookPrinter.php';
include_once 'Printer/JournalPrinter.php';
include_once 'Printer/ChapterPrinter.php';
include_once 'Printer/CongressPrinter.php';
include_once 'Printer/DatePrinter.php';
include_once 'Printer/AuthorPrinter.php';

class Reference {
    private $referenceText;
    private $type;

    public $author;
    public $authorType;

    public $title;
    public $titleType;

    public $date;
    public $dateType;

    public function __construct(string $referenceText) {
        $this->referenceText = $referenceText;
        $this->type = null;
        $this->parse();
    }

    private function parseAuthor() {
        $authorExpression = $this->author = AuthorExpression::parse($this->referenceText);
        $this->author = $authorExpression['value'];
        $this->authorType = $authorExpression['expression'];
    }

    private function parseDate() {
        $dateExpression = DateExpression::parse($this->referenceText);
        $this->date = $dateExpression['value'];
        $this->dateType = $dateExpression['expression'];
    }

    private function parseTitle() {
        $titleExpression = TitleExpression::parse($this->referenceText);
        $this->title = $titleExpression['value'];
        $this->type = $titleExpression['expression'];
        $this->TitleType = $titleExpression['expression'];
    }

    private function parse() {
        $this->parseAuthor();
        $this->parseDate();
        $this->parseTitle();

    }

    public function print(){
        
        if($this->authorType == null) return;
        $printerClassName = ucfirst($this->authorType).'Printer';
        $authorString = $printerClassName::getReferenceString($this->author);

        if($this->dateType == null) return;
        //$printerClassName = $this->dateType.'Printer';
        $printerClassName = 'DatePrinter';
        $dateString = $printerClassName::getReferenceString($this->date);
        
        if($this->type == null) return;
        $printerClassName = ucfirst($this->type).'Printer';
        $titleString = $printerClassName::getReferenceString($this->title);

        print($authorString.$dateString.$titleString);
    }

    // Getter para author
    public function getAuthor() {
        return $this->author;
    }

    // Getter para authorType
    public function getAuthorType() {
        return $this->authorType;
    }

    // Getter para title
    public function getTitle() {
        return $this->title;
    }

    // Getter para titleType
    public function getTitleType() {
        return $this->titleType;
    }

    // Getter para date
    public function getDate() {
        return $this->date;
    }

    // Getter para dateType
    public function getDateType() {
        return $this->dateType;
    }

    private function determineType() {
        if ($this->type === null) {
            // Lógica para determinar el tipo de referencia
        }
    }
}
