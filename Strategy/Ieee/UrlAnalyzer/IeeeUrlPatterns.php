<?php

/* 
 * Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
 * Licensed under GPLv3: see LICENSE file for details.
 */

class IeeeUrlPatterns {

    /**
     * Pattern matching Digital Object Identifier (DOI) in any standard representation
     */
    public const DOI_REGEX = '#(?:doi:\s*|https?://(?:dx\.)?doi\.org/)(10\.\d{4,9}/[-._;()/:A-Za-z0-9]+)#i';

    /**
     * Pattern matching IEEE online resource markers with available URL
     */
    public const ONLINE_AVAILABLE_REGEX = '#\[(?:Online|En\s*l[ií]nea)\]\.?\s*(?:Available|Disponible)(?:\s*(?:at|en))?:?\s*(https?://[^\s,"\'<>]+)#iu';

    /**
     * Pattern matching generic HTTP/HTTPS URLs
     */
    public const GENERIC_URL_REGEX = '#https?://[^\s,"\'<>]+#i';

    /**
     * Pattern matching online bracketed annotations for cleaning
     */
    public const ONLINE_ANNOTATION_REGEX = '/\[(?:Online Video|Online|En\s*l[ií]nea)\]\.?\s*/iu';

    /**
     * Pattern matching trailing URL at the end of the citation string
     */
    public const TRAILING_URL_REGEX = '#https?://[^\s,"\'<>]+[.,;]?\s*$#i';
}
