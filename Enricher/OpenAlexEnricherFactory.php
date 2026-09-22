<?php
/* 
* Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once dirname(__DIR__) . '/Printer/OpenAlexApi/OpenAlexTypeMapper.php';
require_once __DIR__ . '/OpenAlexEnricherInterface.php';
require_once __DIR__ . '/BaseOpenAlexEnricher.php';
require_once __DIR__ . '/JournalOpenAlexEnricher.php';
require_once __DIR__ . '/BookOpenAlexEnricher.php';
require_once __DIR__ . '/ChapterOpenAlexEnricher.php';
require_once __DIR__ . '/ConfprocOpenAlexEnricher.php';
require_once __DIR__ . '/ThesisOpenAlexEnricher.php';
require_once __DIR__ . '/GenericOpenAlexEnricher.php';

/**
 * OpenAlexEnricherFactory
 *
 * Fábrica para instanciar la estrategia de enriquecimiento adecuada
 * inspeccionando los metadatos devueltos por OpenAlex.
 * Soporta registro dinámico de nuevas estrategias cumpliendo el principio Open/Closed.
 */
class OpenAlexEnricherFactory {

    /**
     * Registro de estrategias personalizadas o adicionales
     * [ 'tipo_jats_o_fuente' => 'NombreDeClaseEnricher' ]
     */
    private static array $registry = [];

    /**
     * Permite registrar o sobrescribir dinámicamente una estrategia de enriquecimiento.
     *
     * @param string $type Tipo JATS o tipo de fuente OpenAlex.
     * @param string $enricherClass Nombre de la clase que implementa OpenAlexEnricherInterface.
     */
    public static function registerEnricher(string $type, string $enricherClass): void {
        self::$registry[strtolower(trim($type))] = $enricherClass;
    }

    /**
     * Limpia el registro dinámico (útil para pruebas unitarias).
     */
    public static function clearRegistry(): void {
        self::$registry = [];
    }

    /**
     * Resuelve e instancia el Enricher correspondiente a los datos de la obra de OpenAlex.
     *
     * @param array $openAlexWork JSON devuelto por OpenAlex.
     * @return OpenAlexEnricherInterface
     */
    public static function getEnricher(array $openAlexWork): OpenAlexEnricherInterface {
        // 1. Verificar si el tipo crudo de OpenAlex está registrado dinámicamente
        $rawType = strtolower(trim((string)($openAlexWork['type'] ?? '')));
        if ($rawType !== '' && isset(self::$registry[$rawType])) {
            $class = self::$registry[$rawType];
            if (class_exists($class)) {
                return new $class();
            }
        }

        $rawSourceType = strtolower(trim((string)($openAlexWork['primary_location']['source']['type'] ?? '')));
        if ($rawSourceType !== '' && isset(self::$registry[$rawSourceType])) {
            $class = self::$registry[$rawSourceType];
            if (class_exists($class)) {
                return new $class();
            }
        }

        // 2. Resolver tipo JATS usando 'generic' como fallback para tipos no reconocidos
        $jatsType = OpenAlexTypeMapper::resolveJatsPublicationType($openAlexWork, 'generic');

        // 3. Verificar si el tipo JATS resuelto está registrado dinámicamente
        if (isset(self::$registry[$jatsType])) {
            $class = self::$registry[$jatsType];
            if (class_exists($class)) {
                return new $class();
            }
        }

        // 4. Mapeo estándar a las estrategias del sistema
        return match ($jatsType) {
            OpenAlexTypeMapper::JATS_TYPE_BOOK     => new BookOpenAlexEnricher(),
            OpenAlexTypeMapper::JATS_TYPE_CHAPTER  => new ChapterOpenAlexEnricher(),
            OpenAlexTypeMapper::JATS_TYPE_CONFPROC => new ConfprocOpenAlexEnricher(),
            OpenAlexTypeMapper::JATS_TYPE_THESIS   => new ThesisOpenAlexEnricher(),
            OpenAlexTypeMapper::JATS_TYPE_JOURNAL,
            OpenAlexTypeMapper::JATS_TYPE_PREPRINT => new JournalOpenAlexEnricher(),
            default                                => new GenericOpenAlexEnricher(),
        };
    }
}
