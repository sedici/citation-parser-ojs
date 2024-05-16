<?php
include_once 'Expression/AuthorExpression.php';
include_once 'Expression/DateExpression.php';
include_once 'Expression/TitleExpression.php';
include_once 'Printer/BookPrinter.php';
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

    public function __construct($referenceText) {
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

    public function printer(){
        
        if($this->authorType == null) return;
        $printerClassName = ucfirst($this->authorType).'Printer';
        $authorString = $printerClassName::getString($this->author);

        if($this->dateType == null) return;
        //$printerClassName = $this->dateType.'Printer';
        $printerClassName = 'DatePrinter';
        $dateString = $printerClassName::getString($this->date);
        
        if($this->type == null) return;
        $this->type = 'book';
        $printerClassName = ucfirst($this->type).'Printer';
        $titleString = $printerClassName::getString($this->title);


        print($authorString.$dateString.$titleString);
    }

    public function toString(){
        $this->printAuthor();
        $this->printDate();
        $this->printTitle();
    }

    public function printAuthor(){
        print_r($this->author);
    }

    public function printDate(){
        echo "Expresión que coincidió: " . $this->date['expression'] . "\n";
        echo "Fecha capturada: " . $this->date['value']. "\n";
    }

    public function printTitle(){
        if($this->title === null) return;
        if(strcmp($this->title['expression'],'book') == 0){

            echo "Titulo: " . $this->title['value']['book'] . "\n";
            echo "Edicion: " . $this->title['value']['edicion'] . "\n";
            echo "Editorial: " . $this->title['value']['editorial'] . "\n";
        
        } 
        if (strcmp($this->title['expression'],'journal') == 0){
        
            echo "journal: " . $this->title['value']['journal'] . "\n";
            echo "revista: " . $this->title['value']['revista'] . "\n";
            echo "nedicio: " . $this->title['value']['nedicio'] . "\n";
            echo "volumen: " . $this->title['value']['volumen'] . "\n";
            echo "paginas: " . $this->title['value']['paginas'] . "\n";
        
        }
        if (strcmp($this->title['expression'],'chapter') == 0){
        
            echo "chapter: " . $this->title['value']['chapter'] . "\n";
            echo "book: " . $this->title['value']['book'] . "\n";
            echo "edicion: " . $this->title['value']['edicion'] . "\n";
            echo "editorial: " . $this->title['value']['editorial'] . "\n";
        }
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
