<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

class AnyStyle {
    
    private $binaryPath = 'anystyle';

    public function __construct($binaryPath = null) {
        if ($binaryPath !== null) {
            $this->binaryPath = $binaryPath;
        }
    }

    /**
     * Check if anystyle is available in the system
     */
    public function isAvailable(): bool {
        // Check if anystyle is in PATH
        exec('which anystyle', $output, $returnVar);
        if ($returnVar === 0) return true;

        // Check user gem path (common on linux)
        $userGemPath = getenv('HOME') . '/.local/share/gem/ruby/3.2.0/bin/anystyle';
        if (file_exists($userGemPath)) {
             $this->binaryPath = $userGemPath;
             return true;
        }

        return false;
    }

    /**
     * Parse a reference using AnyStyle CLI
     * Returns an array with structured data or error
     */
    public function parse($reference) {
        $jsonOutput = $this->parseRaw($reference, 'json');
        
        if (strpos($jsonOutput, 'error:') === 0) { // Simple error check
             // If it's a JSON string beginning with { "error": ... } handle that?
             // Actually parseRaw returns string or error string.
             // Let's decode to check.
        }

        $data = json_decode($jsonOutput, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
             // Try to find JSON in output (sometimes warnings appear)
             if (preg_match('/\[.*\]/s', $jsonOutput, $matches)) {
                 $data = json_decode($matches[0], true);
             }
             
             if (json_last_error() !== JSON_ERROR_NONE) {
                // If it really failed, maybe parseRaw returned the error string directly if I make it do that?
                // Let's keep parseRaw simple: returns output.
                return ['error' => 'Invalid JSON from AnyStyle'];
             }
        }

        // AnyStyle returns an array of results, we only sent one
        return isset($data[0]) ? $data[0] : ['error' => 'No Parse Result'];
    }

    /**
     * Parse a reference and return raw string output in specified format
     * Formats: bib, csl, json, ref, txt, xml
     */
    public function parseRaw($reference, $format = 'json') {
        if (!$this->isAvailable()) {
            return '{"error": "AnyStyle CLI not found"}';
        }

        // Create temp file
        $tempFile = tempnam(sys_get_temp_dir(), 'as_');
        file_put_contents($tempFile, $reference);

        // Usage: anystyle --stdout --format=json parse tempFile
        $cmd = escapeshellcmd($this->binaryPath) . " --stdout --format=" . escapeshellarg($format) . " parse " . escapeshellarg($tempFile) . " 2>&1";
        
        $output = shell_exec($cmd);
        
        // Cleanup
        @unlink($tempFile);

        return $output ?: '';
    }
}
