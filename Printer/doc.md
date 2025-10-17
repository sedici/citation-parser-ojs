# Autores: validación y construcción de JATS con una estrategia compartida

Este documento describe cómo se validan los autores contra OpenAlex y cómo se construye el XML JATS para los autores usando la misma estrategia de nombres, de forma consistente y reutilizable.

## Objetivo

- Evitar que un DOI incorrecto reemplace autores en el JATS si los autores de la referencia no coinciden con OpenAlex.
- Reutilizar la misma heurística de nombres para dos tareas: validar y construir `<person-group>` con `<surname>` y `<given-names>`.

## Componentes

- `validators/AuthorFullNameProcessor.php`: utilitario reutilizable para matchear y partir nombres.
- `validators/AuthorValidator.php`: usa la estrategia para validar autores contra OpenAlex.
- `JATSReference::setEnrichmentData()`: adjunta autores originales en `__reference_authors`.
- `Printer/JournalPrinter.php::enrichment()`: usa la estrategia para generar `<surname>` y `<given-names>` con datos de OpenAlex.

## Estrategia única de nombres (AuthorFullNameProcessor)

La clase `AuthorFullNameProcessor` encapsula la lógica para:
- Las iniciales se matchean en orden y sin reutilizar tokens: cada inicial debe coincidir con el siguiente token disponible. Esto evita falsos positivos como "T. T." frente a "Tomas Nahuel" (la segunda "T" no puede volver a machar "Tomas").

Parámetros esperados/Valores retornados
- Input: `array $referenceAuthor` con claves `apellido` y `nombres`, y `string $openAlexDisplayName`.
- Output: `['surname' => string, 'given-names' => string]` si hay match; `null` si no hay match.

Ejemplo:
- Referencia: `apellido = "García", nombres = "J. P."`
- OpenAlex: `display_name = "Juan Pablo García"`
- Resultado: `surname = "García"`, `given-names = "Juan Pablo"`.

Limitaciones conocidas y mejoras posibles:
- Apellidos compuestos y partículas ("de", "da", "van der"): la estrategia actual requiere que el `apellido` de la referencia figure como subcadena en `display_name`. Puede ampliarse con normalización y diccionario de partículas.
- Diacríticos: se usa `stripos` (case-insensitive). Si se necesita normalizar acentos, agregar preprocesamiento.

## Validación de autores (AuthorValidator)

El método `validateFullNameAsAuthor()` mantiene el flujo existente, pero ahora delega la comparación y particionado de nombres completos a `AuthorFullNameProcessor`:

1. Recorre los autores de la referencia y los compara contra `authorships` de OpenAlex (para el DOI).
2. Usa `AuthorFullNameProcessor::matchReferenceToDisplayName()` para determinar si hay match.
3. Devuelve `true` solo si todos los autores de la referencia encuentran match; si no, agrega errores detallados al `JATSReference`.

Esto evita enriquecer con un DOI cuando los autores no corresponden.

## Enriquecimiento y construcción del XML JATS (JournalPrinter)

Cuando hay datos de OpenAlex válidos, `JATSReference::setEnrichmentData()` inyecta `__reference_authors` (los autores originales de la referencia) al array de enriquecimiento. Luego, `JournalPrinter::enrichment()`:

1. Construye `<person-group person-group-type="author">`.
2. Para cada `display_name` de OpenAlex, intenta partirlo en `surname` y `given-names` usando la misma estrategia contra `__reference_authors`.
3. Si logra partirlo, genera:
   ```xml
   <name>
     <surname>García</surname>
     <given-names>Juan Pablo</given-names>
   </name>
   ```
4. Si no puede (no hay match), aplica un fallback conservador para no perder datos:
   ```xml
   <name>
     <surname>Juan Pablo García</surname>
   </name>
   ```

De esta forma, la misma heurística guía tanto la validación como la construcción del JATS, garantizando consistencia. Ambos enfoques (validación y construcción) recurren a `AuthorFullNameProcessor`.

## Casos específicos (ejemplos)

- Referencia: `T. T.` vs OpenAlex: `Tomas Nahuel` → Resultado: `NO MATCH` (correcto; no se permite reutilizar “Tomas”).
- Referencia: `T. N.` vs OpenAlex: `Tomas Nahuel` → Resultado: `MATCH` → `given-names = "Tomas Nahuel"`.
- Referencia: `J. P.` vs OpenAlex: `Juan Pablo` → Resultado: `MATCH` → `given-names = "Juan Pablo"`.
- Referencia: `J. P.` vs OpenAlex: `Juan-Pablo` → Resultado: `MATCH` → `given-names = "Juan-Pablo"` (se divide en subpartes por guion para matchear iniciales).
- Referencia: `A.` vs OpenAlex: `Ana María` → Resultado: `MATCH` → `given-names = "Ana María"` (la inicial A matchea el primer token disponible).
- Referencia: `T. N. T.` vs OpenAlex: `Tomas Alfajor Termas` → Resultado: `NO MATCH` (falla en la “N”, no hay token que empiece con N).

### Paso a paso de algunos casos

- `T. T.` vs `Tomas Nahuel`
  1) T → matchea `Tomas` en display_name y luego se borra; nombres restantes en display_name: [`Nahuel`]
  2) T → no hay token `T*` disponible en display_name (solo `Nahuel`), por ende, falla → `NO MATCH`

- `T. N.` vs `Tomas Nahuel`
  1) T → matchea `Tomas` en display_name y luego se borra; nombres restantes: [`Nahuel`]
  2) N → matchea `Nahuel` en display_name y se borra; nombres restantes: [] → `MATCH` → `given-names = "Tomas Nahuel"`

- `J. P.` vs `Juan-Pablo`
  1) J → matchea la subparte `Juan` de display_name y consume todo el token `Juan-Pablo` (se lo considera como un único token para la secuencia, con subpartes para verificar inciales)
  2) P → como el token usado ya fue consumido, no se reutiliza. Si el nombre completo tuviera otro token `P*` a la derecha (p. ej. `Juan-Pablo Perez`), matchearía `Perez`. En la práctica, en `Juan-Pablo` a secas retornamos `MATCH` y los `given-names` que quedan son `Juan-Pablo`.

- `T. N. T.` vs `Tomas Alfajor Termas`
  1) T → matchea `Tomas` y se consume; restantes: [`Alfajor`, `Termas`]
  2) N → no hay `N*` entre los tokens restantes (se pueden saltar tokens que no matchean, pero debe existir uno que sí). Falla → `NO MATCH`

## Flujo de datos (resumen)

1. `ReferencesManager` parsea referencias y, si hay DOI, consulta OpenAlex.
2. `AuthorValidator::validateFullNameAsAuthor()` valida autores con la estrategia.
3. Si la validación pasa, `JATSReference::setEnrichmentData()` guarda los datos de OpenAlex y `__reference_authors`.
4. `JournalPrinter::enrichment()` agrega/actualiza elementos JATS, incluyendo `<person-group>` con `surname` y `given-names`.

> Nota: Si se quiere endurecer o flexibilizar la validación (p. ej., distancia de Levenshtein para apellidos), hacerlo dentro de `AuthorFullNameProcessor` garantiza que la construcción del JATS siga la misma regla, sin duplicar lógica.

## Runner de pruebas y ejemplos JATS

- Script: `tests/run_author_fullname_processor.php`
- Datos: `tests/data/authors_cases.json`
- Qué hace: imprime por caso `[PASS]/[FAIL] — Matchea/No matchea` y, si matchea, muestra el `<name>` en línea.
- Archivo de salida: `tests/output/jats_names_examples.xml` con todos los `<name>` correspondientes al XML JATS generados.

Cómo ejecutar (desde `citation-parser-ojs`):
- php tests/run_author_fullname_processor.php