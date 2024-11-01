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
            // Smith, J. A., Johnson, M. L., Universidad Nacional de La Plata. (2021). An example title.
            
            preg_match_all($this->authorPattern, $text, $authorsMatches, PREG_SET_ORDER);

            $authors_array = array();
            $counter = 1;
            
            foreach ($authorsMatches as $match) {
                $authors_array['authors']['author' . $counter] = [
                    'apellido' => $match['apellido'],
                    'nombres' => $match['nombres'],
                    'role' => $match['role'] ?? '',
                ];
                $counter++;
            }

            //Names and surnames in the reference text are removed:
            $textWithoutAuthors = preg_replace($this->authorPattern, '', $text);
            // Reference ($text) at this moment:
            // Universidad Nacional de La Plata. (2021). An example title.
            
            // Only the text before the first point, space and parenthesis are taken, the rest is ignored.
            $firstPartText = strstr($textWithoutAuthors, '. (', true);

            //Checking if the institution exists in the reference: 
            if (!empty($firstPartText)) {
                //Add 'institution' key in $authorsArray. This array will contains 'authors' and 'institution' key. 
                preg_match($this->institutionPattern, $firstPartText, $institutionMatch);
                $authors_array['institution'] = $institutionMatch['institution'];
            }

            //Return only $authors_array if the reference does not contain institutions as authors.
            return array('expression' => null, 'value' => $authors_array);
            
        }

    }