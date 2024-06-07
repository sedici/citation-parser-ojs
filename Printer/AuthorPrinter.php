<?php
include_once 'GenericPrinter.php';
class AuthorPrinter extends GenericPrinter {

    public function toPlainText(): string{
        $result = "";
        if (isset($this->reference['authors'])) {
            foreach ($this->reference['authors'] as $key => $author) {
                $result .= "$key:\n";
                $result .= "Apellido: " . $author['apellido'] . "\n";
                $result .= "Nombres: " . $author['nombres'] . "\n";
                $result .= "Role: " . $author['role'] . "\n";
                $result .= "\n";
            }
        } else {
            $result .= "No authors found.\n";
        }
        return $result;
    }

    public function createXMLElements(): array {
        $elements = [];

        $authorElement = $this->dom->createElement('person-group');
        $authorElement->setAttribute('person-group-type','author');
        $elements[] = $authorElement;

        $authores = $this->reference['authors'];

        foreach ($authores as $autor) {
            $nameElement = $this->dom->createElement('name');
            $surname = $this->dom->createElement('surname',$autor['apellido']);
            $given_names = $this->dom->createElement('given-names',$autor['nombres']);
            $nameElement->appendChild($surname);
            $nameElement->appendChild($given_names);
            $authorElement->appendChild($nameElement);
        }
        
        return $elements;
    }
}