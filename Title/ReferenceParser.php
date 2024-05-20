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

    public function setStrategyFromString($strategyText) {
        $strategyClassName = ucfirst($this->type) . 'Strategy';
        try {
            if (!class_exists($strategyClassName)) {
                throw new Exception("Strategy class $strategyClassName does not exist.");
            }
            $this->setStrategy(new $strategyClassName());
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }      

    public function parse($text) {
        return $this->strategy->parse($text);
    }
}
