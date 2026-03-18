# 🚀 Buscador de Empresas e Leads (Pro) - Mais Marketing RJ

Um sistema robusto e responsivo desenvolvido para otimizar a prospecção de clientes B2B. A ferramenta realiza buscas automatizadas de empresas por segmento e região, extraindo dados estratégicos de contato e tecnologia para facilitar a abordagem comercial.

## ✨ Funcionalidades

* **Busca Geoposicionada:** Filtros precisos por Segmento, Bairro, Cidade e Estado.
* **Integração com Google Maps:** Plotagem automática dos leads encontrados em um mapa interativo, com marcadores personalizados e *InfoWindows* de contato.
* **Detecção Inteligente de WhatsApp:** O sistema analisa os números de telefone em tempo real. Se identificar um número de celular válido (padrão DDI+DDD+9), gera automaticamente um botão para iniciar uma conversa no WhatsApp Web/App.
* **Scraping Estratégico:** Identifica se os sites dos leads possuem Pixel da Meta (Facebook) e Google Analytics instalados, gerando oportunidades para vendas de serviços de tráfego pago e BI.
* **Exportação de Relatórios:**
    * **Excel (.xlsx):** Gera planilhas limpas e formatadas utilizando a biblioteca SheetJS.
    * **PDF:** Gera relatórios em formato paisagem com layout listrado e cabeçalho customizado utilizando jsPDF e jsPDF-AutoTable. (Ainda não funcional)
* **Interface Responsiva (Mobile First):** Construído com Bootstrap 5, garantindo que a tela se divida em 50/50 no desktop e empilhe o painel de resultados com o mapa interativo no smartphone.
* **Segurança:** Acesso restrito protegido por controle de sessão nativo do PHP.

## 🛠️ Tecnologias Utilizadas

**Front-end:**
* HTML5 / CSS3
* JavaScript (Vanilla)
* Bootstrap 5
* Google Fonts (Roboto)

**Back-end & APIs:**
* PHP 8+ (Gestão de sessão e processamento de requisições web scraping)
* Google Maps JavaScript API

**Bibliotecas Externas (CDN):**
* [SheetJS (xlsx)](https://sheetjs.com/) - Para exportação de planilhas.
* [jsPDF](https://parall.ax/products/jspdf) - Para geração do arquivo PDF.
* [jsPDF-AutoTable](https://github.com/simonbengtsson/jsPDF-AutoTable) - Para renderização da tabela no PDF.

## 📂 Estrutura Básica de Arquivos

```text
/
├── index.php         # Interface principal do painel (Buscador, Mapa e Tabela)
├── buscar.php        # Script backend de web scraping que retorna o JSON de leads
├── login.php         # Tela de autenticação do sistema
├── logout.php        # Script para destruição da sessão do usuário
└── README.md         # Documentação do projeto
