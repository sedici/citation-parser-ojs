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

            // Example reference ($text) at this moment:
            // Smith, J. A., Johnson, M. L., Universidad Nacional de La Plata (2021). An example title.

            // Only the text before the closed parenthesis are taken, the rest is ignored.
            $firstPartText = strstr($text, ')', true);

            // Match using the author's regex pattern.
            // Example reference ($firstPartText) at this moment: 
            // Smith, J. A., Johnson, M. L., Universidad Nacional de La Plata (2021
            preg_match_all($this->authorPattern, $firstPartText, $authorsMatches, PREG_SET_ORDER);

            if (empty($authorsMatches)) {
                return array('expression' => null, 'value' => '');
            }

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

            //Names and surnames in the reference text are removed because i need to match the institution (only if exists):
            $textWithoutAuthors = preg_replace($this->authorPattern, '', $firstPartText);
            // Text $textWithoutAuthors at this moment: "Universidad Nacional de La Plata (2021". We need to delete the last part: ". (2021"
            $textWithoutAuthors = strstr($textWithoutAuthors, '. (', true);
            // Text $textWithoutAuthors at this moment: "Universidad Nacional de La Plata"

            //Checking if the institution exists in the reference: 
            if (!empty($textWithoutAuthors)) {
                //Add 'institution' key in $authorsArray. This array will contains 'authors' and 'institution' key.
                //Verifying with the regex if the institution respects a valid format: 
                preg_match($this->institutionPattern, $textWithoutAuthors, $institutionMatch);
                $authors_array['institution'] = $institutionMatch['institution'];
            }

            //Return only $authors_array if the reference does not contain institutions as authors.
            return array('expression' => 'authors', 'value' => $authors_array);
            
        }

    }