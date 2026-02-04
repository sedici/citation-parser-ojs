<?php

class DatasetLoader {
    public static function load() {
        $jsonPath = dirname(__DIR__) . '/dataset.json';
        if (!file_exists($jsonPath)) {
            throw new Exception("Dataset file not found: $jsonPath");
        }
        
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON in dataset: " . json_last_error_msg());
        }
        
        return $data;
    }
}
