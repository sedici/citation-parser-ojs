<?php

/* 
 * Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
 * Licensed under GPLv3: see LICENSE file for details.
 */

interface ReferenceStrategyInterface {
    /**
     * Parses a plain-text reference string according to the citation style.
     *
     * @param string $plainTextReference The raw reference text.
     * @return array {
     *     author: array,
     *     authorType: ?string,
     *     date: array,
     *     dateType: ?string,
     *     url: array,
     *     urlType: ?string,
     *     title: array,
     *     titleType: ?string,
     *     type: mixed
     * }
     */
    public function parse(string $plainTextReference): array;
}
