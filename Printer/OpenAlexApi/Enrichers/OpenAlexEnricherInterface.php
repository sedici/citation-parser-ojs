<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

interface OpenAlexEnricherInterface {
    /**
     * Translates OpenAlex API response data into JATS XML DOM elements.
     *
     * @param \DOMDocument $dom The JATS DOMDocument context
     * @param array $data OpenAlex work result item
     * @return \DOMElement[] Array of created JATS XML DOMElements
     */
    public function enrich(\DOMDocument $dom, array $data): array;
}
