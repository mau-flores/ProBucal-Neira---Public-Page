<?php
class DniAPI
{
    private $token;
    private $apiUrl = "https://apiperu.dev/api/dni";

    public function __construct()
    {
        // Intentar cargar token desde el archivo de configuración
        $configPath = __DIR__ . '/../config/api_config.php';
        if (file_exists($configPath)) {
            // Esto define $api_token
            include $configPath;
            if (isset($api_token) && !empty($api_token) && $api_token !== 'CAMBIA_POR_TU_TOKEN_AQUI') {
                $this->token = $api_token;
            }
        }

        if (empty($this->token)) {
            // Lanzar excepción para que quien use la clase la gestione
            throw new Exception('API token no configurado. Coloca tu token en php/config/api_config.php');
        }
    }

    public function consultarDNI($dni)
    {
        $params = json_encode(['dni' => $dni]);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . '/' . $dni,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token
            ],
            CURLOPT_VERBOSE => true
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return json_encode(['error' => $err]);
        }

        return $response;
    }
}
?>