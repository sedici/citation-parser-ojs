<?php
include_once 'GenericPrinter.php';
class AuthorPrinter extends GenericPrinter {

    public function toPlainText(): string{
        // Inicializamos una variable para almacenar la cadena resultante
        $result = '';


        // Iteramos sobre el array de autores
        foreach ($this->reference as $index => $author) {
            $result .= $author['nombre'];

        }

        // Imprimimos el resultado
        return $result;
    }

    public function createXMLElements(): array {
        $elements = [];

        $authorElement = $this->dom->createElement('person-group');
        $authorElement->setAttribute('person-group-type','author');
        $elements[] = $authorElement;

        $authores = [
            ["nombre" => "Gabriel", "apellido" => "García Márquez"],
            ["nombre" => "Isabel", "apellido" => "Allende"],
            ["nombre" => "Jorge", "apellido" => "Luis Borges"],
        ];
        

        foreach ($authores as $autor) {
            $nameElement = $this->dom->createElement('name');
            $surname = $this->dom->createElement('surname',$autor['apellido']);
            $given_names = $this->dom->createElement('given-names',$autor['nombre']);
            $nameElement->appendChild($surname);
            $nameElement->appendChild($given_names);
            $authorElement->appendChild($nameElement);
        }
        
        return $elements;
    }
}