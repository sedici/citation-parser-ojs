<?php

/* 
 * Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
 * Licensed under GPLv3: see LICENSE file for details.
 */

require_once __DIR__ . '/../Reference.php';
require_once __DIR__ . '/../ReferencesManager.php';
require_once __DIR__ . '/../JATSReference.php';

echo "=== TESTING IEEE REFERENCE STRATEGY & PARSER ===\n\n";

$passed = 0;
$failed = 0;

function assertCondition(bool $condition, string $testName, string $detail = '') {
    global $passed, $failed;
    if ($condition) {
        echo "[PASS] $testName\n";
        $passed++;
    } else {
        echo "[FAIL] $testName" . ($detail ? " ($detail)" : '') . "\n";
        $failed++;
    }
}

// -------------------------------------------------------------
// 1. Journal Article with DOI & volume/issue/pages (TEyET dataset)
// -------------------------------------------------------------
echo "--- 1. Journal Articles ---\n";
$ref1 = '[3] W. Bao, “COVID-19 and online teaching in higher education: A case study of Peking University,” Human Behavior and Emerging Technologies, vol. 2, no. 2, pp. 113–115, 2020, doi: 10.1002/hbe2.191.';
$refObj1 = new Reference($ref1, 'ieee');
$parsed1 = $refObj1->getTitle();
$authors1 = $refObj1->getAuthor()['authors'] ?? [];
$date1 = $refObj1->getDate();
$url1 = $refObj1->getURL();

assertCondition($refObj1->getType() === 'journal', 'Ref 1: Type is journal');
assertCondition(count($authors1) === 1 && $authors1[0]['apellido'] === 'Bao' && $authors1[0]['nombres'] === 'W.', 'Ref 1: Author W. Bao parsed');
assertCondition($parsed1['title'] === 'COVID-19 and online teaching in higher education: A case study of Peking University', 'Ref 1: Article title parsed');
assertCondition($parsed1['revista'] === 'Human Behavior and Emerging Technologies', 'Ref 1: Journal name parsed');
assertCondition($parsed1['volumen'] === '2', 'Ref 1: Volume parsed');
assertCondition($parsed1['nedicio'] === '2', 'Ref 1: Issue parsed');
assertCondition($parsed1['fpage'] === '113' && $parsed1['lpage'] === '115', 'Ref 1: Pages parsed');
assertCondition($date1['year'] === '2020', 'Ref 1: Year 2020 parsed');
assertCondition(isset($url1['doi']) && $url1['doi'] === '10.1002/hbe2.191', 'Ref 1: DOI parsed');

// -------------------------------------------------------------
// 2. Journal Article with multiple authors & [Online]. Available
// -------------------------------------------------------------
$ref2 = '[1] C. B. Hodges, S. Moore, B. B. Lockee, T. Trust, and M. A. Bond, “The difference between emergency remote teaching and online learning,” EDUCAUSE Review, Mar. 27, 2020. [Online]. Available: https://er.educause.edu/articles/2020/3/the-difference-between-emergency-remote-teaching-and-online-learning';
$refObj2 = new Reference($ref2, 'ieee');
$authors2 = $refObj2->getAuthor()['authors'] ?? [];
$parsed2 = $refObj2->getTitle();
$url2 = $refObj2->getURL();
$date2 = $refObj2->getDate();

assertCondition(count($authors2) === 5, 'Ref 2: Exactly 5 authors detected');
assertCondition($authors2[0]['apellido'] === 'Hodges' && $authors2[0]['nombres'] === 'C. B.', 'Ref 2: Author 1 is C. B. Hodges');
assertCondition($authors2[4]['apellido'] === 'Bond' && $authors2[4]['nombres'] === 'M. A.', 'Ref 2: Author 5 is M. A. Bond');
assertCondition($parsed2['revista'] === 'EDUCAUSE Review', 'Ref 2: Source cleanly isolates EDUCAUSE Review');
assertCondition($date2['month'] === '3' && $date2['year'] === '2020', 'Ref 2: Date has month 3 and year 2020');
assertCondition(isset($url2['url']) && strpos($url2['url'], 'educause.edu') !== false, 'Ref 2: Online URL parsed');

// -------------------------------------------------------------
// 3. Conference Proceedings
// -------------------------------------------------------------
echo "\n--- 2. Conference Proceedings ---\n";
$refConf = '[8] A. V. dos Santos and C. T. W. da Rosa, “A gamificação no ensino de ciências: uma revisão sistemática,” in Proc. 2019 IEEE Int. Conf. on Advanced Learning Technologies (ICALT), Maceió, Brazil, 2019, pp. 234–238.';
$refObjConf = new Reference($refConf, 'ieee');
$parsedConf = $refObjConf->getTitle();
$authorsConf = $refObjConf->getAuthor()['authors'] ?? [];

assertCondition($refObjConf->getType() === 'confproc', 'Confproc: Type detected as confproc');
assertCondition(count($authorsConf) === 2, 'Confproc: 2 authors detected');
assertCondition($authorsConf[0]['apellido'] === 'dos Santos' && $authorsConf[0]['nombres'] === 'A. V.', 'Confproc: Author 1 compound surname dos Santos');
assertCondition($authorsConf[1]['apellido'] === 'da Rosa' && $authorsConf[1]['nombres'] === 'C. T. W.', 'Confproc: Author 2 compound surname da Rosa');
assertCondition($parsedConf['fpage'] === '234' && $parsedConf['lpage'] === '238', 'Confproc: Pages 234-238 parsed');

// -------------------------------------------------------------
// 4. Books (Unquoted)
// -------------------------------------------------------------
echo "\n--- 3. Books (Unquoted) ---\n";
$refBook1 = '[5] F. Appolinário, Metodologia da ciência: filosofia e prática da pesquisa. São Paulo: Pioneira Thomson Learning, 2006.';
$refObjBook1 = new Reference($refBook1, 'ieee');
$parsedBook1 = $refObjBook1->getTitle();
$authorsBook1 = $refObjBook1->getAuthor()['authors'] ?? [];

assertCondition($refObjBook1->getType() === 'book', 'Book 1: Type detected as book');
assertCondition(count($authorsBook1) === 1 && $authorsBook1[0]['apellido'] === 'Appolinário', 'Book 1: Author F. Appolinário parsed');
assertCondition($parsedBook1['title'] === 'Metodologia da ciência: filosofia e prática da pesquisa', 'Book 1: Book subtitle included without city/publisher');
assertCondition($parsedBook1['editorial'] === 'Pioneira Thomson Learning', 'Book 1: Publisher is Pioneira Thomson Learning');
assertCondition($parsedBook1['publisher-loc'] === 'São Paulo', 'Book 1: City is São Paulo');

$refBook2 = '[6] P. L. Alreck and R. B. Settle, The Survey Research Handbook, 2nd ed. Chicago, IL, USA: Irwin, 1995.';
$refObjBook2 = new Reference($refBook2, 'ieee');
$parsedBook2 = $refObjBook2->getTitle();
$authorsBook2 = $refObjBook2->getAuthor()['authors'] ?? [];

assertCondition(count($authorsBook2) === 2, 'Book 2: 2 authors detected');
assertCondition($authorsBook2[0]['apellido'] === 'Alreck' && $authorsBook2[0]['nombres'] === 'P. L.', 'Book 2: Author 1 P. L. Alreck');
assertCondition($authorsBook2[1]['apellido'] === 'Settle' && $authorsBook2[1]['nombres'] === 'R. B.', 'Book 2: Author 2 R. B. Settle');
assertCondition($parsedBook2['title'] === 'The Survey Research Handbook', 'Book 2: Title cleanly extracted without edition');
assertCondition($parsedBook2['nedicion'] === '2', 'Book 2: Edition number 2 extracted');
assertCondition($parsedBook2['editorial'] === 'Irwin', 'Book 2: Publisher Irwin');

// -------------------------------------------------------------
// 5. Book Chapters
// -------------------------------------------------------------
echo "\n--- 4. Book Chapters ---\n";
$refChapter = '[4] M. J. S. da Silva and R. M. da Silva, “Educação e ensino remoto em tempos de pandemia: desafios e desencontros,” in E-book VII CONEDU (Conedu em Casa), vol. 3, Campina Grande: Realize Editora, 2021, pp. 827–841.';
$refObjChap = new Reference($refChapter, 'ieee');
$parsedChap = $refObjChap->getTitle();

assertCondition($refObjChap->getType() === 'chapter', 'Chapter: Type detected as chapter');
assertCondition($parsedChap['title'] === 'Educação e ensino remoto em tempos de pandemia: desafios e desencontros', 'Chapter: Title extracted');
assertCondition($parsedChap['editorial'] === 'Realize Editora', 'Chapter: Publisher Realize Editora extracted');
assertCondition($parsedChap['volumen'] === '3', 'Chapter: Volume 3 extracted');

// -------------------------------------------------------------
// 6. Theses
// -------------------------------------------------------------
echo "\n--- 5. Theses ---\n";
$refThesis = '[14] F. Delano, A Study of the Effectiveness of a Computer When Used as a Teaching and Learning Tool in High School Mathematics. Tesis doctoral, Univ. Nairobi, 1971. [En línea]. Disponible: http://erepository.uonbi.ac.ke/handle/11295/107680';
$refObjThesis = new Reference($refThesis, 'ieee');
$parsedThesis = $refObjThesis->getTitle();
$authorsThesis = $refObjThesis->getAuthor()['authors'] ?? [];

assertCondition($refObjThesis->getType() === 'thesis', 'Thesis: Type detected as thesis');
assertCondition(count($authorsThesis) === 1 && $authorsThesis[0]['apellido'] === 'Delano', 'Thesis: Author F. Delano');
assertCondition($parsedThesis['title'] === 'A Study of the Effectiveness of a Computer When Used as a Teaching and Learning Tool in High School Mathematics', 'Thesis: Title extracted');
assertCondition($parsedThesis['comment'] === '[Tesis doctoral]', 'Thesis: Degree comment [Tesis doctoral] extracted');
assertCondition($parsedThesis['publisher-name'] === 'Univ. Nairobi', 'Thesis: Institution Univ. Nairobi extracted');

// -------------------------------------------------------------
// 7. Institutional Authors
// -------------------------------------------------------------
echo "\n--- 6. Institutional Authors ---\n";
$refInst = '[10] IEEE Standards Association, “IEEE Standard for Learning Object Metadata,” IEEE Std 1484.12.1-2020, 2020.';
$refObjInst = new Reference($refInst, 'ieee');
$authorData = $refObjInst->getAuthor();

assertCondition(isset($authorData['institution']) && $authorData['institution'] === 'IEEE Standards Association', 'Institution: IEEE Standards Association detected');
assertCondition($refObjInst->getAuthorType() === 'collab', 'Institution: AuthorType is collab');

// -------------------------------------------------------------
// 8. Backward Compatibility with APA 7
// -------------------------------------------------------------
echo "\n--- 7. Backward Compatibility with APA 7 ---\n";
$apaRef = 'Bao, W. (2020). COVID-19 and online teaching in higher education: A case study of Peking University. Human Behavior and Emerging Technologies, 2(2), 113-115. https://doi.org/10.1002/hbe2.191';
$refObjApa = new Reference($apaRef, 'apa');

assertCondition($refObjApa->getType() === 'journal', 'APA: Type is journal');
assertCondition($refObjApa->getAuthorType() === 'authors', 'APA: AuthorType is authors');
assertCondition($refObjApa->getDate()['year'] === '2020', 'APA: Date year is 2020');

// -------------------------------------------------------------
// 9. Full JATS XML Generation
// -------------------------------------------------------------
echo "\n--- 8. JATS XML Generation Integration ---\n";
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->formatOutput = true;
$back = $dom->createElement('back');
$dom->appendChild($back);

$manager = new ReferencesManager($dom, $back, [$ref1, $refBook1], 'ieee');
$xml = $dom->saveXML();

assertCondition(strpos($xml, '<ref id="parser_0">') !== false, 'JATS XML: Contains parser_0');
assertCondition(strpos($xml, '<ref id="parser_1">') !== false, 'JATS XML: Contains parser_1');
assertCondition(strpos($xml, '<article-title>') !== false, 'JATS XML: Journal has article-title');
assertCondition(strpos($xml, '<source>Metodologia da ciência: filosofia e prática da pesquisa</source>') !== false, 'JATS XML: Book has clean source');
assertCondition(strpos($xml, '<publisher-name>Pioneira Thomson Learning</publisher-name>') !== false, 'JATS XML: Book has publisher-name');

echo "\n=========================================\n";
echo "RESULTADO: $passed superadas, $failed fallidas.\n";
echo "=========================================\n";

if ($failed > 0) {
    exit(1);
}
