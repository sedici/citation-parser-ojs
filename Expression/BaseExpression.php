<?php
class RegexPart {
    protected $regex;
    protected $subParts;

    public function __construct($regex) {
        $this->regex = $regex;
        $this->subParts = [];
    }

    public function getRegex() {
        return $this->regex;
    }

    public function addSubPart(RegexPart $part) {
        $this->subParts[] = $part;
    }

    public function getSubParts() {
        return $this->subParts;
    }
}
