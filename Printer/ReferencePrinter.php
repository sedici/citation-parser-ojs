<?php
abstract class ReferencePrinter {
    public static function printArray(array $reference): void {
        foreach ($reference as $key => $value) {
            echo "$key: $value" . PHP_EOL;
        }
    }

    public static function printReference(array $reference): void {
        print(static::getReferenceString($reference));
    }
    
    abstract public static function getReferenceString($reference): string;
}