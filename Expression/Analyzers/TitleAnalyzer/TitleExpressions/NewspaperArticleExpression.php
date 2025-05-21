<?php

include_once('GenericExpression.php');

class NewspaperArticleExpression extends GenericExpression
{
    private static $newspaperArticleExpression = [
        //Fernández, P. (24 de abril de 1842). Ejercicios de lengua castellana. El Mercurio, p. 3. => Detected as a Book reference  
        //Follari, P. (2 de agosto de 2017). Los medios son todos públicos. Página 12. https://www.pagina12.com.ar/53914-los-medios-son-todos-publicos => Detected as a Book reference
        
        //El CONICET superó a la NASA en un ranking mundial como la mejor institución científica. (12 de marzo de 2024). Eldestape. https://www.eldestapeweb.com/tecnologia/conicet/el-conicet-supero-a-la-nasa-en-un-ranking-mundial-como-la-mejor-institucion-cientifica-202431214330
        //or
        //Fernández, P. (24 de abril de 1842). El Mercurio, p. 3. => Detected as a Webpage reference
        //or
        //Cómo se preocupa Gimnasia por la cultura física de los niños. (10 de noviembre de 1933). El Argentino, p. 8. => Detected as a Webpage reference
        '/\).\s(?P<source>[A-Z][A-Za-zÀ-ÿ\s\:\;\-]+(,\sp.(\s)?\d+)?)(.\s(https|http)?:\/\/[^\s]+)?/' => 'webpage',
    ];

    public static function getPattern()
    {
        return self::$newspaperArticleExpression;
    }
}