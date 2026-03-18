<?php
// analisador_site.php
function analisarSite($url) {
    if (!$url) return ['pixel_meta' => false, 'analytics' => false, 'redes_sociais' => []];

    // Configuração do cURL para simular um navegador real e evitar bloqueios
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    $html = curl_exec($ch);
    curl_close($ch);

    if (!$html) return ['pixel_meta' => false, 'analytics' => false, 'redes_sociais' => []];

    // Verifica Meta Pixel (Antigo Facebook Pixel)
    $temPixelMeta = preg_match('/fbq\(/i', $html) || preg_match('/connect\.facebook\.net/i', $html);
    
    // Verifica Google Analytics (GTAG ou analytics.js)
    $temAnalytics = preg_match('/gtag\(/i', $html) || preg_match('/google-analytics\.com/i', $html);

    // Extrai links de Redes Sociais
    $redes_sociais = [];
    if (preg_match_all('/href=["\'](https?:\/\/(www\.)?(instagram\.com|facebook\.com|linkedin\.com)\/[^"\']+)["\']/i', $html, $matches)) {
        $redes_sociais = array_unique($matches[1]);
    }

    return [
        'pixel_meta' => $temPixelMeta ? 'Sim' : 'Não',
        'analytics' => $temAnalytics ? 'Sim' : 'Não',
        'redes_sociais' => implode(", ", $redes_sociais)
    ];
}
?>