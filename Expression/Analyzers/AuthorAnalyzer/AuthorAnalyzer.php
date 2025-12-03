<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

include_once('AuthorPatterns.php');

class AuthorAnalyzer {

    private $authorPattern;
    private $institutionPattern;

    public function __construct(){
        $this->authorPattern = AuthorPatterns::getAuthorPattern();
        $this->institutionPattern = AuthorPatterns::getInstitutionPattern();
    }

    /**
     * Analyzes the given text to extract authors name and surname or institution as authors information.
     *
     * @param string $text The text to analyze.
     * @return array An associative array with 'expression' and 'value' keys.
     */
    public function analyze(string $text){
        $firstPartText = strstr($text, ')', true);
        if (!$firstPartText) {
            return ['expression' => null, 'value' => ''];
        }
        $authorsData = $this->extractAuthors($firstPartText);
        if (empty($authorsData['authors'])) {
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
    
    /** Extracts institution from the given text.
     *
     * @param string $text The text to extract institution from.
     * @return string The extracted institution or an empty string if not found.
     */
    private function extractInstitution(string $text) {
        preg_match($this->institutionPattern, $text, $institutionMatch);
        return !empty($institutionMatch['institution']) ? $institutionMatch['institution'] : '';
    }
}