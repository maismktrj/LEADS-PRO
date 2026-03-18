<?php
// api_google.php
function buscarEmpresasGoogle($segmento, $bairro, $cidade, $estado, $apiKey) {
    // Agora incluímos o bairro na string de busca
    $busca = trim("$segmento $bairro $cidade $estado");
    $query = urlencode($busca);
    $urlBusca = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=$query&key=$apiKey";
    
    $resposta = file_get_contents($urlBusca);
    $dados = json_decode($resposta, true);
    
    $resultados = [];
    
    if (isset($dados['results'])) {
        foreach ($dados['results'] as $local) {
            $place_id = $local['place_id'];
            
            // Pega as coordenadas que já vêm na primeira chamada
            $lat = $local['geometry']['location']['lat'] ?? null;
            $lng = $local['geometry']['location']['lng'] ?? null;
            
            // Busca detalhes (Telefone, Site)
            $urlDetalhes = "https://maps.googleapis.com/maps/api/place/details/json?place_id=$place_id&fields=name,formatted_phone_number,website&key=$apiKey";
            $respDetalhes = json_decode(file_get_contents($urlDetalhes), true);
            
            $resultados[] = [
                'nome' => $respDetalhes['result']['name'] ?? $local['name'],
                'telefone' => $respDetalhes['result']['formatted_phone_number'] ?? 'Não informado',
                'site' => $respDetalhes['result']['website'] ?? null,
                'lat' => $lat, // Adicionado
                'lng' => $lng  // Adicionado
            ];
        }
    }
    return $resultados;
}
?>