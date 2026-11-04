<?php
/* 
* Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

class OpenAlexAPI {
    private string $institutionsUrl = "https://api.openalex.org/institutions?search=";
    private string $worksUrl = "https://api.openalex.org/works/";
    private string $doisUrl = "https://api.openalex.org/works?filter=doi:";

    /**
     * Cantidad máxima de DOIs por lote (chunk) en consultas a OpenAlex.
     */
    public const CHUNK_SIZE = 25;

    /**
     * Timeout de conexión cURL en segundos.
     */
    private int $connectTimeout = 5;

    /**
     * Timeout total de transferencia cURL en segundos.
     */
    private int $timeout = 20;

    /**
     * Realiza una solicitud HTTP GET utilizando cURL con control de timeouts y cabeceras.
     *
     * @param string $url URL de destino.
     * @return string|null Respuesta en texto o null en caso de error.
     */
    protected function httpGet(string $url): ?string {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_USERAGENT => 'docxConverter/1.0',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            error_log("OpenAlexAPI cURL Error: " . curl_error($ch) . " (URL: {$url})");
            curl_close($ch);
            return null;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            error_log("OpenAlexAPI HTTP Error: {$httpCode} (URL: {$url})");
            return null;
        }

        return is_string($response) ? $response : null;
    }

    /**
     * Busca una institución por término de consulta.
     *
     * @param string $query Término o nombre de la institución.
     * @return array Datos de la primera institución coincidente o array con error.
     */
    public function searchInstitution(string $query): array {
        $url = $this->institutionsUrl . urlencode($query);
        $response = $this->httpGet($url);

        if ($response === null || $response === false) {
            return ["error" => "No se pudo conectar a la API"];
        }

        $data = json_decode($response, true);

        if (isset($data['meta']['count']) && $data['meta']['count'] > 0 && isset($data['results'][0])) {
            return [
                "id" => $data['results'][0]['id'] ?? null,
                "ror" => $data['results'][0]['ror'] ?? null,
                "display_name" => $data['results'][0]['display_name'] ?? null,
                "type" => $data['results'][0]['type'] ?? null
            ];
        }

        return ["error" => "No se encontraron instituciones con el término de búsqueda proporcionado"];
    }

    /**
     * Consulta obras en OpenAlex por lista de DOIs con particionamiento (chunking).
     * Divide la lista en bloques de hasta $chunkSize (por defecto 25) para prevenir
     * errores por URLs excesivamente largas y unifica todos los resultados.
     *
     * @param array $dois Lista de DOIs a consultar.
     * @param int $chunkSize Tamaño de cada lote (default: 25).
     * @return string JSON unificado con la estructura {"meta": {"count": N}, "results": [...]}.
     * @throws InvalidArgumentException Si la lista de DOIs está vacía.
     */
    public function searchWorksListWithDoi(array $dois, int $chunkSize = self::CHUNK_SIZE): string {
        if (empty($dois)) {
            throw new InvalidArgumentException("La lista de DOIs no puede estar vacía.");
        }

        // Limpiar y filtrar DOIs vacíos
        $cleanDois = array_values(array_filter(array_map('trim', $dois), fn($d) => !empty($d)));
        if (empty($cleanDois)) {
            throw new InvalidArgumentException("La lista de DOIs no puede estar vacía.");
        }

        // Particionar en bloques de hasta $chunkSize
        $chunks = array_chunk($cleanDois, $chunkSize);
        $allResults = [];

        foreach ($chunks as $chunk) {
            $doiList = implode('|', $chunk);
            $url = $this->doisUrl . urlencode($doiList);
            $response = $this->httpGet($url);

            if ($response !== null) {
                $data = json_decode($response, true);
                if (isset($data['results']) && is_array($data['results'])) {
                    $allResults = array_merge($allResults, $data['results']);
                }
            }
        }

        return json_encode([
            'meta' => [
                'count' => count($allResults)
            ],
            'results' => $allResults
        ]);
    }

    /**
     * Consulta instituciones por nombre y retorna la respuesta cruda JSON.
     *
     * @param string $institution Nombre de la institución.
     * @return string|null Respuesta en formato JSON o null.
     * @throws InvalidArgumentException Si el nombre está vacío.
     */
    public function searchWorksListWithInstitutions(string $institution): ?string {
        if (empty(trim($institution))) {
            throw new InvalidArgumentException("La institución no puede estar vacía.");
        }

        $url = $this->institutionsUrl . urlencode($institution);
        return $this->httpGet($url);
    }
}
