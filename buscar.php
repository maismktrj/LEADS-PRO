<?php
// DÁ TEMPO INFINITO PARA O PHP NÃO TRAVAR NA VARREDURA DOS SITES
set_time_limit(0); 
session_start();

// Proteção da API: Se não estiver logado, bloqueia a busca
if (!isset($_SESSION['usuario_id'])) {
    header('Content-Type: application/json');
    die(json_encode([['nome' => 'Erro de Acesso', 'telefone' => '-', 'site' => 'Não autorizado', 'pixel' => '-', 'analytics' => '-', 'redes' => 'Faça login no sistema.']]));
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexao.php'; 
require 'api_google.php';
require 'analisador_site.php';

// Agora o sistema pega o ID de quem realmente está logado!
$usuario_id = $_SESSION['usuario_id'];
$apiKey = 'COLE_SUA_API_AQUI'; // Lembre-se de colocar sua chave real aqui

$segmento = $_POST['segmento'] ?? '';
$bairro   = $_POST['bairro'] ?? '';
$cidade   = $_POST['cidade'] ?? '';
$estado   = $_POST['estado'] ?? '';

try {
    $stmt = $pdo->prepare("INSERT INTO logs_pesquisa (usuario_id, segmento, cidade, estado) VALUES (?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $segmento, $cidade, $estado]);
} catch (PDOException $e) {
    // Ignora erro de log para não parar o sistema
}

$empresas = buscarEmpresasGoogle($segmento, $bairro, $cidade, $estado, $apiKey);

$dadosFinais = [];

foreach ($empresas as $emp) {
    $analise = analisarSite($emp['site']);
    
    $dadosFinais[] = [
        'nome'      => $emp['nome'],
        'telefone'  => $emp['telefone'],
        'site'      => $emp['site'] ?? 'Sem site',
        'pixel'     => $analise['pixel_meta'],
        'analytics' => $analise['analytics'],
        'redes'     => $analise['redes_sociais'],
        'lat'       => $emp['lat'], 
        'lng'       => $emp['lng']  
    ];
}

// 2. AQUI ESTÁ O JSON SEGURO (BEM NO FINAL DO ARQUIVO)
header('Content-Type: application/json; charset=utf-8');

// O JSON_INVALID_UTF8_IGNORE ignora caracteres zoados que possam vir de sites malfeitos
$json = json_encode($dadosFinais, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);

if ($json === false) {
    // Se der erro ao montar o JSON, ele te avisa o porquê em vez de ficar tela branca
    echo json_encode([
        ['nome' => 'Erro interno', 'telefone' => '-', 'site' => 'Sem site', 'pixel' => '-', 'analytics' => '-', 'redes' => 'Falha ao gerar JSON: ' . json_last_error_msg()]
    ]);
} else {
    // Se deu tudo certo, cospe os resultados
    echo $json;
}
?>
