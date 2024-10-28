<?php

    class TitlePatterns {

    public static function getAllPatterns() {

        // Types of congress references
        $congressPattern = '/\)\.\s(?P<title>.+?)\s\[(?P<comment>[^\]]+)\](?:(?:\.\s(?P<evento>[A-Z][^\.]*))?(?:\.\s(?P<publisher>(Universidad|Editorial|Press|Ediciones)[^\.]*))?(?:\.\s(?P<publishername>[A-Z][^\.]*))?)?\.??/';
        
        // Book reference pattern
        $bookPattern = '/(?P<title>[A-Z0-9][0-9A-Za-zÀ-ÿ\s\:\,\;\-]+\.)(\s\((?P<edicion>(?P<nedicion>[0-9]+ª)\sed\.(,\sVol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?)\)\.)?\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s\:\,\;\-]+\.)/';
    
        // Book's chapter reference
        $chapterPattern = '/(?P<title>[A-Z][A-Za-zÀ-ÿ\s\:,;]+\.)\sEn\s(?P<author>(?P<nombres>(\p{Lu}\.\s?)+)\s(?P<apellido>\p{L}+(\s\p{L}+)*) (?P<role>(\((Ed.|Coord.|Comp.)\)))?((?:,\s|y\s))?)+(?P<roles>(\((Eds.|Coords.|Comps.)\)))?(?P<book>[A-Z][A-Za-zÀ-ÿ\s]+)\s(\((?P<edicion>((?P<nedicion>[0-9]+ᵃ)\sed\.,\s)?(Vol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?(,\s)?(pp.\s(?P<paginas>(\d{1,4}-\d{1,4})))?)\))\.\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)(\s(?P<url>https?:\/\/[^\s]+))?$/';
    
        // Types of journal references
        $journalPattern = '/(?P<title>[A-Z][A-Za-zÀ-ÿ\s\:\,\;\-]+)\.\s(?P<revista>[A-Z][A-Za-zÀ-ÿ\s\:\.]+)\,\s(?P<nedicio>([IVXLCDM]+|\d{1,3}))?(\((?P<volumen>\d{1,3})\))?(\,\s(?P<paginas>((?P<fpage>\d{1,4})(\–|\-)(?P<lpage>\d{1,4}))))?\./';
    
        // Types of thesis references
        $thesisPattern = '/(?P<title>[A-Z][0-9A-Za-zÀ-ÿ\s\:,;\(\)]+)\s\[(?P<comment>[^\][]*)\].\s/';
    
        return [
            $congressPattern => 'congress',
            $thesisPattern => 'thesis',
            $bookPattern => 'book',
            $chapterPattern => 'chapter',
            $journalPattern => 'journal',
        ];
    }

    //--------------------------------------> SPECIFIC PATTERN GETTERS <--------------------------------------------------//

        public static function getCongressPatterns(){
            return [
                '/\)\.\s(?P<title>.+?)\s\[(?P<comment>[^\]]+)\](?:(?:\.\s(?P<evento>[A-Z][^\.]*))?(?:\.\s(?P<publisher>(Universidad|Editorial|Press|Ediciones)[^\.]*))?(?:\.\s(?P<publishername>[A-Z][^\.]*))?)?\.??/' => 'congress'
            ];
        }

        public static function getBookPatterns(){
            return [
                '/(?P<title>[A-Z0-9][0-9A-Za-zÀ-ÿ\s\:\,\;\-]+\.)(\s\((?P<edicion>(?P<nedicion>[0-9]+ª)\sed\.(,\sVol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?)\)\.)?\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s\:\,\;\-]+\.)/' => 'book',
            ];
        }

        public static function getJournalPatterns(){
            return [
                '/(?P<title>[A-Z][A-Za-zÀ-ÿ\s\:\,\;\-]+)\.\s(?P<revista>[A-Z][A-Za-zÀ-ÿ\s\:\.]+)\,\s(?P<nedicio>([IVXLCDM]+|\d{1,3}))?(\((?P<volumen>\d{1,3})\))?(\,\s(?P<paginas>((?P<fpage>\d{1,4})(\–|\-)(?P<lpage>\d{1,4}))))?\./' => 'journal',
            ];
        }

        public static function getChapterPatterns(){
            return [
                '/(?P<title>[A-Z][A-Za-zÀ-ÿ\s\:,;]+\.)\sEn\s(?P<author>(?P<nombres>(\p{Lu}\.\s?)+)\s(?P<apellido>\p{L}+(\s\p{L}+)*) (?P<role>(\((Ed.|Coord.|Comp.)\)))?((?:,\s|y\s))?)+(?P<roles>(\((Eds.|Coords.|Comps.)\)))?(?P<book>[A-Z][A-Za-zÀ-ÿ\s]+)\s(\((?P<edicion>((?P<nedicion>[0-9]+ᵃ)\sed\.,\s)?(Vol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?(,\s)?(pp.\s(?P<paginas>(\d{1,4}-\d{1,4})))?)\))\.\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)(\s(?P<url>https?:\/\/[^\s]+))?$/' => 'chapter',
            ];
        }

        public static function getThesisPatterns(){
            return [
                '/(?P<title>[A-Z][A-Za-zÀ-ÿ\s\:,;]+)\s\[(?P<comment>[^\][]*)\].\s/' => 'thesis'
            ];
        }

    }