<?php

/* 
 * Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
 * Licensed under GPLv3: see LICENSE file for details.
 */

class IeeeTitlePatterns {

    /**
     * Citation numbering prefix (e.g. "[1]", "[12]")
     */
    public const CITATION_NUMBER_REGEX = '/^\s*\[(\d+)\]\s*/u';

    /**
     * Title enclosed in quotation marks (English, Spanish, curly or straight quotes)
     */
    public const TITLE_QUOTED_REGEX = '/["“«]([^"”»]+)["”»][,\.]?\s*/u';

    /**
     * Volume, issue, pages, and elocation metadata
     */
    public const VOLUME_REGEX = '/\bvol\.\s*([a-zA-Z0-9]+)/i';
    public const ISSUE_REGEX = '/\b(?:no|n[oº]|issue)\.\s*([a-zA-Z0-9]+)/iu';
    public const PAGES_REGEX = '/\bpp?\.?\s*([a-zA-Z]?\d+)(?:\s*[-–—]\s*([a-zA-Z]?\d+))?/iu';
    public const ELOCATION_REGEX = '/\b(?:art\.\s*(?:no\.)?|article\s*(?:no\.)?|elocation-id:?\s*)\s*([a-zA-Z0-9_-]+)/i';
    public const EDITION_REGEX = '/\b(\d+)(?:st|nd|rd|th|a|da|ra|ta)?\s+ed\b/i';

    /**
     * Publication type detection keywords
     */
    public const TYPE_CONFERENCE_REGEX = '/\b(?:in\s+proc|proc|proceedings|conference|congreso|symposium|simposio|workshop|conf)(?:\.|\b)/iu';
    public const TYPE_THESIS_REGEX = '/\b(?:ph\.?d\.?\s*(?:dissertation|thesis)|m\.?s\.?\s*thesis|master(?:\'s)?\s*thesis|tesis\s*(?:doctoral|de\s*maestr[ií]a|de\s*grado|de\s*licenciatura|fin\s*de\s*m[aá]ster)|trabajo\s*fin\s*de\s*(?:m[aá]ster|grado))\b/iu';
    public const THESIS_DEGREE_REGEX = '/\b(Ph\.?D\.?\s*dissertation|M\.?S\.?\s*thesis|Master(?:\'s)?\s*thesis|Tesis\s*(?:doctoral|de\s*maestr[ií]a|de\s*grado|de\s*licenciatura|fin\s*de\s*m[aá]ster))\b/iu';
    public const CHAPTER_INDICATOR = '/\b(?:in|en)\s+(?:e-book|book|livro|libro)\b/i';

    /**
     * Publisher & City in books: "City, Country: Publisher, Year"
     */
    public const BOOK_PUBLISHER_REGEX = '/(?:\.|\,)\s*([A-ZÁÉÍÓÚ][\p{L}\s\.,\-]+?)\s*:\s*([A-ZÁÉÍÓÚ][\p{L}0-9\s\.\-&]+?)(?:,\s*(?:19\d{2}|20\d{2})|\.|$)/u';
}
