<?php
abstract class Expression {
    public abstract static function parse(string $reference);
}