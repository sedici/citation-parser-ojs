<?php
abstract class GenericPrinter {
    
    protected $reference;
    protected $dom;

    public function __construct(array $reference, \DOMDocument $dom = null) {
        $this->reference = $reference;
        $this->dom = $dom ?? new \DOMDocument('1.0', 'UTF-8');
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
        if ($this->reference === null) {
            //trigger_error("La referencia no está inicializada.", E_USER_WARNING);
            return false;
        }

        if (array_key_exists($key, $this->reference)) {
            return $this->reference[$key];
        } else {
            //trigger_error("La clave '{$key}' no existe en la referencia.", E_USER_WARNING);
            return false;
        }
    }

    abstract public function toPlainText(): string;

    public function printAsXML(): void {
        $rootElement = $this->createXMLElement();
        $this->dom->appendChild($rootElement);
        echo $this->dom->saveXML();
    }

     /**
     * Creates a DOMElement with the specified name and value.
     *
     * @param string $name The name of the element.
     * @param string $value The value of the element.
     * @return \DOMElement The created DOMElement.
     */
    protected function createElement(string $name, string $value): \DOMElement {
        return $this->dom->createElement($name, htmlspecialchars($value));
    }

    /**
     * Creates XML elements based on the reference data.
     *
     * @return \DOMElement[] Array of DOMElement objects.
     */
    abstract public function createXMLElements(): array;
}
