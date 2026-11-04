<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
class WebpagePrinter extends TitlePrinter {

    public function createXMLElements(): array {
        
        $elements = [];
        if ($this->getArticleTitle()) {
            $articleTitleElement = $this->createElement('article-title', $this->getArticleTitle());
            $elements[] = $articleTitleElement;
        }
        if ($this->getSource()) {
            $sourceElement = $this->createElement('source', $this->getSource());
            $elements[] = $sourceElement;
        }

        if ($this->getCitationDay()) {
            $dayElement = $this->createElement('day', $this->getCitationDay());
            $elements[] = $dayElement;
        }

        if ($this->getCitationMonth()) {
            $monthElement = $this->createElement('month', $this->getCitationMonth());
            $elements[] = $monthElement;
        }

        if ($this->getCitationYear()) {
            $yearElement = $this->createElement('year', $this->getCitationYear());
            $elements[] = $yearElement;
        }

        if ($this->getCitationYear() && $this->getCitationMonth() && $this->getCitationDay()) {
            $dateInCitationText = $this->getCitationYear() . '-' . $this->getCitationMonth() . '-' . $this->getCitationDay();
            $dateInCitationElement = $this->createElement('date-in-citation', $dateInCitationText);
            $dateInCitationElement->setAttribute('iso-8601-date', $this->getDateInCitation());
        }

        return $elements;
    }

    public function getSource(){
        return $this->get('source');
    }

    public function toPlainText(): string{
        return "";
    }

    public function getDateInCitation(){
        return $this->get('date_in_citation');
    }

    public function getCitationDay() {
        return $this->get('citation_day');
    }

    public function getCitationMonth() {
        return $this->get('citation_month');
    }

    public function getCitationYear() {
        return $this->get('citation_year');
    }

    public function getArticleTitle(){
        return $this->get('article_title');
    }

}