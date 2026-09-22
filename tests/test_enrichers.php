<?php
/* 
* Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once dirname(__DIR__) . '/Enricher/OpenAlexEnricherFactory.php';
require_once dirname(__DIR__) . '/JATSReference.php';
require_once dirname(__DIR__) . '/Reference.php';

echo "=== TESTING OPENALEX ENRICHERS & FACTORY ===\n\n";

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
// 1. Tests de Resolución de la Fábrica (OpenAlexEnricherFactory)
// -------------------------------------------------------------
echo "--- 1. Pruebas de OpenAlexEnricherFactory ---\n";

$casesFactory = [
    'journal' => [
        'payload' => ['type' => 'article', 'primary_location' => ['source' => ['type' => 'journal']]],
        'expectedClass' => 'JournalOpenAlexEnricher'
    ],
    'book' => [
        'payload' => ['type' => 'book', 'primary_location' => ['source' => ['type' => null]]],
        'expectedClass' => 'BookOpenAlexEnricher'
    ],
    'ebook' => [
        'payload' => ['type' => 'book', 'primary_location' => ['source' => ['type' => 'ebook platform']]],
        'expectedClass' => 'BookOpenAlexEnricher'
    ],
    'chapter' => [
        'payload' => ['type' => 'book-chapter', 'primary_location' => ['source' => ['type' => 'book series']]],
        'expectedClass' => 'ChapterOpenAlexEnricher'
    ],
    'confproc' => [
        'payload' => ['type' => 'proceedings-article', 'primary_location' => ['source' => ['type' => 'conference']]],
        'expectedClass' => 'ConfprocOpenAlexEnricher'
    ],
    'thesis' => [
        'payload' => ['type' => 'dissertation', 'primary_location' => ['source' => ['type' => 'repository']]],
        'expectedClass' => 'ThesisOpenAlexEnricher'
    ],
    'generic_fallback' => [
        'payload' => ['type' => 'unrecognized_type_xyz', 'primary_location' => null],
        'expectedClass' => 'GenericOpenAlexEnricher'
    ]
];

foreach ($casesFactory as $name => $test) {
    $enricher = OpenAlexEnricherFactory::getEnricher($test['payload']);
    $className = get_class($enricher);
    assertCondition(
        $className === $test['expectedClass'],
        "Factory resuelve {$name} a {$test['expectedClass']}",
        "Obtenido: {$className}"
    );
}

// -------------------------------------------------------------
// 2. Test de Registro Dinámico (Extensibilidad OCP)
// -------------------------------------------------------------
echo "\n--- 2. Pruebas de Registro Dinámico (Open/Closed Principle) ---\n";

class CustomTestEnricher extends BaseOpenAlexEnricher {
    public function enrich(\DOMDocument $dom, array $data): array {
        $elements = $this->buildCommonElements($dom, $data);
        $elements[] = $this->createElement($dom, 'custom-tag', 'CustomValue');
        return $elements;
    }
}

OpenAlexEnricherFactory::registerEnricher('custom-resource', 'CustomTestEnricher');
$customEnricher = OpenAlexEnricherFactory::getEnricher(['type' => 'custom-resource']);
assertCondition(
    $customEnricher instanceof CustomTestEnricher,
    "Registro dinámico: Factory resuelve 'custom-resource' a CustomTestEnricher"
);

// Probar que el custom enricher genere el tag
$testDom = new \DOMDocument('1.0', 'UTF-8');
$customElements = $customEnricher->enrich($testDom, ['title' => 'Custom Title']);
$hasCustomTag = false;
foreach ($customElements as $el) {
    if ($el->tagName === 'custom-tag' && $el->nodeValue === 'CustomValue') {
        $hasCustomTag = true;
    }
}
assertCondition($hasCustomTag, "CustomTestEnricher genera <custom-tag>CustomValue</custom-tag>");

OpenAlexEnricherFactory::clearRegistry();

// -------------------------------------------------------------
// 3. Tests de Estrategias Concretas de Enriquecimiento
// -------------------------------------------------------------
echo "\n--- 3. Pruebas de Estrategias Concretas ---\n";

// Helper para convertir array de DOMElements a mapa de [tag => nodeValue] o [tag => DOMElement]
function elementsToMap(array $elements): array {
    $map = [];
    foreach ($elements as $el) {
        $map[$el->tagName] = $el;
    }
    return $map;
}

$dom = new \DOMDocument('1.0', 'UTF-8');

// 3.1 JournalOpenAlexEnricher
$journalData = [
    'title' => 'Advances in Computer Science',
    'publication_date' => '2023-05-15',
    'doi' => 'https://doi.org/10.1234/acs.2023.001',
    'primary_location' => [
        'source' => [
            'display_name' => 'Journal of Systems and Software',
            'issn_l' => '0164-1212',
            'type' => 'journal'
        ],
        'landing_page_url' => 'https://example.org/article1'
    ],
    'biblio' => [
        'volume' => '42',
        'issue' => '3',
        'first_page' => '100',
        'last_page' => '115'
    ],
    '__authors_formatted' => [
        ['surname' => 'Pérez', 'given-names' => 'Juan'],
        ['surname' => 'González', 'given-names' => 'María']
    ]
];

$journalEnricher = new JournalOpenAlexEnricher();
$journalElements = $journalEnricher->enrich($dom, $journalData);
$jMap = elementsToMap($journalElements);

assertCondition(isset($jMap['article-title']) && $jMap['article-title']->nodeValue === 'Advances in Computer Science', "Journal: <article-title>");
assertCondition(isset($jMap['source']) && $jMap['source']->nodeValue === 'Journal of Systems and Software', "Journal: <source>");
assertCondition(isset($jMap['volume']) && $jMap['volume']->nodeValue === '42', "Journal: <volume>");
assertCondition(isset($jMap['issue']) && $jMap['issue']->nodeValue === '3', "Journal: <issue>");
assertCondition(isset($jMap['fpage']) && $jMap['fpage']->nodeValue === '100', "Journal: <fpage>");
assertCondition(isset($jMap['lpage']) && $jMap['lpage']->nodeValue === '115', "Journal: <lpage>");
assertCondition(isset($jMap['issn']) && $jMap['issn']->nodeValue === '0164-1212', "Journal: <issn>");
assertCondition(
    isset($jMap['pub-id']) && 
    $jMap['pub-id']->nodeValue === '10.1234/acs.2023.001' && 
    $jMap['pub-id']->getAttribute('pub-id-type') === 'doi',
    "Journal: <pub-id pub-id-type=\"doi\"> estándar JATS"
);
assertCondition(
    isset($jMap['ext-link']) && 
    $jMap['ext-link']->getAttribute('ext-link-type') === 'uri' &&
    $jMap['ext-link']->getAttribute('xlink:href') === 'https://example.org/article1',
    "Journal: <ext-link ext-link-type=\"uri\">"
);
assertCondition(
    isset($jMap['person-group']) && 
    $jMap['person-group']->getElementsByTagName('name')->length === 2,
    "Journal: <person-group person-group-type=\"author\"> con 2 autores"
);

// 3.2 BookOpenAlexEnricher
$bookData = [
    'title' => 'Design Patterns in PHP',
    'publication_date' => '2021-11-01',
    'doi' => 'https://doi.org/10.1007/978-3-030-12345-6',
    'primary_location' => [
        'source' => [
            'host_organization_name' => 'Springer Nature',
            'type' => 'ebook platform'
        ]
    ],
    'biblio' => [
        'issue' => '2nd ed.'
    ],
    '__authors_formatted' => [
        ['surname' => 'Gamma', 'given-names' => 'Erich']
    ]
];

$bookEnricher = new BookOpenAlexEnricher();
$bookElements = $bookEnricher->enrich($dom, $bookData);
$bMap = elementsToMap($bookElements);

assertCondition(!isset($bMap['article-title']), "Book: NO genera <article-title> (cumple Texture)");
assertCondition(isset($bMap['source']) && $bMap['source']->nodeValue === 'Design Patterns in PHP', "Book: título en <source>");
assertCondition(isset($bMap['publisher-name']) && $bMap['publisher-name']->nodeValue === 'Springer Nature', "Book: editorial en <publisher-name>");
assertCondition(isset($bMap['edition']) && $bMap['edition']->nodeValue === '2nd ed.', "Book: edición en <edition>");
assertCondition(
    isset($bMap['pub-id']) && $bMap['pub-id']->nodeValue === '10.1007/978-3-030-12345-6',
    "Book: DOI estándar normalizado"
);

// 3.3 ChapterOpenAlexEnricher
$chapterData = [
    'title' => 'Creational Patterns',
    'publication_date' => '2021-11-01',
    'doi' => 'https://doi.org/10.1007/978-3-030-12345-6_3',
    'primary_location' => [
        'source' => [
            'display_name' => 'Design Patterns in PHP',
            'host_organization_name' => 'Springer Nature',
            'type' => 'book series'
        ]
    ],
    'biblio' => [
        'first_page' => '45',
        'last_page' => '78'
    ],
    '__authors_formatted' => [
        ['surname' => 'Fowler', 'given-names' => 'Martin']
    ]
];

$chapterEnricher = new ChapterOpenAlexEnricher();
$chapterElements = $chapterEnricher->enrich($dom, $chapterData);
$cMap = elementsToMap($chapterElements);

assertCondition(isset($cMap['chapter-title']) && $cMap['chapter-title']->nodeValue === 'Creational Patterns', "Chapter: <chapter-title>");
assertCondition(isset($cMap['source']) && $cMap['source']->nodeValue === 'Design Patterns in PHP', "Chapter: libro contenedor en <source>");
assertCondition(isset($cMap['publisher-name']) && $cMap['publisher-name']->nodeValue === 'Springer Nature', "Chapter: <publisher-name>");
assertCondition(isset($cMap['fpage']) && $cMap['fpage']->nodeValue === '45', "Chapter: <fpage>");
assertCondition(isset($cMap['lpage']) && $cMap['lpage']->nodeValue === '78', "Chapter: <lpage>");

// 3.4 ConfprocOpenAlexEnricher
$confData = [
    'title' => 'Benchmarking Web Standards',
    'publication_date' => '2022-08-20',
    'doi' => 'https://doi.org/10.1145/1234567.890',
    'primary_location' => [
        'source' => [
            'display_name' => 'Proceedings of the 2022 ACM Conference',
            'type' => 'conference'
        ]
    ],
    'biblio' => [
        'first_page' => '12',
        'last_page' => '24'
    ]
];

$confEnricher = new ConfprocOpenAlexEnricher();
$confElements = $confEnricher->enrich($dom, $confData);
$confMap = elementsToMap($confElements);

assertCondition(isset($confMap['article-title']) && $confMap['article-title']->nodeValue === 'Benchmarking Web Standards', "Confproc: <article-title>");
assertCondition(isset($confMap['source']) && $confMap['source']->nodeValue === 'Proceedings of the 2022 ACM Conference', "Confproc: <source>");
assertCondition(isset($confMap['fpage']) && $confMap['fpage']->nodeValue === '12', "Confproc: <fpage>");
assertCondition(isset($confMap['lpage']) && $confMap['lpage']->nodeValue === '24', "Confproc: <lpage>");

// 3.5 ThesisOpenAlexEnricher
$thesisData = [
    'title' => 'Deep Learning in Scholarly Metadata Processing',
    'publication_date' => '2024-03-10',
    'doi' => 'https://doi.org/10.35537/10915/12345',
    'primary_location' => [
        'source' => [
            'host_organization_name' => 'Universidad Nacional de La Plata',
            'type' => 'repository'
        ]
    ]
];

$thesisEnricher = new ThesisOpenAlexEnricher();
$thesisElements = $thesisEnricher->enrich($dom, $thesisData);
$tMap = elementsToMap($thesisElements);

assertCondition(isset($tMap['article-title']) && $tMap['article-title']->nodeValue === 'Deep Learning in Scholarly Metadata Processing', "Thesis: <article-title>");
assertCondition(isset($tMap['publisher-name']) && $tMap['publisher-name']->nodeValue === 'Universidad Nacional de La Plata', "Thesis: <publisher-name>");
assertCondition(isset($tMap['comment']) && $tMap['comment']->nodeValue === '[Tesis]', "Thesis: <comment>[Tesis]</comment>");

// 3.6 GenericOpenAlexEnricher (Graceful Fallback)
$genericData = [
    'title' => 'Open Dataset on Science Metrics',
    'primary_location' => [
        'source' => [
            'display_name' => 'Zenodo Data Archive'
        ]
    ],
    'doi' => 'https://doi.org/10.5281/zenodo.123456'
];

$genericEnricher = new GenericOpenAlexEnricher();
$genericElements = $genericEnricher->enrich($dom, $genericData);
$gMap = elementsToMap($genericElements);

assertCondition(isset($gMap['article-title']) && $gMap['article-title']->nodeValue === 'Open Dataset on Science Metrics', "Generic: <article-title>");
assertCondition(isset($gMap['source']) && $gMap['source']->nodeValue === 'Zenodo Data Archive', "Generic: <source>");
assertCondition(isset($gMap['pub-id']) && $gMap['pub-id']->nodeValue === '10.5281/zenodo.123456', "Generic: <pub-id>");

// -------------------------------------------------------------
// 4. Test de Integración JATSReference con OpenAlexEnricherFactory
// -------------------------------------------------------------
echo "\n--- 4. Pruebas de Integración con JATSReference ---\n";

// Caso A: Conversión de referencia a Libro (debe eliminar article-title y fijar publication-type="book")
$refDocx = "Gamma, E. (2021). Design Patterns in PHP. Springer Nature. https://doi.org/10.1007/978-3-030-12345-6";
$parsedRef = new Reference($refDocx);

$doc = new \DOMDocument('1.0', 'UTF-8');
$reflist = $doc->createElement('ref-list');
$doc->appendChild($reflist);

$jatsRef = new JATSReference($doc, $reflist, $parsedRef, 1);
$jatsRef->setEnrichmentData($bookData);
$jatsRef->createXMLElemetns();

$xmlOut = $doc->saveXML();

assertCondition(strpos($xmlOut, 'publication-type="book"') !== false, "JATSReference: publication-type actualizado a 'book'");
assertCondition(strpos($xmlOut, '<article-title>') === false, "JATSReference: <article-title> eliminado correctamente para libro");
assertCondition(strpos($xmlOut, '<source>Design Patterns in PHP</source>') !== false, "JATSReference: <source> contiene el título del libro");
assertCondition(strpos($xmlOut, '<publisher-name>Springer Nature</publisher-name>') !== false, "JATSReference: <publisher-name> contiene la editorial");
assertCondition(strpos($xmlOut, '<pub-id pub-id-type="doi">10.1007/978-3-030-12345-6</pub-id>') !== false, "JATSReference: <pub-id pub-id-type=\"doi\"> generado");

// Caso B: Conversión de referencia a Journal
$refDocxJournal = "Pérez, J. (2023). Advances in Computer Science. Journal of Systems and Software, 42(3), 100-115. https://doi.org/10.1234/acs.2023.001";
$parsedRefJournal = new Reference($refDocxJournal);

$docJ = new \DOMDocument('1.0', 'UTF-8');
$reflistJ = $docJ->createElement('ref-list');
$docJ->appendChild($reflistJ);

$jatsRefJournal = new JATSReference($docJ, $reflistJ, $parsedRefJournal, 2);
$jatsRefJournal->setEnrichmentData($journalData);
$jatsRefJournal->createXMLElemetns();

$xmlOutJournal = $docJ->saveXML();

assertCondition(strpos($xmlOutJournal, 'publication-type="journal"') !== false, "JATSReference: publication-type es 'journal'");
assertCondition(strpos($xmlOutJournal, '<article-title>Advances in Computer Science</article-title>') !== false, "JATSReference: <article-title> del artículo presente");
assertCondition(strpos($xmlOutJournal, '<source>Journal of Systems and Software</source>') !== false, "JATSReference: <source> de la revista presente");
assertCondition(strpos($xmlOutJournal, '<volume>42</volume>') !== false, "JATSReference: <volume> presente");
assertCondition(strpos($xmlOutJournal, '<issue>3</issue>') !== false, "JATSReference: <issue> presente");
assertCondition(strpos($xmlOutJournal, '<fpage>100</fpage>') !== false, "JATSReference: <fpage> presente");
assertCondition(strpos($xmlOutJournal, '<lpage>115</lpage>') !== false, "JATSReference: <lpage> presente");

echo "\n=========================================\n";
echo "RESULTADO: {$passed} superadas, {$failed} fallidas.\n";
echo "=========================================\n";

if ($failed > 0) {
    exit(1);
}
