<?php
require_once 'ReferenceStrategyFactory.php';
require_once 'TitleStrategys/BookStrategy.php';
require_once 'TitleStrategys/CharapterStrategy.php';
require_once 'TitleStrategys/CongressStrategy.php';
require_once 'TitleStrategys/JournalStrategy.php';

class ReferenceParser {
    protected $strategy;

    public function setStrategy($strategy) {
        $this->strategy = $strategy;
    }

    public function newStrategy($strategyText) {
        switch ($this->type) {
            case 'journal':
                $this->setStrategy(new JournalStrategy()); 
                break;
            case 'book':
                $this->setStrategy(new BookStrategy());
                break;
            case 'chapter':
                $this->setStrategy(new CharapterStrategy());
                break;
            case 'congress':
                $this->setStrategy(new CongressStrategy());
                break;
        }

    }

    public function parse($text) {
        return $this->strategy->parse($text);
    }
}
