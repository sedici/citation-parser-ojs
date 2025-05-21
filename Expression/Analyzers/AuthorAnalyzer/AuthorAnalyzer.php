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
        // Extract text before the closing parenthesis
        $firstPartText = strstr($text, ')', true);
        if (!$firstPartText) {
            return ['expression' => null, 'value' => ''];
        }

        // Extract authors
        $authorsData = $this->extractAuthors($firstPartText);

        // Check if we have authors
        if (empty($authorsData['authors'])) {
            // No authors found, try to find an institution as the primary entity
            $institution = $this->extractInstitution($firstPartText);

            if (strstr($institution, '(', true)) {
                $cleanInstitution = trim(strstr($institution, '(', true));
            }

            if (!empty($cleanInstitution)) {
                return [
                    'expression' => 'institution', 
                    'value' => ['institution' => $cleanInstitution]
                ];
            }
            return ['expression' => null, 'value' => ''];
        }
        
        // If we have authors, also check for additional institution. For example: "Carl, J., Smith, A. Universidad Nacional de La Plata (2005)"
        // This reference has authors and an institution, not only an institution or authors.
        // We need to remove the authors from the text to find the institution. 
        $textWithoutAuthors = preg_replace($this->authorPattern, '', $firstPartText);
        if (strstr($textWithoutAuthors, '. (', true)) {
            $textWithoutAuthors = strstr($textWithoutAuthors, '. (', true);
        }
        
        if (!empty(trim($textWithoutAuthors))) {
            //Check if the institution text is valid
            // If the institution text is valid, we can extract the institution
            // and add it to the authors data.
            // If the institution text is not valid, we can ignore it.
        
            $institution = $this->extractInstitution($textWithoutAuthors);
            $institutionClean = preg_replace('/\s*\(.*$/', '', $institution);

            if (!empty($institutionClean)) {
                $authorsData['institution'] = trim($institutionClean);
            }
        }
        
        return ['expression' => 'authors', 'value' => $authorsData];
    }
    
    private function extractAuthors(string $text) {
        $result = [];
        preg_match_all($this->authorPattern, $text, $authorsMatches, PREG_SET_ORDER);
        
        if (empty($authorsMatches)) {
            return $result;
        }
        
        $counter = 1;
        foreach ($authorsMatches as $match) {
            $result['authors']['author' . $counter] = [
                'apellido' => $match['apellido'],
                'nombres' => $match['nombres'],
                'role' => $match['role'] ?? '',
            ];
            $counter++;
        }
        
        return $result;
    }
    
    private function extractInstitution(string $text) {
        preg_match($this->institutionPattern, $text, $institutionMatch);
        return !empty($institutionMatch['institution']) ? $institutionMatch['institution'] : '';
    }
}

// Si no tengo autores, entonces debo evaluar si tengo una institucion. En caso de tener institución, se devuelve la institucion.  
// Si no tengo autores ni institucion, se devuelve null.
// Si tengo autores, debo evaluar si tengo una institucion, ya que puedo haber ademas de autores, una institucion.
// En caso de tener autores y una institucion, se devuelven ambos en el mismo array $authorsData.
// Si tengo autores y no tengo institucion, se devuelven solo los autores.