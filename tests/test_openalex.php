<?php
// tests/test_openalex.php
// Tests ONLY the OpenAlex search logic.

require_once __DIR__ . '/src/OpenAlexApiExtended.php';

$citations = [
    // Difficult for Regex, Easy for OpenAlex
    "Deep Learning LeCun Nature 2015",
    "Attention is all you need Vaswani 2017",
    // Nonsense (Should return nothing or low score)
    "This is a random sentence that is not a citation."
];

$api = new OpenAlexApiExtended();

echo "=== TESTING OPENALEX API ===\n";

foreach ($citations as $ref) {
    echo "Ref: $ref\n";
    $result = $api->searchWorks($ref);
    
    if (isset($result['error'])) {
        echo "❌ FAIL: " . $result['error'] . "\n";
    } else {
        echo "✅ FOUND: " . ($result['title'] ?? 'No Title') . "\n";
        echo "   Score: " . ($result['relevance_score'] ?? 'N/A') . "\n";
        echo "   Type: " . ($result['type'] ?? 'N/A') . "\n";
    }
    echo "---------------------------------\n";
}
