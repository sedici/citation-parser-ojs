<?php
/* 
* Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

/**
 * OpenAlexEnricherInterface
 *
 * Contrato para las estrategias de enriquecimiento bibliográfico JATS
 * a partir de los datos provistos por la API de OpenAlex.
 */
interface OpenAlexEnricherInterface {

    /**
     * Enriquece una referencia construyendo los elementos XML JATS correspondientes.
     *
     * @param \DOMDocument $dom Documento DOM propietario de los nodos.
     * @param array $data JSON de la obra devuelto por OpenAlex (con __authors_formatted).
     * @return \DOMElement[] Lista de elementos DOM a incorporar o reemplazar en <element-citation>.
     */
    public function enrich(\DOMDocument $dom, array $data): array;
}
