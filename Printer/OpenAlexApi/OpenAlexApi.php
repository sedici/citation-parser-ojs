<?php
class OpenAlexAPI {
    private $baseUrl = "https://api.openalex.org/institutions?search=";

    public function searchInstitutions(string $query): array {
        // Construye la URL completa con el parámetro de búsqueda
        $url = $this->baseUrl . urlencode($query);

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
}

?>
