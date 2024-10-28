<?php

    class AuthorPatterns {

        public static function getPatterns () {

            $apellido = "(?P<apellido>[A-Za-zÀ-ÿñÑ]+(?:\s[A-Za-zÀ-ÿñÑ]+)*)";
            $nombre = "(?P<nombres>(\p{Lu}\.\s?)+)";
    
            $role = "(?P<role>(\((Ed.|Coord.|Comp.)\)))?";  //Los roles deberian ser individuales 
            $roles = "(?P<roles>(\((Eds.|Coords.|Comps.)\)))?";   
    
            $author = "(?P<author>".$apellido.", ".$nombre." ".$role.")"; //Corregir los espacios
            $authors = '/'.$author.$roles.'((?:,\s|y\s))?/u';

            return $authors;

        }

    }