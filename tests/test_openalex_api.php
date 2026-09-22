<?php
/* 
* Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once dirname(__DIR__) . '/Printer/OpenAlexApi/OpenAlexApi.php';
require_once dirname(__DIR__) . '/Printer/OpenAlexApi/OpenAlexApiManager.php';

echo "=== TESTING OPENALEX API (cURL & CHUNKING) ===\n\n";

$passed = 0;
$failed = 0;

function assertCondition(bool $condition, string $testName, string $failureMsg = ""): void {
    global $passed, $failed;
    if ($condition) {
        echo "[PASS] {$testName}\n";
        $passed++;
    } else {
        echo "[FAIL] {$testName}" . ($failureMsg ? " -> {$failureMsg}" : "") . "\n";
        $failed++;
    }
}

// -------------------------------------------------------------
// 1. Mock de OpenAlexAPI para verificar Chunking sin depender de la red
// -------------------------------------------------------------
echo "--- 1. Pruebas de Particionamiento (Chunking de 25 DOIs) ---\n";

class MockOpenAlexAPI extends OpenAlexAPI {
    public array $requestedUrls = [];
    public array $chunkCounts = [];

    protected function httpGet(string $url): ?string {
        $this->requestedUrls[] = $url;

        // Extraer los DOIs de la URL para verificar el tamaño del lote
        $parsed = parse_url($url);
        parse_str($parsed['query'] ?? '', $queryParams);
        $filter = $queryParams['filter'] ?? '';
        $doiString = str_replace('doi:', '', $filter);
        $dois = explode('|', $doiString);
        $this->chunkCounts[] = count($dois);

        // Devolver un resultado simulado por cada DOI en el lote
        $results = [];
        foreach ($dois as $doi) {
            $results[] = [
                'id' => 'https://openalex.org/W' . md5($doi),
                'doi' => 'https://doi.org/' . $doi,
                'title' => 'Title for ' . $doi,
            ];
        }

        return json_encode([
            'meta' => ['count' => count($results)],
            'results' => $results
        ]);
    }
}

$mockApi = new MockOpenAlexAPI();

// Generar 60 DOIs sintéticos
$testDois = [];
for ($i = 1; $i <= 60; $i++) {
    $testDois[] = sprintf("10.1000/test.doi.%03d", $i);
}

$jsonResponse = $mockApi->searchWorksListWithDoi($testDois);
$decoded = json_decode($jsonResponse, true);

// Verificar número de peticiones (deben ser 3: 25, 25, 10)
assertCondition(
    count($mockApi->requestedUrls) === 3,
    "Chunking: 60 DOIs se dividen en 3 peticiones HTTP",
    "Se realizaron " . count($mockApi->requestedUrls) . " peticiones"
);

assertCondition(
    $mockApi->chunkCounts === [25, 25, 10],
    "Chunking: Los tamaños de cada lote son exactamente [25, 25, 10]",
    "Obtenido: " . json_encode($mockApi->chunkCounts)
);

// Verificar que los resultados se unificaron
assertCondition(
    isset($decoded['results']) && count($decoded['results']) === 60,
    "Chunking: La respuesta final unifica los 60 resultados",
    "Total resultados obtenidos: " . count($decoded['results'] ?? [])
);

assertCondition(
    isset($decoded['meta']['count']) && $decoded['meta']['count'] === 60,
    "Chunking: El campo meta.count refleja los 60 resultados unificados"
);

// -------------------------------------------------------------
// 2. Validación de Argumentos y Casos Límite
// -------------------------------------------------------------
echo "\n--- 2. Pruebas de Validación de Argumentos ---\n";

$api = new OpenAlexAPI();

// 2.1 Array vacío de DOIs
$exceptionThrown = false;
try {
    $api->searchWorksListWithDoi([]);
} catch (InvalidArgumentException $e) {
    $exceptionThrown = true;
}
assertCondition($exceptionThrown, "Validación: Array vacío de DOIs arroja InvalidArgumentException");

// 2.2 Array con solo strings en blanco
$exceptionBlank = false;
try {
    $api->searchWorksListWithDoi(["   ", ""]);
} catch (InvalidArgumentException $e) {
    $exceptionBlank = true;
}
assertCondition($exceptionBlank, "Validación: Array con solo espacios vacíos arroja InvalidArgumentException");

// 2.3 Institución vacía
$exceptionInst = false;
try {
    $api->searchWorksListWithInstitutions("");
} catch (InvalidArgumentException $e) {
    $exceptionInst = true;
}
assertCondition($exceptionInst, "Validación: Institución vacía arroja InvalidArgumentException");

// -------------------------------------------------------------
// 3. Tolerancia a Fallos de Red en Chunking (Degradación Elegante)
// -------------------------------------------------------------
echo "\n--- 3. Pruebas de Tolerancia a Fallos de Red ---\n";

class FailingChunkMockAPI extends OpenAlexAPI {
    private int $callCount = 0;

    protected function httpGet(string $url): ?string {
        $this->callCount++;
        // El lote 2 falla (simulando timeout o error 500)
        if ($this->callCount === 2) {
            return null;
        }

        return json_encode([
            'results' => [
                ['id' => 'W1', 'doi' => '10.1000/success']
            ]
        ]);
    }
}

$failingApi = new FailingChunkMockAPI();
$failTestDois = array_fill(0, 50, "10.1000/item"); // 2 chunks de 25
$partialResponse = $failingApi->searchWorksListWithDoi($failTestDois);
$partialDecoded = json_decode($partialResponse, true);

assertCondition(
    isset($partialDecoded['results']) && count($partialDecoded['results']) === 1,
    "Tolerancia: Si un lote falla con null, el método no colapsa y devuelve los resultados de los lotes exitosos"
);

// -------------------------------------------------------------
// 4. Pruebas de OpenAlexApiManager
// -------------------------------------------------------------
echo "\n--- 4. Pruebas de OpenAlexApiManager ---\n";

$manager = new OpenAlexApiManager();

// 4.1 Deduplicación de DOIs
$manager->addDoi("10.1234/test");
$manager->addDoi("10.1234/test"); // Duplicado
assertCondition(count($manager->getDois()) === 1, "Manager: Deduplica DOIs repetidos");

// 4.2 Eliminación de DOI
$removed = $manager->removeDoi("10.1234/test");
assertCondition($removed && count($manager->getDois()) === 0, "Manager: Elimina DOI correctamente");

// 4.3 doiRequest con lista vacía devuelve null sin excepciones
$emptyResult = $manager->doiRequest();
assertCondition($emptyResult === null, "Manager: doiRequest() con lista vacía devuelve null limpiamente");

// -------------------------------------------------------------
// 5. Prueba de Conectividad cURL Real a api.openalex.org
// -------------------------------------------------------------
echo "\n--- 5. Prueba de Conectividad cURL Real ---\n";

$liveApi = new OpenAlexAPI();
$liveResponse = $liveApi->searchWorksListWithDoi(['10.7717/peerj.4375']);
$liveDecoded = json_decode($liveResponse, true);

if (isset($liveDecoded['results'][0]['title'])) {
    echo "[PASS] cURL Real: Conexión exitosa a OpenAlex API. Obra: '{$liveDecoded['results'][0]['title']}'\n";
    $passed++;
} else {
    echo "[WARN] cURL Real: No se pudo conectar a OpenAlex (posible falta de acceso a internet o firewall en el entorno).\n";
}

echo "\n=========================================\n";
echo "RESULTADO: {$passed} superadas, {$failed} fallidas.\n";
echo "=========================================\n";

if ($failed > 0) {
    exit(1);
}
