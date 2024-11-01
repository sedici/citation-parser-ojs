<?php

include_once('AuthorPatterns.php');


    class AuthorAnalyzer {

        private $patterns;

        public function __construct(){
            $this->patterns = AuthorPatterns::getPatterns();
        }

        public function analyze(string $text){

            preg_match_all($this->patterns, $text, $matches, PREG_SET_ORDER);

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
            return array('expression' => null, 'value' => $authors_array);

        }

    }