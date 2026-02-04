<?php
// tests/test_regex.php
// Tests ONLY the Regex-based Parser.

require_once dirname(__DIR__) . '/Expression/Parser.php';
require_once __DIR__ . '/src/DatasetLoader.php';

try {
    $dataset = DatasetLoader::load();
} catch (Exception $e) {
    echo "Error loading dataset: " . $e->getMessage() . "\n";
    exit(1);
}

echo "=== TESTING REGEX PARSER (Dataset: " . count($dataset) . " items) ===\n\n";

foreach ($dataset as $index => $data) {
    $ref = $data['citation'];
    echo "[$index] Ref: " . substr($ref, 0, 80) . "...\n";
    
    $result = Parser::parse($ref);
    
    // Check if expected type matches expression found
    $expectedType = $data['type'] ?? 'unknown';
    $foundType = $result['expression'] ?? 'none';
    
    // Simple mapping check (Regex uses 'journal', 'book', 'chapter', 'thesis')
    // Dataset uses CSL types 'article-journal', 'book', 'chapter', 'thesis' mostly.
    
    $status = "❌";
    if ($foundType === 'journal' && $expectedType === 'article-journal') $status = "✅";
    elseif ($foundType === $expectedType) $status = "✅";
    elseif ($foundType === 'No match found') $status = "❌ (No match)";
    
    echo "Result: $status Found: $foundType | Expected: $expectedType\n";
    if ($foundType !== 'No match found') {
       // print_r($result['value']); // Too verbose for 100 items
    }
    echo "---------------------------------\n";
}
