<?php
include_once 'Expression/AuthorExpression.php';
include_once 'Expression/DateExpression.php';
include_once 'Expression/TitleExpression.php';
include_once 'Expression/URLExpression.php';
include_once 'Printer/BookPrinter.php';
include_once 'Printer/JournalPrinter.php';
include_once 'Printer/ChapterPrinter.php';
include_once 'Printer/CongressPrinter.php';
include_once 'Printer/DatePrinter.php';
include_once 'Printer/AuthorPrinter.php';
include_once 'Printer/ThesisPrinter.php';
include_once 'Printer/URLPrinter.php';
include_once 'Printer/DOIPrinter.php';
include_once 'Printer/HANDLEPrinter.php';

class Reference {
    private $referenceText;
    private $type;

    public $author;
    public $authorType;

    public $title;
    public $titleType;

    public $date;
    public $dateType;

    public $url;
    public $urlType;

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
        $this->titleType = $titleExpression['expression'];
    }

    private function parseURL() {
        $URLExpression = URLExpression::parse($this->referenceText);
        $this->url = $URLExpression['value'];
        $this->urlType = $URLExpression['expression'];
    }

    private function parse() {
        $this->parseAuthor();
        $this->parseDate();
        $this->parseTitle();
        $this->parseURL();

    }

    // Getter para author
    public function merge(): array {
        // Usando array_merge para combinar los arreglos
        $combinedArray = array_merge($this->author, $this->title, $this->date, $this->url);
        return $combinedArray;
    }

    // Getter para author
    public function getAuthor() {[];
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

    // Getter para date
    public function getURL() {
        return $this->url;
    }

    // Getter para dateType
    public function getURLType() {
        return $this->urlType;
    }

    private function determineType() {
        if ($this->type === null) {
            // Lógica para determinar el tipo de referencia
        }
    }
}
