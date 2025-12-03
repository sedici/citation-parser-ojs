<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
abstract class Expression {

    /**
     * Strategy method to parse a reference string.
     * @param string $reference The reference string to parse.
     * @return array Parsed components of the reference.
     */
    public abstract static function parse(string $reference);
}