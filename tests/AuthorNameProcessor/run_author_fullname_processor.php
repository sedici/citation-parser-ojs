<?php

/*
 Simple runner for AuthorFullNameProcessor test cases.
 Usage: php tests/run_author_fullname_processor.php
*/

require_once __DIR__ . '/../../validators/AuthorFullNameProcessor.php'; // contains class AuthorFullNameProcessor

function color($text, $code) { return "\033[{$code}m{$text}\033[0m"; }

function build_name_xml_string(string $surname, ?string $givenNames): string {
    $dom = new \DOMDocument('1.0', 'UTF-8');
    $name = $dom->createElement('name');
    $name->appendChild($dom->createElement('surname', $surname));
    if ($givenNames !== null && $givenNames !== '') {
        $name->appendChild($dom->createElement('given-names', $givenNames));
    }
    $dom->appendChild($name);
    $dom->formatOutput = false;
    // Strip XML declaration for inline print
    $xml = $dom->saveXML($name);
    return $xml ?: '';
}

$dataFile = __DIR__ . '/data/authors_cases.json';
if (!file_exists($dataFile)) {
    fwrite(STDERR, "Test data not found: {$dataFile}\n");
    exit(1);
}

$cases = json_decode(file_get_contents($dataFile), true);
if (!is_array($cases)) {
    fwrite(STDERR, "Invalid JSON in {$dataFile}\n");
    exit(1);
}

$total = count($cases);
$passed = 0;

// Prepare consolidated examples XML
$examplesDom = new \DOMDocument('1.0', 'UTF-8');
$examplesDom->formatOutput = true;
$root = $examplesDom->createElement('examples');
$examplesDom->appendChild($root);

foreach ($cases as $idx => $c) {
    $label = $c['label'] ?? "case #{$idx}";
    $ref = $c['referenceAuthor'] ?? [];
    $display = $c['displayName'] ?? '';
    $expectMatch = (bool)($c['expectMatch'] ?? false);
    $expectSurname = $c['expectSurname'] ?? null;
    $expectGiven = $c['expectGiven'] ?? null;

    $result = AuthorFullNameProcessor::matchReferenceToDisplayName($ref, $display);
    $isMatch = $result !== null;

    $ok = true;
    if ($isMatch !== $expectMatch) {
        $ok = false;
    }
    if ($ok && $isMatch && $expectSurname !== null && ($result['surname'] ?? null) !== $expectSurname) {
        $ok = false;
    }
    if ($ok && $isMatch && $expectGiven !== null && ($result['given-names'] ?? null) !== $expectGiven) {
        $ok = false;
    }

    $status = $isMatch ? 'Matchea' : 'No matchea';
    if ($ok) {
        $passed++;
        echo color("[PASS] ", '32') . $label . " — {$status}";
        if ($isMatch) {
            $surnameOut = $result['surname'] ?? '';
            $givenOut = $result['given-names'] ?? '';
            echo " — surname=" . $surnameOut . ", given-names=" . $givenOut;
            // Build and show JATS <name>
            $inlineXml = build_name_xml_string($surnameOut, $givenOut);

            // Append to consolidated examples
            $ex = $examplesDom->createElement('example');
            $ex->setAttribute('label', $label);
            $nameEl = $examplesDom->createElement('name');
            $surEl = $examplesDom->createElement('surname');
            $surEl->appendChild($examplesDom->createTextNode($surnameOut));
            $nameEl->appendChild($surEl);
            if ($givenOut !== '') {
                $givEl = $examplesDom->createElement('given-names');
                $givEl->appendChild($examplesDom->createTextNode($givenOut));
                $nameEl->appendChild($givEl);
            }
            $ex->appendChild($nameEl);
            $root->appendChild($ex);
        }
        echo "\n";
    } else {
        echo color("[FAIL] ", '31') . $label . " — {$status}\n";
        echo "  reference: " . json_encode($ref, JSON_UNESCAPED_UNICODE) . "\n";
        echo "  display  : {$display}\n";
        echo "  expected : match=" . ($expectMatch ? 'true' : 'false');
        if ($expectSurname !== null) { echo ", surname={$expectSurname}"; }
        if ($expectGiven !== null) { echo ", given-names={$expectGiven}"; }
        echo "\n";
        echo "  got      : match=" . ($isMatch ? 'true' : 'false');
        if ($isMatch) { echo ", surname=" . ($result['surname'] ?? ''); echo ", given-names=" . ($result['given-names'] ?? ''); }
        echo "\n\n";
    }
}

$pct = $total > 0 ? round(($passed / $total) * 100, 1) : 0;
echo "Summary: {$passed}/{$total} passed ({$pct}%)\n";

// Write consolidated examples file
$outDir = __DIR__ . '/output';
if (!is_dir($outDir)) { @mkdir($outDir, 0777, true); }
$outFile = $outDir . '/jats_names_examples.xml';
$examplesDom->save($outFile);
echo "Examples written to: {$outFile}\n";

exit($passed === $total ? 0 : 2);

?>
