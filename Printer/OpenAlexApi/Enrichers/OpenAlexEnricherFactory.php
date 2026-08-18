<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once __DIR__ . '/OpenAlexEnricherInterface.php';
require_once __DIR__ . '/BaseOpenAlexEnricher.php';
require_once __DIR__ . '/JournalOpenAlexEnricher.php';
require_once __DIR__ . '/GenericOpenAlexEnricher.php';

class OpenAlexEnricherFactory {

    private static array $map = [
        'journal' => JournalOpenAlexEnricher::class,
    ];

    /**
     * Resolves source type and instantiates the enricher strategy, returning metadata.
     */
    public static function resolve(array $openAlexData): array {
        $sourceType = strtolower($openAlexData['primary_location']['source']['type'] ?? $openAlexData['type'] ?? 'generic');
        $isFallback = false;

        if (isset(self::$map[$sourceType])) {
            $class = self::$map[$sourceType];
        } else {
            $class = GenericOpenAlexEnricher::class;
            $isFallback = true;
        }

        return [
            'source_type'    => $sourceType,
            'enricher_class' => $class,
            'is_fallback'    => $isFallback,
            'enricher'       => new $class(),
        ];
    }

    /**
     * Instantiates the appropriate OpenAlex enricher strategy.
     *
     * @param array $openAlexData OpenAlex work item array
     * @return OpenAlexEnricherInterface
     */
    public static function create(array $openAlexData): OpenAlexEnricherInterface {
        $resolved = self::resolve($openAlexData);
        return $resolved['enricher'];
    }


    /**
     * Register a new enricher class for a specific source type.
     */
    public static function registerEnricher(string $sourceType, string $enricherClass): void {
        self::$map[strtolower($sourceType)] = $enricherClass;
    }
}
