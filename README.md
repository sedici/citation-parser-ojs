# Citation Parser OJS

**Citation Parser OJS** es un sistema desarrollado en PHP para analizar referencias bibliográficas y generar automáticamente su representación en **XML JATS**.  

Actualmente soporta referencias en **formato APA 7ma edición**, pero la estructura del proyecto está diseñada para permitir en el futuro la incorporación de otros estilos de referencias, facilitando su expansión y adaptación a distintos estándares bibliográficos.

Está pensado para integrarse con el proyecto [docxToJats](https://github.com/Vitaliy-1/docxToJats/tree/main) y utilizarse en flujos de marcación de artículos en **OJS**, permitiendo la generación automática de XML de referencias para publicaciones científicas.

---

## Características

- Analiza referencias bibliográficas en estilo APA 7.
- Arquitectura extensible para agregar soporte a otros estilos bibliográficos.
- Extrae campos clave como:
  - Autor(es)
  - Año
  - Título del artículo o libro
  - Fuente (revista, editorial, DOI, URL)
- Genera automáticamente XML JATS de cada referencia.
- Diseñado para integrarse como módulo en otros proyectos PHP y en flujos de trabajo de OJS.

---
##  Desarrollo futuro

Conexión con **OpenAlex** para aprovechar DOI y enriquecer automáticamente la información de las referencias (autores, títulos, journals, etc.).

## Licencia

Este proyecto está licenciado bajo **GNU General Public License v3.0 (GPLv3)**.  
Puedes ver la licencia completa en el archivo [LICENSE](LICENSE).

- Permite usar, modificar y distribuir el software.  
- Obliga a mantener la misma licencia en derivados.  
- Requiere dar atribución a **PREBI-SEDICI, Universidad Nacional de La Plata**.
