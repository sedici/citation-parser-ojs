<?php
/* 
* Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

/**
 * OpenAlexTypeMapper
 *
 * Mapea metadatos devueltos por la API de OpenAlex (work.type y primary_location.source.type)
 * a los publication-type compatibles con JATS XML (Texture) y a las clases Printer del parser.
 */
class OpenAlexTypeMapper {

    /**
     * Mapeo de tipos estándar JATS admitidos por Texture
     */
    public const JATS_TYPE_JOURNAL   = 'journal';
    public const JATS_TYPE_BOOK      = 'book';
    public const JATS_TYPE_CHAPTER   = 'chapter';
    public const JATS_TYPE_CONFPROC  = 'confproc';
    public const JATS_TYPE_THESIS    = 'thesis';
    public const JATS_TYPE_PREPRINT  = 'preprint';
    public const JATS_TYPE_REPORT    = 'report';
    public const JATS_TYPE_DATA      = 'data';
    public const JATS_TYPE_WEBSITE   = 'website';

    /**
     * Resuelve el publication-type de JATS XML a partir de un objeto Work de OpenAlex.
     *
     * @param array $openAlexWork Datos del trabajo devueltos por la API de OpenAlex.
     * @param string|null $fallback Tipo por defecto en caso de no coincidir (default: 'journal').
     * @return string Tipo JATS compatible con Texture.
     */
    public static function resolveJatsPublicationType(array $openAlexWork, ?string $fallback = self::JATS_TYPE_JOURNAL): string {
        $workType = strtolower(trim((string)($openAlexWork['type'] ?? '')));
        $sourceType = strtolower(trim((string)($openAlexWork['primary_location']['source']['type'] ?? '')));

        // 1. Evaluación por tipo de obra (work.type)
        if ($workType === 'book') {
            return self::JATS_TYPE_BOOK;
        }

        if ($workType === 'book-chapter' || $workType === 'chapter') {
            return self::JATS_TYPE_CHAPTER;
        }

        if ($workType === 'dissertation' || $workType === 'thesis') {
            return self::JATS_TYPE_THESIS;
        }

        if (in_array($workType, ['proceedings-article', 'proceedings', 'conference-paper'], true)) {
            return self::JATS_TYPE_CONFPROC;
        }

        if ($workType === 'preprint') {
            return self::JATS_TYPE_PREPRINT;
        }

        if ($workType === 'report') {
            return self::JATS_TYPE_REPORT;
        }

        if ($workType === 'dataset') {
            return self::JATS_TYPE_DATA;
        }

        // 2. Evaluación por tipo de medio / canal (source.type)
        if ($sourceType === 'conference') {
            return self::JATS_TYPE_CONFPROC;
        }

        if ($sourceType === 'ebook platform' || $sourceType === 'book series') {
            return ($workType === 'book-chapter') ? self::JATS_TYPE_CHAPTER : self::JATS_TYPE_BOOK;
        }

        if ($sourceType === 'journal' || $workType === 'article') {
            return self::JATS_TYPE_JOURNAL;
        }

        if ($sourceType === 'repository') {
            // Documentos en repositorios suelen ser artículos (Green OA) o preprints
            return ($workType === 'preprint') ? self::JATS_TYPE_PREPRINT : self::JATS_TYPE_JOURNAL;
        }

        return !empty($fallback) ? $fallback : self::JATS_TYPE_JOURNAL;
    }

    /**
     * Resuelve el nombre de la clase Printer correspondiente al recurso OpenAlex.
     *
     * @param array $openAlexWork Datos del trabajo devueltos por OpenAlex.
     * @param string|null $fallback Nombre de la clase por defecto (default: 'JournalPrinter').
     * @return string|null Nombre de la clase Printer.
     */
    public static function resolvePrinterClass(array $openAlexWork, ?string $fallback = 'JournalPrinter'): ?string {
        $jatsType = self::resolveJatsPublicationType($openAlexWork, null);

        return match ($jatsType) {
            self::JATS_TYPE_BOOK     => 'BookPrinter',
            self::JATS_TYPE_CHAPTER  => 'ChapterPrinter',
            self::JATS_TYPE_CONFPROC => 'ConfprocPrinter',
            self::JATS_TYPE_THESIS   => 'ThesisPrinter',
            self::JATS_TYPE_REPORT,
            self::JATS_TYPE_DATA     => 'GenericPrinter',
            self::JATS_TYPE_WEBSITE  => 'WebpagePrinter',
            self::JATS_TYPE_JOURNAL,
            self::JATS_TYPE_PREPRINT => 'JournalPrinter',
            default                  => $fallback,
        };
    }

    /**
     * Comprueba si la clase Printer resuelta cuenta con soporte activo de enriquecimiento.
     *
     * @param string|null $printerClassName
     * @return bool True si la clase existe y cuenta con el método enrichment().
     */
    public static function isEnrichmentSupported(?string $printerClassName): bool {
        if (empty($printerClassName)) {
            return false;
        }

        if (!class_exists($printerClassName)) {
            return false;
        }

        return method_exists($printerClassName, 'enrichment');
    }
}
