<?php
include_once 'Expression.php';
class AuthorExpression extends Expression {
    
    public static function parse(string $text) {
        $apellido = "(?P<apellido>\p{L}+(\s\p{L}+)*)";
        $nombre = "(?P<nombres>(\p{Lu}\.\s?)+)";

        $role = "(?P<role>(\((Ed.|Coord.|Comp.)\)))?";  //Los roles deberian ser individuales 
        $roles = "(?P<roles>(\((Eds.|Coords.|Comps.)\)))?";   

        $author = "(?P<author>".$apellido.", ".$nombre." ".$role.")"; //Corregir los espacios
        $authors = '/'.$author.$roles.'((?:,\s|y\s))?/';

        preg_match_all($authors, $text, $matches, PREG_SET_ORDER);

        $authors_array = array();
        $counter = 1;
        
        foreach ($matches as $match) {
            $authors_array['authors']['author' . $counter] = [
                'apellido' => $match['apellido'],
                'nombres' => $match['nombres'],
                'role' => $match['role'] ?? '',
            ];
            $counter++;
        }

        //return $authors_array;
        return array('expression' => 'author', 'value' => $authors_array);
    }
}

