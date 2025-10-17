<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
include_once 'TitlePrinter.php';
include_once __DIR__ . '/../validators/AuthorNameStrategy.php';
class JournalPrinter extends TitlePrinter{

    static int $i = 1;

    public function toPlainText(): string{
        return $this->getTitle().$this->getJournal().', '.$this->getEdition().'('.$this->getVolume().'), '.$this->getPages().'.';
    }

    public function getJournal(): string{
        return $this->get('revista');
    }

    public function getEdition(): string{
        return $this->get('nedicio');
    }

    public function getVolume(): string{
        return $this->get('volumen');
    }

    public function getPages(): string{
        return $this->get('paginas');
    }

    public function getFPages(): string{
        return $this->get('fpage');
    }

    public function getLPages(): string{
        return $this->get('lpage');
    }

    public function getArticleNumber(): string{
        return '';//
    }

    public function getSource(): string{
        return $this->getJournal();
    }

    public function createXMLElements(): array {
        $elements = [];
        
        //<source> tag creation
        $sourceElement = $this->createElement('source',$this->getSource());
        $elements[] = $sourceElement;

        //<article-title> tag creation
        $articleTitleElement = $this->createElement('article-title',$this->getTitle());
        $elements[] = $articleTitleElement;

        //<volume> tag creation
        $volumeElement = $this->createElement('volume',$this->getVolume());
        $elements[] = $volumeElement;

        //<issue> tag creation
        $editionElement = $this->createElement('issue',$this->getEdition());
        $elements[] = $editionElement;

        //<fpage> tag creation
        $fpageElement = $this->createElement('fpage',$this->getFPages());
        $elements[] = $fpageElement;

        //<lpage> tag creation
        $lpageElement = $this->createElement('lpage',$this->getLPages());
        $elements[] = $lpageElement;

        return $elements;
    }

    public function enrichment(array $data): array {
        $elements = [];

        // Fecha (en formato plano: year, month, day al nivel de element-citation)
        $publicationDate = $data['publication_date'] ?? null;
        $year = $month = $day = null;
        if ($publicationDate) {
            $dateArray = $this->getPublicationDateArray($publicationDate);
            $year = $dateArray['year'];
            $month = $dateArray['month'];
            $day = $dateArray['day'];
        }

        // Autores
        if (!empty($data['authorships'])) {
            $personGroup = $this->dom->createElement('person-group');
            $personGroup->setAttribute('person-group-type', 'author');

            // original reference authors to guide splitting (same strategy as validation)
            $referenceAuthors = $data['__reference_authors'] ?? [];

            foreach ($data['authorships'] as $authorData) {
                $displayName = $authorData['author']['display_name'] ?? null;
                if (!$displayName) { continue; }

                $split = null;
                // Try to match against each reference author to get deterministic surname + given-names
                foreach ($referenceAuthors as $refAuthor) {
                    $split = AuthorFullNameProcessor::matchReferenceToDisplayName($refAuthor, $displayName);
                    if ($split !== null) { break; }
                }

                $name = $this->dom->createElement('name');
                if ($split !== null) {
                    $name->appendChild($this->dom->createElement('surname', $split['surname']));
                    if (!empty($split['given-names'])) {
                        $name->appendChild($this->dom->createElement('given-names', $split['given-names']));
                    }
                } else {
                    // Fallback: keep prior behavior to avoid data loss
                    $name->appendChild($this->dom->createElement('surname', $displayName));
                }

                $personGroup->appendChild($name);
            }
            $elements[] = $personGroup;
        }

        // Campos simples (plano, como en createXMLElements)
        $simpleFields = [
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'doi' => $data['doi'] ?? null,
            'source' => $data['primary_location']['source']['display_name'] ?? null,
            'issn-l' => $data['primary_location']['source']['issn_l'] ?? null,
            'issn' => $data['primary_location']['source']['issn'][1] ?? null,
            'article-title' => $data['title'] ?? null,
            'volume' => $data['biblio']['volume'] ?? null,
            'issue' => $data['biblio']['issue'] ?? null,
            'fpage' => $data['biblio']['first_page'] ?? null,
            'lpage' => $data['biblio']['last_page'] ?? null,
        ];
        foreach ($simpleFields as $tag => $val) {
            if ($val === null || $val === '') { continue; }
            $elements[] = $this->createElement($tag, (string)$val);
        }

        // ext-link con atributos (si existe)
        $extLink = $data['primary_location']['landing_page_url'] ?? null;
        if ($extLink) {
            $ext = $this->dom->createElement('ext-link', $extLink);
            $ext->setAttribute('ext-link-type', 'uri');
            $ext->setAttribute('xlink:href', $extLink);
            $elements[] = $ext;
        }

        return $elements;
    }


}
