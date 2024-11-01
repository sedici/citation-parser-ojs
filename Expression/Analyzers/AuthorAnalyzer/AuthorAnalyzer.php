<?php

include_once('AuthorPatterns.php');


    class AuthorAnalyzer {

        private $authorPattern;
        private $institutionPattern;

        public function __construct(){
            $this->authorPattern = AuthorPatterns::getAuthorPattern();
            $this->institutionPattern = AuthorPatterns::getInstitutionPattern();
        }

        public function analyze(string $text){

            //Match using the author's regex pattern.
            //Example reference ($text) at this moment: 
            // Smith, J. A., Johnson, M. L., Universidad de California, Berkeley, Instituto de Investigación en Ciencias Sociales, Universidad de Harvard, & Universidad Nacional Autónoma de México. (2021). An example reference.
            
            preg_match_all($this->authorPattern, $text, $matches, PREG_SET_ORDER);

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

            //Matches in the reference are removed
            $textWithoutAuthors = preg_replace($this->authorPattern, '', $text);
            // Reference ($text) at this moment:
            // Universidad de California, Berkeley, Instituto de Investigación en Ciencias Sociales, Universidad de Harvard, & Universidad Nacional Autónoma de México. (2021). An example reference.
            
            // Only the text before the first point is taken, the rest is ignored.
            $firstPartText = strstr($textWithoutAuthors, '.', true);

            

            //return $authors_array;
            return array('expression' => null, 'value' => $authors_array);

        }

    }