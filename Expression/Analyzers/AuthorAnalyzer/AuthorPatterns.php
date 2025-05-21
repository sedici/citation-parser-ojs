<?php

    class AuthorPatterns {

        public static function getAuthorPattern(){

           //Author Pattern.
           $apellido = "(?P<apellido>[A-Za-zÀ-ÿñÑ]+(?:\s[A-Za-zÀ-ÿñÑ]+)*)";
           $nombre = "(?P<nombres>(\p{Lu}\.\s?)+)";
   
           $role = "(?P<role>(\((Ed.|Coord.|Comp.)\)))?";  //Los roles deberian ser individuales 
           $roles = "(?P<roles>(\((Eds.|Coords.|Comps.)\)))?";   
   
           $author = "(?P<author>".$apellido.", ".$nombre.$role.")"; //Corregir los espacios
           $authors = '/'.$author.$roles.'((?:,\s|y\s|&\s))?/';

           return $authors;
        }
        public static function getInstitutionPattern(){
            //Institution Pattern.
            $institution = '/(?P<institution>[\s\S]*)/';
            
            return $institution;
        }

    }