<?php

    //This class contains the SPANISH keywords to determine if a reference is a thesis or a conference. 
    class Es_Keywords {

        public static function getEsKeywords() {
            $esThesisKeywords = [',', 'tesis', 'doctoral', 'maestria', 'trabajo final', 'disertacion', 'tesina', 'doctorado', 'grado', 'tecnicatura', 'posgrado'];
            $esCongressKeywords = ['congreso', 'simposio', 'conferencia', 'presentacion', 'ponencia', 'diapositiva de power point', 'resumen', 'discurso', 'comunicado'];

            $arrayKeywords = [
                'thesis' => $esThesisKeywords,
                'congress' => $esCongressKeywords
            ];

            return $arrayKeywords;
        }

    }