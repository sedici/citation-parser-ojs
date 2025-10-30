# Autores: validación y construcción de JATS

Este documento describe cómo se validan los autores contra OpenAlex, cómo se formatean una sola vez y cómo se construye el XML JATS consumiendo ese resultado, evitando reprocesos.

## Objetivo

- Evitar que un DOI incorrecto reemplace autores en el JATS si los autores de la referencia no coinciden con OpenAlex.
- Procesar y formatear autores una única vez (validación + formateo), y que los printers solo impriman.

## Componentes

- `validators/AuthorFullNameProcessor.php`: utilitario para matchear y partir nombres; se usa en validación/formateo, no en los printers.
- `validators/AuthorValidator.php`:
  - `validateFullNameAsAuthor()`: valida autores contra OpenAlex cuando la referencia original trae autores.
  - `buildFormattedAuthors()`: genera una lista normalizada para imprimir: `['surname' => string, 'given-names' => string|null]`.
- `ReferencesManager`:
  - Tras validar, invoca `buildFormattedAuthors()` y agrega el resultado como parte del arreglo de resultados obtenidos mediante la api de OpenAlex (arreglo $result), bajo la clave: `__authors_formatted`.
- `JATSReference::setEnrichmentData()`: recibe los resultados de OpenAlex para almacenarlos en la variable de instancia `$enrichmentData` -> A su vez, este arreglo contiene `__authors_formatted`.
- `Printer/JournalPrinter.php::enrichment()`: Imprime toda la información correspondiente al enriquecimiento del XML JATS, y a su vez, accede a la clave `__authors_formatted` del arreglo `$arrayData` para imprimir los autores correctamente.

## Procesamiento único de nombres (AuthorFullNameProcessor)

La clase `AuthorFullNameProcessor` encapsula la lógica de matcheo y particionado y se invoca solo en la etapa de validación/formateo (no en los printers):
- Las iniciales se matchean en orden y sin reutilizar tokens: cada inicial debe coincidir con el siguiente token disponible. Esto evita falsos positivos como "T. T." frente a "Tomas Nahuel".

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

## Validación y formateo de autores (AuthorValidator)

El método `validateFullNameAsAuthor()` delega la comparación y particionado de nombres completos a la clase `AuthorFullNameProcessor` cuando corresponde, y `buildFormattedAuthors()` produce el arreglo que los printers consumirán:

1. Recorre los autores de la referencia y los compara contra `authorships` de OpenAlex (para el DOI).
2. Usa `AuthorFullNameProcessor::matchReferenceToDisplayName()` para determinar si hay match.
3. Devuelve `true` solo si todos los autores de la referencia encuentran match; si no, agrega errores detallados al `JATSReference`.
4. Independientemente de la validación, `buildFormattedAuthors()` arma `__authors_formatted`:
  - Si la referencia original desde el docx trae autores: intenta partir `display_name` → `surname`/`given-names` con la misma estrategia.
  - Si la referencia original desde el docx NO trae autores (caso en el que solo haya DOI, por ejemplo): no intenta partir; en este caso se realiza un fallback: `surname = display_name`, `given-names = null`.

Esto evita enriquecer con un DOI cuando los autores no corresponden.

## Enriquecimiento y construcción del XML JATS (JournalPrinter)

Cuando hay datos de OpenAlex válidos, `ReferencesManager` adjunta `__authors_formatted` al arreglo de resultados obtenidos por OpenAlex (`$result`) y `JATSReference::setEnrichmentData()` lo almacena en el arreglo `enrichmentData`. Luego, `JournalPrinter::enrichment()`:

1. Construye `<person-group person-group-type="author">`.
2. Itera `__authors_formatted` y genera directamente:
   ```xml
   <name>
     <surname>García</surname>
     <given-names>Juan Pablo</given-names>
   </name>
   ```
3. Si un autor no trae `given-names`, se imprime solo `<surname>`, que puede contener el `display_name` completo (fallback DOI-only).

De esta forma, se procesa una sola vez fuera del printer (con `AuthorFullNameProcessor`) y se evita duplicar heurísticas. El Printer SOLO debe encargarse de imprimir información específica.

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
2. `AuthorValidator::validateFullNameAsAuthor()` valida autores (si la referencia original trae autores). Si no trae, omite validación. -> Acá se podría incluir a futuro una implementación para recuperar autores mediante la API de OpenAlex.
3. `AuthorValidator::buildFormattedAuthors()` arma el arreglo con clave `__authors_formatted` según el caso (split o fallback), y `ReferencesManager` lo inserta en el arreglo correspondiente al enriquecimiento en la clase JATSReference (`$enrichmentData[]`).
4. `JATSReference::setEnrichmentData()` guarda los datos de OpenAlex y `__authors_formatted`.
5. `JournalPrinter::enrichment()` solo imprime autores desde `__authors_formatted` y agrega/actualiza el resto de elementos JATS correspondientes al enriquecimiento.

> Nota: Si se quiere endurecer o flexibilizar la validación (p. ej., distancia de Levenshtein para apellidos), hacerlo dentro de `AuthorFullNameProcessor` garantiza que la construcción del JATS siga la misma regla, sin duplicar lógica.

## Runner de pruebas y ejemplos JATS

- Script: `tests/run_author_fullname_processor.php`
- Datos: `tests/data/authors_cases.json`
- Qué hace: imprime por caso `[PASS]/[FAIL] — Matchea/No matchea` y, si matchea, muestra el `<name>` en línea.
- Archivo de salida: `tests/output/jats_names_examples.xml` con todos los `<name>` correspondientes al XML JATS generados.

Cómo ejecutar (desde `citation-parser-ojs`):
- php tests/run_author_fullname_processor.php

## Tickets pendientes / backlog

### Referencia con solo DOI (sin autores/título/fecha en la referencia original)

- Contexto
  - Cuando la referencia bibliográfica (desde el archivo docx) solo especifica el DOI, la consulta a OpenAlex permite construir correctamente el JATS completo, pero hay un problema: 
  Los autores provenientes de OpenAlex se reciben como `display_name` (cadena con nombre completo), sin ninguna distinción de nombre y apellido por separado. Esto hace que no haya manera de saber cuál es el nombre o el apellido de los autores, y por ende, no se podrían crear los tags `surname` y `given-names` correspondientes en el XML JATS.

- Estado actual
  - La construcción del JATS funciona y se enriquece con OpenAlex.
  - Si la referencia original no trae autores, se omite la validación de autores.
  - Se aplica un fallback controlado en `__authors_formatted`: `surname = display_name` y sin `given-names`.
  - `JournalPrinter` imprime directamente `__authors_formatted` (en este caso solo imprimirá `surname`, que contendrá el nombre y apellido completo sin ningún tipo de distinción).

- Problema
  - No se distinguen nombres y apellidos de forma confiable para autores obtenidos solo desde OpenAlex cuando no hay autores en la referencia original.

- Próximo
  - [ ] Integrar API de ORCID para obtener `given-names` y `family-name` cuando sea posible y reemplazar el fallback por `<given-names>` + `<surname>` reales.

- Notas/edge cases (valores de borde)
  - Si OpenAlex no trae autores, mantener el comportamiento de no fallar y no validar.

---

### Nuevo tipo de fuente de OpenAlex: "E book platform"

- Contexto
  - Hasta ahora se manejó exitosamente el tipo `Journal`. Se detectó un nuevo `source.type` en OpenAlex: `E book platform` (o variantes de capitalización).

- Comportamiento actual
  - No hay enriquecimiento específico implementado para este tipo; se desconoce si debe mapearse a `Book`, `Webpage` u otro.

- Tareas a realizar
  - [ ] Investigar qué representa exactamente `E book platform` en OpenAlex y su mapeo adecuado a `publication-type` JATS.
  - [ ] Definir si corresponde usar `BookPrinter` (y agregar `enrichment()` similar a `JournalPrinter`) o crear un `Printer` específico.
  - [ ] Incorporar pruebas con JSON de ejemplo (añadir fixtures en `Printer/JournalPrinter_enrichment*.json` o nuevo set específico) y ejemplos de salida XML.
  - [ ] Ajustar `JATSReference::enrichment()` para setear `publication-type` consistente con la decisión tomada.

- Criterios de aceptación
  - [ ] Dado un resultado de OpenAlex con `source.type = "E book platform"`, el sistema enriquece `element-citation` con los campos esperados (título, año, publisher/editor si aplica, etc.).
  - [ ] Se generan los autores coherentes con la estrategia actual (o el fallback si no hay split de nombres).
  - [ ] No se registran errores si el enriquecimiento se completa correctamente.

- Notas
  - Evaluar si algunos casos de `E book platform` son en realidad plataformas de distribución y no obras; podría requerir lógica de exclusión o tratamiento especial.