<?php
session_start();
// Se não existir uma sessão de usuário ativa, manda de volta pro login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador de Empresas e Leads (Pro) - Mais Marketing RJ</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7f6;
        }

        /* --- Comportamento Responsivo Tela Dividida --- */
        @media (min-width: 992px) {
            /* Desktop: Tela cheia, sem rolagem no body, rolagem apenas no painel */
            body { overflow: hidden; height: 100vh; }
            #painel-lateral { height: 100vh; overflow-y: auto; }
            #mapa-container { height: 100vh; }
        }
        
        @media (max-width: 991px) {
            /* Mobile/Tablet: Empilhado, mapa com altura fixa embaixo */
            #mapa-container { height: 60vh; width: 100%; border-top: 3px solid #0d6efd; }
        }

        /* --- Estilos Adicionais --- */
        #painel-lateral { background: #ffffff; box-shadow: 2px 0 10px rgba(0,0,0,0.1); z-index: 10; }
        
        .whats-container { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .btn-whats {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;
            text-decoration: none; transition: 0.2s;
        }
        .btn-whats.ativo { background-color: #25D366; color: white; box-shadow: 0 2px 4px rgba(37, 211, 102, 0.3); }
        .btn-whats.ativo:hover { background-color: #128C7E; color: white; }
        .btn-whats.inativo { background-color: #e0e0e0; color: #888; cursor: not-allowed; pointer-events: none; }
        
        .icone-whats { width: 14px; height: 14px; margin-right: 4px; fill: currentColor; }

        /* Spinner Animado CSS */
        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid #ffffff;
            border-radius: 50%; width: 16px; height: 16px;
            animation: spin 0.8s linear infinite; display: inline-block;
            vertical-align: middle; margin-right: 8px; margin-top: -2px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="row g-0">
            
            <div class="col-lg-6 col-12 p-4" id="painel-lateral">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0 fs-4 text-dark fw-bold">Buscador de Empresas - LEADS</h2>
                    <a href="https://maismktrj.com.br/leads/logout.php" class="btn btn-danger btn-sm px-3 fw-bold">SAIR</a>
                </div>

                <form id="formBusca" class="mb-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small mb-1">Segmento</label>
                        <input type="text" class="form-control" id="segmento" placeholder="Ex: Escritório de Advocacia" required>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-md-5 col-12">
                            <label class="form-label fw-medium text-secondary small mb-1">Bairro</label>
                            <input type="text" class="form-control" id="bairro" placeholder="Ex: Icaraí">
                        </div>
                        <div class="col-md-5 col-8">
                            <label class="form-label fw-medium text-secondary small mb-1">Cidade</label>
                            <input type="text" class="form-control" id="cidade" placeholder="Ex: Niterói" required>
                        </div>
                        <div class="col-md-2 col-4">
                            <label class="form-label fw-medium text-secondary small mb-1">Estado</label>
                            <input type="text" class="form-control" id="estado" placeholder="RJ" required maxlength="2">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" id="btnBuscar">Buscar Empresas</button>
                </form>

                <div id="acoesExportacao" style="display:none;" class="mb-3">
                    <button class="btn btn-success btn-sm me-2" onclick="exportarExcel()">Exportar Excel</button>
                    <button class="btn btn-danger btn-sm" onclick="exportarPDF()">Exportar PDF</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped border text-nowrap" id="tabelaResultados" style="font-size: 13px;">
                        <thead class="table-light">
                            <tr>
                                <th>Empresa</th>
                                <th>Contato</th>
                                <th>Site</th>
                                <th>Pixel</th>
                                <th>Analytics</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="5" class="text-center text-muted py-4">Preencha os filtros para buscar</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-6 col-12">
                <div id="mapa-container"></div>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    
    <script>
        let map;
        let markers = [];
        let dadosGlobais = []; 

        // Função para identificar se é celular e criar o link do WhatsApp
        function processarTelefone(telefoneOriginal) {
            if (!telefoneOriginal || telefoneOriginal === 'Não informado') {
                return { isCelular: false, link: '', numeroLimpo: '' };
            }

            let numeros = telefoneOriginal.replace(/\D/g, '');

            if (numeros.length === 10 || numeros.length === 11) {
                numeros = '55' + numeros;
            }

            let isCelular = false;
            if (numeros.length === 13 && numeros.charAt(4) === '9') {
                isCelular = true;
            }

            return {
                isCelular: isCelular,
                link: isCelular ? `https://wa.me/${numeros}` : '',
                numeroLimpo: numeros
            };
        }

        // SVG do ícone do WhatsApp
        const iconeWhatsSVG = `<svg class="icone-whats" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm.029 18.88c-1.161 0-2.305-.292-3.318-.844l-3.677.964.984-3.595c-.607-1.052-.927-2.246-.926-3.468.001-5.824 4.74-10.563 10.564-10.563 5.826 0 10.564 4.74 10.564 10.564 0 5.824-4.74 10.564-10.564 10.564z"/></svg>`;

        function initMap() {
            map = new google.maps.Map(document.getElementById("mapa-container"), {
                center: { lat: -22.8808, lng: -43.1043 },
                zoom: 13,
                mapTypeId: 'roadmap',
                styles: [ { "featureType": "poi", "elementType": "labels", "stylers": [ { "visibility": "off" } ] } ]
            });
        }

        document.getElementById('formBusca').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('btnBuscar');
            btn.innerHTML = '<span class="spinner"></span> Buscando e varrendo sites (pode levar alguns segundos)...';
            btn.disabled = true;
            
            const formData = new FormData();
            formData.append('segmento', document.getElementById('segmento').value);
            formData.append('bairro', document.getElementById('bairro').value);
            formData.append('cidade', document.getElementById('cidade').value);
            formData.append('estado', document.getElementById('estado').value);

            try {
                const response = await fetch('buscar.php', { method: 'POST', body: formData });
                const dados = await response.json();

                dadosGlobais = dados;

                const tbody = document.querySelector('#tabelaResultados tbody');
                tbody.innerHTML = ''; 

                markers.forEach(m => m.setMap(null));
                markers = [];
                const limitesMapa = new google.maps.LatLngBounds();

                if (dados.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Nenhuma empresa encontrada.</td></tr>';
                    document.getElementById('acoesExportacao').style.display = 'none';
                } else {
                    dados.forEach(emp => {
                        const telInfo = processarTelefone(emp.telefone);
                        
                        let btnWhatsHTML = '';
                        if (telInfo.isCelular) {
                            btnWhatsHTML = `<a href="${telInfo.link}" target="_blank" class="btn-whats ativo" title="Chamar no WhatsApp">${iconeWhatsSVG} Chamar</a>`;
                        } else {
                            btnWhatsHTML = `<span class="btn-whats inativo" title="Número Fixo">${iconeWhatsSVG} Fixo</span>`;
                        }

                        tbody.innerHTML += `
                            <tr>
                                <td><strong>${emp.nome}</strong></td>
                                <td>
                                    <div class="whats-container">
                                        ${emp.telefone}
                                        ${btnWhatsHTML}
                                    </div>
                                </td>
                                <td>${emp.site !== 'Sem site' ? `<a href="${emp.site}" target="_blank" class="text-decoration-none fw-medium">Acessar</a>` : '-'}</td>
                                <td>${emp.pixel}</td>
                                <td>${emp.analytics}</td>
                            </tr>
                        `;

                        if (emp.lat && emp.lng) {
                            const posicao = { lat: parseFloat(emp.lat), lng: parseFloat(emp.lng) };
                            const marker = new google.maps.Marker({
                                map: map,
                                position: posicao,
                                title: emp.nome,
                                animation: google.maps.Animation.DROP
                            });
                            
                            const infoWindow = new google.maps.InfoWindow({
                                content: `<div style="font-family:'Roboto',sans-serif;"><b>${emp.nome}</b><br>${emp.telefone}</div>`
                            });
                            marker.addListener("click", () => { infoWindow.open(map, marker); });

                            markers.push(marker);
                            limitesMapa.extend(posicao);
                        }
                    });
                    
                    document.getElementById('acoesExportacao').style.display = 'block';
                    
                    if (markers.length > 0) {
                        map.fitBounds(limitesMapa);
                    }

                    // Rolagem automática para o mapa no Mobile
                    if (window.innerWidth < 992) {
                        document.getElementById('mapa-container').scrollIntoView({ behavior: 'smooth' });
                    }
                }

            } catch (erro) {
                console.error('Erro:', erro);
                alert('Erro ao processar dados. Verifique a conexão ou a aba Network.');
            } finally {
                btn.innerHTML = 'Buscar Empresas';
                btn.disabled = false;
            }
        });

        // Exportação para Excel
        function exportarExcel() {
            if (dadosGlobais.length === 0) {
                alert("Nenhum dado para exportar.");
                return;
            }

            const dadosExcel = dadosGlobais.map(emp => ({
                "Empresa": emp.nome,
                "Telefone": emp.telefone,
                "Site": emp.site !== 'Sem site' ? emp.site : 'Não possui',
                "Pixel da Meta": emp.pixel,
                "Google Analytics": emp.analytics,
                "Redes Sociais": emp.redes
            }));

            const worksheet = XLSX.utils.json_to_sheet(dadosExcel);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Leads Gerados");

            const wscols = [
                {wch: 40}, {wch: 20}, {wch: 35}, {wch: 15}, {wch: 20}, {wch: 50}
            ];
            worksheet['!cols'] = wscols;

            XLSX.writeFile(workbook, "Leads_Mais_Marketing.xlsx");
        }

        // Exportação para PDF
        function exportarPDF() {
            if (dadosGlobais.length === 0) {
                alert("Nenhum dado para exportar.");
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('landscape'); 

            doc.setFontSize(16);
            doc.text("Relatório de Leads - Mais Marketing RJ", 14, 15);
            
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text("Gerado pelo Buscador de Empresas (Pro)", 14, 22);

            const colunas = ["Empresa", "Telefone", "Site", "Pixel da Meta", "Google Analytics"];
            
            const linhas = dadosGlobais.map(emp => [
                emp.nome,
                emp.telefone,
                emp.site !== 'Sem site' ? emp.site : 'Não possui',
                emp.pixel,
                emp.analytics
            ]);

            doc.autoTable({
                head: [colunas],
                body: linhas,
                startY: 30,
                theme: 'striped',
                styles: { 
                    font: 'helvetica',
                    fontSize: 9,
                    cellPadding: 4
                },
                headStyles: { 
                    fillColor: [13, 110, 253], // Cor primária do Bootstrap
                    textColor: [255, 255, 255]
                },
                alternateRowStyles: {
                    fillColor: [245, 245, 245]
                }
            });

            doc.save("Leads_Mais_Marketing_RJ.pdf");
        }
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDICo5HYe97t8FriF15BdyD2liI-txiNho&callback=initMap"></script>

</body>
</html>