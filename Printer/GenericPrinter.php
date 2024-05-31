<?php
abstract class GenericPrinter {
    
    protected $reference;

    public function __construct(array $reference){
        $this->reference = $reference;
    }
    
    protected function printArray(): void {
        foreach ($this->reference as $key => $value) {
            echo "$key: $value" . PHP_EOL;
        }
    }

    public function print(): void {
        print($this->toPlainText($this->reference));
    }
    
    protected function get(string $key) {
        if ($this->reference === null){
            throw new \Exception("La referencia no está inicializada.");
        }

        if (array_key_exists($key, $this->reference)) {
            return $this->reference[$key];
        } else {
            throw new \Exception("La clave '{$key}' no existe en la referencia.");
        }
    }

    abstract public function toPlainText(): string;
}
