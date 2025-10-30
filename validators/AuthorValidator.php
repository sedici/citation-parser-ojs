<?php

include_once __DIR__ . '/AuthorFullNameProcessor.php';

class AuthorValidator {

    /** 
     * Validate institution data from OpenAlex results
     * @param array $results Results (information) from OpenAlex institution search for a specific reference
     * @return void
    */
    public function validateInstitutionAsAuthor(array $openAlexResults, JATSReference $jatsReference, string $institution): bool {
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
     * @param array $openAlexResults Results (information) from OpenAlex DOI search for a specific reference
     * @return bool
     */
    public function validateFullNameAsAuthor(array $openAlexResults, JATSReference $jatsReference): bool {
        $authorships = $openAlexResults['authorships'] ?? [];
        $referenceAuthors = $jatsReference->reference->getAuthor()['authors'] ?? [];

        // Si la referencia no tiene autores, no bloquear la creación ni validar
        if (empty($referenceAuthors)) { return true; }

        // Si hay autores en la referencia pero OpenAlex no devolvió authorships, reportar
        if (empty($authorships)) {
            $doi = $openAlexResults['doi'] ?? '';
            $jatsReference->addError("OpenAlex did not return authors for DOI {$doi}.");
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
                $doi = $openAlexResults['doi'] ?? '';
                $surname = $referenceAuthor['apellido'];
                $names = $referenceAuthor['nombres'];
                $jatsReference->addError("Author '{$surname}, {$names}' not found in OpenAlex data for DOI {$doi}.");
            }
        }

        return $allMatched;
    }

    /**
     * Build a normalized list of authors to be printed by printers, avoiding re-processing later.
     * Strategy:
     * - If the reference has authors, try to split OpenAlex display_name using AuthorFullNameProcessor
     *   against each reference author. If split succeeds, use surname + given-names; otherwise fallback
     *   to surname with full display_name.
     * - If the reference has NO authors (DOI-only case), do not attempt splitting; fallback to surname=display_name.
     *
     * @return array Each item: ['surname' => string, 'given-names' => string|null]
     */
    public function buildFormattedAuthors(array $openAlexResults, JATSReference $jatsReference): array {
        $formatted = [];
        $authorships = $openAlexResults['authorships'] ?? [];
        $referenceAuthors = $jatsReference->reference->getAuthor()['authors'] ?? [];

        if (empty($authorships)) { return $formatted; }

        foreach ($authorships as $authorship) {
            $displayName = $authorship['author']['display_name'] ?? null;
            if (!$displayName) { continue; }

            $entry = ['surname' => $displayName, 'given-names' => null];

            if (!empty($referenceAuthors)) {
                foreach ($referenceAuthors as $refAuthor) {
                    $split = AuthorFullNameProcessor::matchReferenceToDisplayName($refAuthor, $displayName);
                    if ($split !== null) {
                        $entry = [
                            'surname' => $split['surname'] ?? $displayName,
                            'given-names' => $split['given-names'] ?? null,
                        ];
                        break;
                    }
                }
            }

            $formatted[] = $entry;
        }

        return $formatted;
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