<?php
include_once 'GenericPrinter.php';
class CongressPrinter extends GenericPrinter{

    public function toPlainText(): string{
        //'Apellido Autor, N. N. (10 de abril de 2023). Título del trabajo. (3ª ed., Vol. 4). Editorial.';
        return $this->reference['book'].' ('.$this->reference['edicion'].'). '.$this->reference['editorial'].'.';
    }
}