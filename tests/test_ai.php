<?php
// tests/test_ai.php
// Tests Generative AI with BATCH processing.
// REQUIRES: GENAI_API_KEY environment variable.

require_once __DIR__ . '/src/GenerativeAI.php';
require_once __DIR__ . '/src/DatasetLoader.php';

// Load config
$key = getenv('GENAI_API_KEY');
// Default to 'gemini' if key is present but provider is not set
// Default to 'mock' only if key is missing and provider is not set
$defaultProvider = $key ? 'gemini' : 'mock';
$provider = getenv('GENAI_PROVIDER') ?: $defaultProvider; 
$model = getenv('GENAI_MODEL') ?: 'gemini-1.5-flash';

if (!$key && $provider !== 'mock') {
    echo "⚠️  GENAI_API_KEY not set. Switching to MOCK provider.\n";
    $provider = 'mock';
}

echo "=== TESTING GENERATIVE AI (BATCH MODE) ===\n";
echo "Provider: $provider\n";
echo "Model: $model\n";

try {
    $dataset = DatasetLoader::load();
} catch (Exception $e) {
    echo "Error loading dataset: " . $e->getMessage() . "\n";
    exit(1);
}

// Extract just the citation strings
$citations = array_column($dataset, 'citation');
$total = count($citations);
$batchSize = 5; // Start small for testing safely
echo "Total References: $total\n";
echo "Batch Size: $batchSize\n\n";

$ai = new GenerativeAI($provider, $key, $model);

$chunks = array_chunk($citations, $batchSize);
$allResults = [];

echo "Output Directory: tests/ai_output/\n";

foreach ($chunks as $i => $batch) {
    echo "Processing Batch " . ($i + 1) . " (" . count($batch) . " items)...\n";
    
    $start = microtime(true);
    $results = $ai->parseBatch($batch);
    $duration = microtime(true) - $start;
    
    if (isset($results['error'])) {
        echo "❌ FAIL: " . $results['error'] . "\n";
        // Attempt to store error info for the record
        $allResults[] = [
            'batch_index' => $i,
            'error' => $results['error'],
            'inputs' => $batch
        ];
    } else {
        echo "✅ Success (" . count($results) . " items returned) in " . round($duration, 2) . "s\n";
        // Print first result of batch as sample
        if (!empty($results)) {
            echo "   Sample: " . json_encode($results[0]['parsed'] ?? $results[0] ?? 'N/A') . "\n";
        }
        $allResults = array_merge($allResults, $results);
    }
    echo "---------------------------------\n";
    
    // Process only first 2 batches for quick test unless ALL is requested
    if ($i >= 1 && !in_array('--all', $_SERVER['argv'])) {
        echo "Stopping after 2 batches. Run with --all to process everything.\n";
        break;
    }
}

// 4. Save results to unique file
$outputDir = __DIR__ . '/ai_output';
if (!is_dir($outputDir)) mkdir($outputDir, 0777, true);

// Find next index
$files = glob("$outputDir/run_*.json");
$maxId = 0;
foreach ($files as $f) {
    if (preg_match('/run_(\d+)\.json/', basename($f), $matches)) {
        $id = (int)$matches[1];
        if ($id > $maxId) $maxId = $id;
    }
}
$nextId = sprintf('%03d', $maxId + 1);
$filename = "run_{$nextId}.json";
$outputPath = "$outputDir/$filename";

$outputData = [
    'date' => date('Y-m-d H:i:s'),
    'provider' => $provider,
    'model' => $model,
    'total_items' => count($allResults),
    'results' => $allResults
];

file_put_contents($outputPath, json_encode($outputData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\n💾 Saved results to: tests/ai_output/$filename\n";


