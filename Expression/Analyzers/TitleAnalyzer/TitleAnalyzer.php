<?php

    include_once('TitlePatterns.php');
    include_once('TitleKeywords/Comment/CommentKeywords.php');

    class TitleAnalyzer{

        private $patterns;

        public function __construct(array $types = []) { 
            if (empty($types)) {
                $this->patterns = TitlePatterns::getAllPatterns();
            } else {
                $this->patterns = [];
                foreach ($types as $type) {
                    $patternGetter = 'get'.ucfirst($type).'Patterns';
                    $this->patterns = array_merge($this->patterns, TitlePatterns::$patternGetter());
                }
            }
        }



        public function analyze(string $text, array $types) {
            foreach ($this->patterns as $pattern => $name) {
             
                if (preg_match($pattern, $text, $matches)) { 

                    if ($name === 'congress' || $name === 'thesis') {
                        if (!in_array('congress', $types)) {
                            
                            if (isset($matches['comment'])) {
                                $comment = $matches['comment'];
                            } else {
                                $comment = "No 'comment' match found.";
                            }

                            $checkedExpression = $this->checkComment($comment, $name);
                            return array('expression' => $checkedExpression, 'value' => $matches);
                        }
                    
                    }

                    return array('expression' => $name, 'value' => $matches);
                }
            }

            return array('expression' => 'No match found', 'value' => '');
        }




        public function checkComment (string $comment, $name, $language = 'es') {

            $keywords = CommentKeywords::buildArrayKeywords($language);

            $commentWithoutAccents = $this->DeleteAccents($comment);
            $lowerComment = strtolower($commentWithoutAccents);
            
            //Congress keywords verification.
            foreach ($keywords['congress'] as $keyword) {
                if (strpos($lowerComment, $keyword) !== false) {
                    return 'congress';
                }
            }

            //Thesis keywords verification.
            foreach ($keywords['thesis'] as $keyword) {
                if (strpos($lowerComment, $keyword) !== false) {
                    return 'thesis';
                }
            }

            return $name;

        }

        
        
        public function DeleteAccents(String $text){
            $noAccentText = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
            return $noAccentText;
        }

    }