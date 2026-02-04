<?php
// test_parser.php (Legacy)
// Tests the Production Parser (Regex only)

require_once 'Expression/Parser.php';

$citations = [
    "Smith, J. (2020). The art of coding. Journal of Examples, 10(2), 123-130.",
    "Doe, J. (2021). The Book of Everything. (2nd ed.). Universal Press.",
    "Author, A. (2022). Chapter Title. En Editor, E. (Ed.), The Anthology (pp. 50-60). Big House.",
    "Student, S. (2019). My PhD Thesis [Doctoral dissertation]. University of Science.",
];

foreach ($citations as $ref) {
    echo "Ref: $ref\n";
    $result = Parser::parse($ref);
    echo "Expression: " . $result['expression'] . "\n";
    if ($result['expression'] !== 'No match found') {
        print_r($result['value']);
    }
    echo "--------------------------------------------------\n";
}

