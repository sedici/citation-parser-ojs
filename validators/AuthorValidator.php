<?php

include_once __DIR__ . '/AuthorFullNameProcessor.php';

class AuthorValidator {

    /** 
     * Validate institution data from OpenAlex results
     * @param Array $results Results (information) from OpenAlex institution search for a specific reference
     * @return void
    */
    public function validateInstitutionAsAuthor(Array $openAlexResults, JATSReference $jatsReference, String $institution): bool {
        if ($openAlexResults === null || count($openAlexResults) === 0) {
            $jatsReference->addError("No institution found in OpenAlex for the name \"$institution\". ");
            return false;
        } else if (count($openAlexResults) > 1) {
            return false;
        }

        $displayName = $openAlexResults[0]['display_name'] ?? null; // Institution name from OpenAlex
        $jatsInstitution = $jatsReference->reference->getAuthor()['institution'] ?? null; // Parsed Institution name from original JATS reference

        if (strtolower($displayName) !== strtolower($jatsInstitution)) {  //this condition isn't necessary, but for security we keep it here
            $jatsReference->addError("Specified name \"$displayName\" does not match with OpenAlex data: \"$jatsInstitution\".");
            return false;
        } else {
            $jatsReference->reference->setAuthor('institution', $displayName); //Replace default institution using OpenAlex institution
        }

        return true;
    }   

    /**
     * Validate full name data from OpenAlex results
     * @param Array $openAlexResults Results (information) from OpenAlex DOI search for a specific reference
     * @return bool
     */
    public function validateFullNameAsAuthor(array $openAlexResults, JATSReference $jatsReference): bool {
        $authorships = $openAlexResults['authorships'] ?? null;
        $referenceAuthors = $jatsReference->reference->getAuthor()['authors'] ?? null;

        if (empty($referenceAuthors)) {
            $jatsReference->addError("No authors found in the reference.");
            return false;
        }

        $allMatched = true;

        // For each reference author, we try to find them in the authorships returned by OpenAlex
        foreach ($referenceAuthors as $referenceAuthor) {
            $matchFound = false;
            foreach ($authorships as $authorship) {
                $openAlexFullName = $authorship['author']['display_name'] ?? null;
                if ($openAlexFullName === null) continue;

                $split = AuthorFullNameProcessor::matchReferenceToDisplayName($referenceAuthor, $openAlexFullName);
                if ($split !== null) {
                    $matchFound = true;
                    break; // don't need to keep looking for this author
                }
            }

            if (!$matchFound) {
                $allMatched = false;
                $doi = $openAlexResults['doi'];
                $surname = $referenceAuthor['apellido'];
                $names = $referenceAuthor['nombres'];
                $jatsReference->addError("Author '{$surname}, {$names}' not found in OpenAlex data for DOI {$doi}.");
            }
        }

        return $allMatched;
    }

    /**
     * Search for approximate matches between the reference author and the OpenAlex display_name
     */
    private function findMatchByFullName(JATSReference $jatsReference, array $referenceAuthor, string $anOpenAlexFullName): bool {
    $split = AuthorFullNameProcessor::matchReferenceToDisplayName($referenceAuthor, $anOpenAlexFullName);
        if ($split === null) {
            $authorSurname = $referenceAuthor['apellido'] ?? '';
            $authorGivenNames = $referenceAuthor['nombres'] ?? '';
            $jatsReference->addError("Author '{$authorSurname}, {$authorGivenNames}' does not match OpenAlex name '{$anOpenAlexFullName}'.");
            return false;
        }
        return true;
    }
}