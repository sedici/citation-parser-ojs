<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once __DIR__ . '/Strategy/ReferenceStrategyInterface.php';

include_once 'Printer/BookPrinter.php';
include_once 'Printer/JournalPrinter.php';
include_once 'Printer/ChapterPrinter.php';
include_once 'Printer/ConfprocPrinter.php';
include_once 'Printer/DatePrinter.php';
include_once 'Printer/AuthorPrinter.php';
include_once 'Printer/ThesisPrinter.php';
include_once 'Printer/URLPrinter.php';
include_once 'Printer/DOIPrinter.php';
include_once 'Printer/HANDLEPrinter.php';
include_once 'Printer/WebpagePrinter.php';

class Reference {
    private $plainTextReference;
    private $type;
    protected string $citationStyle;

    public $author;
    public $authorType;

    public $title;
    public $titleType;

    public $date;
    public $dateType;

    public $url;
    public $urlType;

    public function __construct(string $plainTextReference, string $citationStyle = 'apa') {
        $this->plainTextReference = $plainTextReference;
        $this->citationStyle = $citationStyle;
        $this->type = [];
        $this->parse();
    }

    private function parse() {
        $strategy = $this->createStrategy();
        $result = $strategy->parse($this->plainTextReference);

        $this->author = $result['author'];
        $this->authorType = $result['authorType'];
        $this->date = $result['date'];
        $this->dateType = $result['dateType'];
        $this->url = $result['url'];
        $this->urlType = $result['urlType'];
        $this->title = $result['title'];
        $this->titleType = $result['titleType'];
        $this->type = $result['type'];
    }

    private function createStrategy(): ReferenceStrategyInterface {
        $style = ucfirst(strtolower($this->citationStyle));
        $className = "{$style}ReferenceStrategy";
        $strategyFile = __DIR__ . "/Strategy/{$style}/{$className}.php";

        if (file_exists($strategyFile)) {
            require_once $strategyFile;
        }

        if (class_exists($className)) {
            $strategy = new $className();
            if ($strategy instanceof ReferenceStrategyInterface) {
                return $strategy;
            }
        }

        // Fallback seguro a APA si no se encuentra la estrategia solicitada
        require_once __DIR__ . '/Strategy/Apa/ApaReferenceStrategy.php';
        return new ApaReferenceStrategy();
    }

    // Getter para author
    public function merge(): array {
        // Usando array_merge para combinar los arreglos
        $combinedArray = array_merge($this->author, $this->title, $this->date, $this->url);
        return $combinedArray;
    }

    // Getter para author
    public function getType() {
        return $this->type;
    }

    public function getPlainTextReference() {
        return $this->plainTextReference;
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

    public function setAuthor($key, $value) {
        $this->author[$key] = $value;
    }
}
