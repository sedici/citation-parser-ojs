# APA7-Reference-Parser
Desarrollar un sistema de análisis y extracción de información de referencias bibliográficas en formato APA7 utilizando expresiones regulares en PHP.



# Tabla de elmentos distintivos para el reconocimiento del referencias

| Libro      | Capitulo de libro | Revista | Congresos | Sitios dinamicos |
| ------------- | ------------- | ------------- |------------- |------------- |
| Content Cell  | Content Cell  |
| Content Cell  | Content Cell  |

# Terea siendo relizada
1. Charapte, autores. NO FUNCIONAN LOS ACENTOS.


# Problemas sin solucionar

1. ¿Como resolver el camelcase de las editoriales y las revistas?
2. ¿Como generaliza?
3. Tenemos que saber que hacemos cuando una referencia no conincide.
4. Tenemo que separar las paginas entre first y last. Tanto para imprimir como para el JATS
5. Congresos y tesisnas


Listado faltantes:
1. congresos, sitios dinamicos y tesis.
4. nro de articulo de revista
5. libros
    a. Traducidos 
    b. Idioma original 
6. Punteo general, como la aceptancion de ".", ":", ";". 



JATS:
1. Capitulo de libro: https://jats.nlm.nih.gov/archiving/tag-library/1.3/element/part-title.html
2. tesis: https://jats.nlm.nih.gov/archiving/tag-library/1.3/element/part-title.html