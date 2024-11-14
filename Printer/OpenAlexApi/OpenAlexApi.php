<?php
class OpenAlexAPI {
    private string $institutionsUrl = "https://api.openalex.org/institutions?search=";
    private string $worksUrl = "https://api.openalex.org/works/";
    private string $doisUrl = "https://api.openalex.org/works?filter=doi:";


    public function searchInstitutions(string $query): array {
        // Construye la URL completa con el parámetro de búsqueda
        $url = $this->institutionsUrl . urlencode($query);

        // Realiza la solicitud y obtiene la respuesta en JSON
        $response = file_get_contents($url);

        // Si hay algún error en la solicitud, devuelve un mensaje de error
        if ($response === FALSE) {
            return ["error" => "No se pudo conectar a la API"];
        }

        // Decodifica el JSON en un array asociativo
        $data = json_decode($response, true);

        // Verifica si el campo 'meta' tiene un count mayor a 0 y si existen resultados
        if (isset($data['meta']['count']) && $data['meta']['count'] > 0 && isset($data['results'][0])) {
            // Retorna solo los datos de interés de la primera posición de 'results'
            return [
                "id" => $data['results'][0]['id'] ?? null,
                "ror" => $data['results'][0]['ror'] ?? null,
                "display_name" => $data['results'][0]['display_name'] ?? null,
                "type" => $data['results'][0]['type'] ?? null
            ];
        }

        // Si no hay resultados válidos, devuelve un mensaje indicando que no se encontró información
        return ["error" => "No se encontraron instituciones con el término de búsqueda proporcionado"];
    }

    public function searchWorksWhitDoi(string $query): array {

        /*
            title
            publication_year
            publication_date
            ids
            primary_location{
                source:{
                    display_name
                    issn_l
                }
            }
            authorships:{
                0:{
                    author:{
                        display_name:
                        orcid: 
                    }
                    institution:{
                        0:{
                            display_name
                            ror

                        }
                    }   
                }
                <n>:
            }

            */
        // Construye la URL completa con el parámetro de búsqueda
        $url = $this->worksUrl . urlencode($query);
    
        // Realiza la solicitud y obtiene la respuesta en JSON
        $response = file_get_contents($url);
    
        // Si hay algún error en la solicitud, devuelve un mensaje de error
        if ($response === FALSE) {
            return ["error" => "No se pudo conectar a la API"];
        }
    
        // Decodifica el JSON en un array asociativo
        $data = json_decode($response, true);
        print_r($data['authorships']);
        
        $result = $data; // Obtenemos el primer resultado
        
            // Preparamos el arreglo con los datos de interés
            $authors = [];
            foreach ($result['authorships'] as $index => $authorship) {
                $authors[$index]['display_name'] = $authorship['author']['display_name'] ?? null;
                $authors[$index]['orcid']  = $authorship['author']['orcid'] ?? null;
            }
            $result['authors'] =  $authors;
            // Retorna los datos de interés en formato de arreglo
            
            return [
                "title" => $result['title'] ?? null,
                "publication_year" => $result['publication_year'] ?? null,
                "publication_date" => $result['publication_date'] ?? null,
                "source_display_name" => $result['primary_location']['source']['display_name'] ?? null,
                "source_issn_l" => $result['primary_location']['source']['issn_l'] ?? null,
                "authors" => $result['authors'] ?? null,
            ];
    }
    
    public function searchWorksListWithDoi(array $dois) {
        // Verificar que el array no esté vacío
        if (empty($dois)) {
            throw new InvalidArgumentException("La lista de DOIs no puede estar vacía.");
        }

        // Concatenar los DOIs con "|"
        $doiList = implode('|', $dois);
        
        // Construir la URL de la solicitud
        $url = $this->doisUrl . urlencode($doiList);
        // Realizar la solicitud GET
        $response = file_get_contents($url);
        // Convertir la respuesta a JSON y retornarla
        return $response;
    }


}


/*
$openalex = new OpenAlexAPI();
$result = $openalex->searchWorksWhitDoi('https://doi.org/10.24215/23143738e136');

print_r($result);
*/
?>
