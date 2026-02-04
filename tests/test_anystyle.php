<?php
// tests/test_anystyle.php
// Tests ONLY the AnyStyle CLI logic.
// REQUIREMENT: ruby installed and `anystyle-cli` gem.
// RUN: sudo apt-get install ruby-dev && gem install anystyle-cli --user-install

require_once __DIR__ . '/src/AnyStyle.php';

require_once __DIR__ . '/src/DatasetLoader.php';

try {
    $dataset = DatasetLoader::load();
} catch (Exception $e) {
    echo "Error loading dataset: " . $e->getMessage() . "\n";
    exit(1);
}

$as = new AnyStyle();

echo "=== EXTENSIVE ANYSTYLE BENCHMARK (Dataset: " . count($dataset) . " items) ===\n\n";

if (!$as->isAvailable()) {
    echo "⚠️ AnyStyle CLI not found.\n";
    exit(1);
}

$passed = 0;
$total = count($dataset);

foreach ($dataset as $index => $data) {
    echo "[" . ($index + 1) . "/$total] Processing ID: " . ($data['id'] ?? 'unknown') . "\n";
    echo "IN : " . substr($data['citation'], 0, 80) . "...\n";
    
    $result = $as->parse($data['citation']);
    $expected = $data['expected'];
    
    // Normalize Result for Comparison (Flatten arrays if needed)
    $flatResult = [];
    $flatResult['type'] = $result['type'] ?? 'unknown';
    $flatResult['title'] = $result['title'][0] ?? '';
    // Date cleaning (AnyStyle returns array like ['2020'])
    $flatResult['date'] = $result['date'][0] ?? '';
    $flatResult['container-title'] = $result['container-title'][0] ?? '';
    $flatResult['publisher'] = $result['publisher'][0] ?? '';

    // Check matches
    $matches = [];
    $isPass = true;
    
    // 1. Title Fuzzy Check
    if (stripos($flatResult['title'], $expected['title']) !== false) {
        $matches[] = "✅ Title";
    } else {
        $matches[] = "❌ Title (Got: '{$flatResult['title']}')";
        $isPass = false;
    }

    // 2. Date Check
    $expectedDate = $expected['date'] ?? null;
    if ($expectedDate && stripos($flatResult['date'], $expectedDate) !== false) {
        $matches[] = "✅ Date";
    } elseif ($expectedDate) {
         $matches[] = "❌ Date (Got: '{$flatResult['date']}')";
         $isPass = false;
    }

    // 3. Type Loose Check (book/chapter logic is fuzzy in AnyStyle)
    // Map AnyStyle types to expectations
    // anyStyle: article-journal, book, chapter, etc.
    if (isset($expected['type'])) {
         if ($flatResult['type'] === $expected['type']) {
             $matches[] = "✅ Type";
         } else {
             // Accept 'chapter' output for 'article-journal' sometimes happens in headers
             // But let's log the mismatch.
             $matches[] = "❌ Type (Exp: {$expected['type']} | Got: {$flatResult['type']})";
             $isPass = false;
         }
    }

    if ($isPass) $passed++;

    echo "OUT: Type=[{$flatResult['type']}] Title=[{$flatResult['title']}] Date=[{$flatResult['date']}]\n";
    echo "RES: " . implode("  ", $matches) . "\n";
    echo "------------------------------------------------------------\n";
}

echo "\n============================\n";
echo "BENCHMARK RESULTS\n";
echo "Total:    $total\n";
echo "Correct:  $passed\n";
$accuracy = ($total > 0) ? round(($passed / $total) * 100, 2) : 0;
echo "Accuracy: $accuracy%\n";
echo "============================\n";

// -------------------------------------------------------------------------
// OUTPUT GENERATION (Optional)
// -------------------------------------------------------------------------
// Check CLI args for --generate-files
$args = $_SERVER['argv'] ?? [];
$genFiles = in_array('--generate-files', $args);

if ($genFiles) {
    echo "\n=== GENERATING OUTPUT FILES ===\n";
    $outputDir = __DIR__ . '/anystyle_output';
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    echo "Directory: tests/anystyle_output/\n";

    // 1. Consolidate all citations
    $allCitations = "";
    foreach ($dataset as $data) {
        $allCitations .= $data['citation'] . "\n";
    }

    $formats = ['bib', 'csl', 'xml', 'json'];

    foreach ($formats as $fmt) {
        echo "Generating .$fmt... ";
        $output = $as->parseRaw($allCitations, $fmt);
        $filename = "$outputDir/citations.$fmt";
        file_put_contents($filename, $output);
        echo "Saved (" . strlen($output) . " bytes)\n";
    }
    echo "Done.\n";
} else {
    echo "\nTip: Run with --generate-files to create .bib, .xml, .json outputs in tests/anystyle_output/\n";
}

