<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

class GenerativeAI {
    
    private $apiKey;
    private $apiUrl;
    private $model;
    private $provider;

    public function __construct($provider = 'groq', $apiKey = '', $model = '') {
        $this->provider = strtolower(trim($provider));
        $this->apiKey = $apiKey;
        
        // Defaults
        if ($provider === 'groq') {
            $this->apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
            $this->model = $model ?: 'llama3-70b-8192'; 
        } elseif ($provider === 'gemini') {
            // Using OpenAI compatibility endpoint if available or standard google API
            // For simplicity, we'll assume a standard structure or implementing OpenAI compatible interface if possible
            // But Google usually requires google-generative-ai lib or specific REST
            // We'll stick to a generic "OpenAI-compatible" structure for now which Groq supports perfectly.
            // If user wants raw Gemini REST:
             $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
             $this->model = $model ?: 'gemini-1.5-flash';
        } else {
             // General assignment (e.g. for Mock or future providers)
             $this->model = $model;
        }
    }

    public function parseReference($reference) {
        if (empty($this->apiKey) && $this->provider !== 'mock') { // API Key not needed for mock
            return ['error' => 'API Key missing'];
        }

        if ($this->provider === 'groq') {
            return $this->callOpenAICompatible($reference);
        } elseif ($this->provider === 'gemini') {
            return $this->callGemini($reference);
        } elseif ($this->provider === 'mock') {
            return [
                'type' => 'journal',
                'title' => 'Mock AI Title Detected',
                'source' => 'Mock AI Journal Source',
                'authors' => ['AI Author', 'Test User'],
                'year' => '2025',
                'volume' => '1',
                'issue' => '1',
                'pages' => '100-200',
                'model' => $this->model // Use configured model
            ];
        }
        
        return ['error' => 'Unknown provider'];
    }

    private function callOpenAICompatible($reference) {
        $prompt = "You are a bibliographic expert. Your task is to parse the following citation into a structured JSON format. " . 
                  "Return ONLY the JSON. No markdown formatting, no explanations. " .
                  "Fields: attempt to detect 'type' (journal, book, chapter, thesis, conf-proc), 'title', 'authors' (array of strings), 'year', 'source' (journal/book name), 'volume', 'issue', 'pages', 'doi'. " .
                  "Citation: \"$reference\"";

        $data = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that outputs only JSON.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.1
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            curl_close($ch);
            return ['error' => 'Request Error'];
        }
        curl_close($ch);

        $result = json_decode($response, true);
        
        if (isset($result['choices'][0]['message']['content'])) {
            $content = $result['choices'][0]['message']['content'];
            // Clean up code blocks if present
            $content = str_replace(['```json', '```'], '', $content);
            return json_decode(trim($content), true);
        }

        return ['error' => 'Invalid response from AI'];
    }

   private function callGemini($reference) {
        $prompt = "Parse this citation into JSON (type, title, authors, year, source, volume, issue, pages, doi). Citation: \"$reference\"";
        
        $data = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
             curl_close($ch);
             return ['error' => 'Request Error'];
        }
        curl_close($ch);

        $result = json_decode($response, true);
        // Map Gemini response structure
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
             $content = $result['candidates'][0]['content']['parts'][0]['text'];
             $content = str_replace(['```json', '```'], '', $content);
             return json_decode(trim($content), true);
        }
        
        // Debug: if decoding failed or structure missing
        if (json_last_error() !== JSON_ERROR_NONE) {
             return ['error' => 'Invalid JSON from Gemini: ' . substr($response, 0, 200)];
        }
        
        return ['error' => 'Invalid response structure from Gemini: ' . substr($response, 0, 200)];
    }
    public function parseBatch(array $references) {
         if (empty($this->apiKey) && $this->provider !== 'mock') {
            return ['error' => 'API Key missing'];
         }
         
         if (empty($references)) {
             return [];
         }

         if ($this->provider === 'mock') {
             // Return dummy results for match
             $results = [];
             foreach ($references as $ref) {
                 $results[] = [
                    'citation' => $ref,
                    'parsed' => [
                        'type' => 'journal',
                        'title' => 'Mock Batch Title',
                        'year' => '2024'
                    ]
                 ];
             }
             return $results;
         }

         // Construct prompt for batch
        $prompt = <<<EOT
        You are an expert in JATS XML (Journal Article Tag Suite) standards for bibliographic metadata.
        Your task is to parse the input citation and extract the data into a JSON object that perfectly maps to JATS structure logic.

        **Input Citation:**
        "{$reference}"

        **Strict Instructions:**
        1. **Output:** Return ONLY valid JSON. No markdown, no intro text.
        2. **Name Parsing:** You MUST split names into `surname` and `given_names`.
        3. **Roles:** Detect specific roles based on cues (e.g., "(Eds.)", "coord.", "trad.", "trans."). Map them to valid JATS `person-group-type` values: 'author', 'editor', 'translator', 'compiler'.
        4. **Pages:** Split page ranges into `fpage` (first page) and `lpage` (last page).
        5. **Nulls:** Use `null` for missing fields.

        **Target JSON Structure:**
        {
        "publication_type": "string (enum: journal, book, thesis, confproc, report, patent, webpage)",
        "article_title": "string or null",
        "source": "string or null (The container title: journal name, book title, or conference proceedings)",
        "year": "string (ISO 4-digit year) or null",
        "volume": "string or null",
        "issue": "string or null",
        "fpage": "string or null",
        "lpage": "string or null",
        "pub_id_doi": "string or null (clean DOI without URL prefix)",
        "publisher_name": "string or null",
        "publisher_loc": "string or null",
        "person_groups": [
            {
            "type": "string (enum: author, editor, translator, compiler, curator)",
            "names": [
                {
                "surname": "string",
                "given_names": "string"
                }
            ]
            }
        ]
        }
        EOT;
         
         foreach ($references as $i => $ref) {
             $prompt .= ($i+1) . ". $ref\n";
         }
         
         if ($this->provider === 'groq') {
             return $this->callOpenAICompatibleBatch($prompt);
         } elseif ($this->provider === 'gemini') {
             return $this->callGeminiBatch($prompt);
         }
         
         return ['error' => 'Batch not fully implemented for this provider yet'];
    }

    private function callGeminiBatch($prompt) {
         $data = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ];
        
        // Use configured URL
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
             curl_close($ch);
             return ['error' => 'Request Error'];
        }
        curl_close($ch);

        $result = json_decode($response, true);
        
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
             $content = $result['candidates'][0]['content']['parts'][0]['text'];
             $content = str_replace(['```json', '```'], '', $content);
             $decoded = json_decode(trim($content), true);
             if (is_array($decoded)) return $decoded;
        }
         
        // Debug
        if (json_last_error() !== JSON_ERROR_NONE) {
             return ['error' => 'Invalid JSON from Gemini Batch: ' . substr($response, 0, 200)];
        }

        return ['error' => 'Invalid response from Gemini Batch. Content: ' . substr(json_encode($result), 0, 500)];
    }

    private function callOpenAICompatibleBatch($prompt) {
         $data = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that outputs only JSON.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.1
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            $content = $result['choices'][0]['message']['content'];
            $content = str_replace(['```json', '```'], '', $content);
            return json_decode(trim($content), true);
        }
        return ['error' => 'Invalid batch response'];
    }
}
