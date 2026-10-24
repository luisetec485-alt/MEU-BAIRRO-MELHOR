<?php
require_once __DIR__.'/../model/Seguranca.php';
Seguranca::iniciarSessao();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$categorias = $categorias ?? [];
$subcategorias = $subcategorias ?? [];
$problemas = $problemas ?? [];

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$usuarioLogadoId = (int) $_SESSION['id'];
$usuarioEhAdmin = (int) ($_SESSION['nivelAcesso'] ?? 1) === 0;

?>

<?php include 'header.php'; ?>

<section class="screen">

    <div class="dashboard-container max-width-large">

        <div class="welcome-box">

            <h2>Problemas Registrados</h2>

            <p>
                Acompanhe e pesquise os problemas relatados na comunidade.
            </p>

            <!-- FILTROS -->

            <div class="filtros-container">

                <label class="sr-only" for="campoBusca">Buscar denúncias</label>

                <input
                    type="text"
                    id="campoBusca"
                    placeholder="Buscar problema..."
                    onkeyup="buscarProblemas()"
                >

                <label class="sr-only" for="filtroCategoria">Filtrar por categoria</label>

                <select
                    id="filtroCategoria"
                    onchange="atualizarSubcategoriasFiltro(); filtrarProblemas()"
                >

                    <option value="">
                        Todas as Categorias
                    </option>

                    <?php foreach ($categorias as $c): ?>

                        <option value="<?= $c['id'] ?>">
                            <?= htmlspecialchars($c['nome']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <label class="sr-only" for="filtroSubcategoria">Filtrar por subcategoria</label>

                <select
                    id="filtroSubcategoria"
                    onchange="filtrarProblemas()"
                >

                    <option value="">
                        Todas as Subcategorias
                    </option>

                    <?php foreach ($subcategorias as $s): ?>

                        <option value="<?= $s['id'] ?>" data-categoriaid="<?= $s['categoriaid'] ?>">
                            <?= htmlspecialchars($s['nome']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <label class="sr-only" for="raioBusca">Raio da busca em quilômetros</label>

                <input
                    type="number"
                    id="raioBusca"
                    min="0.1"
                    max="100"
                    step="0.1"
                    placeholder="Raio em km"
                    title="Filtrar ocorrências dentro deste raio"
                    oninput="filtrarProblemas()"
                >

                <button
                    type="button"
                    class="btn-blue"
                    onclick="usarMinhaLocalizacao()"
                >
                    Usar minha localização
                </button>

            </div>

            <div
                id="mapa-ocorrencias"
                class="map-container"
                aria-label="Mapa com as ocorrências cadastradas"
            ></div>

            <p id="mensagemLocalizacao" class="text-muted"></p>


            <!-- TABELA -->

            <div class="table-responsive">

                <table class="tabela-dados">

                    <thead>

                        <tr>

                            <th>
                                Código
                            </th>

                            <th>
                                Título
                            </th>

                            <th>
                                Categoria
                            </th>

                            <th>
                                Subcategoria
                            </th>

                            <th>
                                Prioridade
                            </th>

                            <th>
                                Usuário
                            </th>

                            <th>
                                Data
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Ações
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (isset($problemas) && !empty($problemas)): ?>

                            <?php foreach ($problemas as $p): ?>

                                <tr
                                    class="linha-problema"

                                    data-titulo="<?= htmlspecialchars(strtolower($p['titulo'])) ?>"

                                    data-categoria="<?= htmlspecialchars(strtolower($p['categoriaNome'])) ?>"

                                    data-subcategoria="<?= htmlspecialchars(strtolower($p['subcategoriaNome'])) ?>"

                                    data-usuario="<?= htmlspecialchars(strtolower($p['nomeUsuario'])) ?>"

                                    data-categoriaid="<?= $p['categoriaid'] ?>"

                                    data-subcategoriaid="<?= $p['subcategoriaid'] ?>"

                                    data-latitude="<?= htmlspecialchars((string) $p['latitude']) ?>"

                                    data-longitude="<?= htmlspecialchars((string) $p['longitude']) ?>"
                                >

                                    <!-- CÓDIGO DA DENÚNCIA -->

                                    <td>

                                        <strong>
                                            <?= htmlspecialchars($p['codigoDenuncia']) ?>
                                        </strong>

                                    </td>


                                    <!-- TÍTULO -->

                                    <td>

                                        <strong>
                                            <?= htmlspecialchars($p['titulo']) ?>
                                        </strong>

                                    </td>


                                    <!-- CATEGORIA -->

                                    <td>

                                        <span class="badge badge-gray">

                                            <?= htmlspecialchars($p['categoriaNome']) ?>

                                        </span>

                                    </td>


                                    <!-- SUBCATEGORIA -->

                                    <td>

                                        <?= htmlspecialchars($p['subcategoriaNome']) ?>

                                    </td>


                                    <!-- PRIORIDADE -->

                                    <td>
                                        <?php 
                                            $prioridade = $p['prioridade'] ?? 'media';
                                            $badgeClasses = [
                                                'baixa' => 'badge-gray',
                                                'media' => 'badge-blue',
                                                'alta' => 'badge-orange',
                                                'critica' => 'badge-red'
                                            ];
                                            $prioridadeLabels = [
                                                'baixa' => 'Baixa',
                                                'media' => 'Média',
                                                'alta' => 'Alta',
                                                'critica' => 'Crítica'
                                            ];
                                            $badgeClass = $badgeClasses[$prioridade] ?? 'badge-gray';
                                            $label = $prioridadeLabels[$prioridade] ?? ucfirst($prioridade);
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= htmlspecialchars($label) ?>
                                        </span>
                                    </td>


                                    <!-- USUÁRIO -->

                                    <td>

                                        <?= htmlspecialchars($p['nomeUsuario']) ?>

                                    </td>


                                    <!-- DATA -->

                                    <td>

                                        <?= date(
                                            'd/m/Y H:i',
                                            strtotime($p['dataproblema'])
                                        ) ?>

                                    </td>

                                    <!-- STATUS E AÇÕES -->

                                    <td>
                                        <?php $status = $p['status'] ?? 'aberta'; ?>
                                        <span class="badge badge-gray">
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $status))) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php
                                            $ehDono = (int) ($p['usuarioid'] ?? 0) === (int) $_SESSION['id'];
                                            $ehAdministrador = (int) ($_SESSION['nivelAcesso'] ?? 1) === 0;
                                        ?>

                                        <?php if ($ehDono && $status === 'aberta'): ?>
                                            <form method="POST" action="../controller/routes.php?acao=cancelarProblema" onsubmit="return confirm('Deseja cancelar esta denúncia?');">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                                <button type="submit" class="btn-orange">Cancelar</button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($ehDono || $ehAdministrador): ?>
                                            <a
                                                class="btn-secondary"
                                                href="../controller/routes.php?acao=historicoProblema&id=<?= (int) $p['id'] ?>"
                                            >
                                                Histórico
                                            </a>
                                            <button
                                                type="button"
                                                class="btn-mensagens"
                                                onclick="abrirModalMensagens(<?= (int) $p['id'] ?>, '<?= htmlspecialchars($p['codigoDenuncia'], ENT_QUOTES) ?>')"
                                            >
                                                💬 Mensagens
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($ehAdministrador): ?>
                                            <button type="button" class="btn-observacao" onclick="abrirModalObservacoes(<?= (int) $p['id'] ?>)">📝 Observações</button>

                                            <form method="POST" action="../controller/routes.php?acao=atualizarStatusProblema">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                                <select name="status" onchange="this.form.submit()" aria-label="Alterar status">
                                                    <?php foreach (['aberta' => 'Aberta', 'em_analise' => 'Em análise', 'resolvida' => 'Resolvida', 'cancelada' => 'Cancelada'] as $valor => $rotulo): ?>
                                                        <option value="<?= $valor ?>" <?= $status === $valor ? 'selected' : '' ?>><?= $rotulo ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </form>

                                            <form method="POST" action="../controller/routes.php?acao=atualizarPrioridadeProblema">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                                <select name="prioridade" onchange="this.form.submit()" aria-label="Alterar prioridade">
                                                    <?php 
                                                        $prioridadeAtual = $p['prioridade'] ?? 'media';
                                                        $opcoesPrioridade = [
                                                            'baixa' => 'Prioridade: Baixa',
                                                            'media' => 'Prioridade: Média',
                                                            'alta' => 'Prioridade: Alta',
                                                            'critica' => 'Prioridade: Crítica'
                                                        ];
                                                    ?>
                                                    <?php foreach ($opcoesPrioridade as $valor => $rotulo): ?>
                                                        <option value="<?= $valor ?>" <?= $prioridadeAtual === $valor ? 'selected' : '' ?>><?= $rotulo ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </form>

                                            <form method="POST" action="../controller/routes.php?acao=excluirProblema" onsubmit="return confirm('Tem certeza que deseja excluir definitivamente esta denúncia?');">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                                <button type="submit" class="btn-red">Excluir</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>


                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center text-muted"
                                    style="padding: 30px;"
                                >

                                    Nenhum problema encontrado.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>


            <!-- VOLTAR -->

            <a
                href="../view/dashboard.php"
                class="btn-secondary btn-block"
            >
                Voltar ao Dashboard
            </a>

        </div>

    </div>

</section>

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""
></script>


<script>

const ocorrencias = <?= json_encode(array_map(static function ($problema) {
    return [
        'id' => (int) $problema['id'],
        'codigo' => $problema['codigoDenuncia'],
        'titulo' => $problema['titulo'],
        'categoria' => $problema['categoriaNome'],
        'status' => $problema['status'] ?? 'aberta',
        'latitude' => (float) $problema['latitude'],
        'longitude' => (float) $problema['longitude']
    ];
}, $problemas), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

const mapOcorrencias = L.map('mapa-ocorrencias').setView([-23.3215, -46.7285], 13);
const marcadores = [];
let pontoBusca = null;

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(mapOcorrencias);

ocorrencias.forEach(function (ocorrencia) {
    const marcador = L.marker([ocorrencia.latitude, ocorrencia.longitude])
        .addTo(mapOcorrencias)
        .bindPopup(
            '<strong>' + escapeHtml(ocorrencia.codigo) + '</strong><br>' +
            escapeHtml(ocorrencia.titulo) + '<br>' +
            escapeHtml(ocorrencia.categoria) + '<br>' +
            escapeHtml(ocorrencia.status.replace('_', ' '))
        );

    marcadores.push({ marcador: marcador, ocorrencia: ocorrencia });
});

function escapeHtml(valor) {
    return String(valor).replace(/[&<>'"]/g, function (caractere) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#039;',
            '"': '&quot;'
        }[caractere];
    });
}

function distanciaEmKm(lat1, lng1, lat2, lng2) {
    const raioTerra = 6371;
    const diferencaLat = (lat2 - lat1) * Math.PI / 180;
    const diferencaLng = (lng2 - lng1) * Math.PI / 180;
    const valor = Math.sin(diferencaLat / 2) ** 2 +
        Math.cos(lat1 * Math.PI / 180) *
        Math.cos(lat2 * Math.PI / 180) *
        Math.sin(diferencaLng / 2) ** 2;

    return raioTerra * 2 * Math.atan2(Math.sqrt(valor), Math.sqrt(1 - valor));
}

function usarMinhaLocalizacao() {
    if (!navigator.geolocation) {
        document.getElementById('mensagemLocalizacao').textContent =
            'Este navegador não oferece geolocalização.';
        return;
    }

    navigator.geolocation.getCurrentPosition(function (posicao) {
        pontoBusca = {
            latitude: posicao.coords.latitude,
            longitude: posicao.coords.longitude
        };

        mapOcorrencias.setView(
            [pontoBusca.latitude, pontoBusca.longitude],
            14
        );

        document.getElementById('mensagemLocalizacao').textContent =
            'Localização obtida. Informe um raio para filtrar as ocorrências.';
        filtrarProblemas();
    }, function () {
        document.getElementById('mensagemLocalizacao').textContent =
            'Não foi possível obter sua localização.';
    });
}

function filtrarProblemas() {

    const busca =
        document
            .getElementById('campoBusca')
            .value
            .toLowerCase();

    const categoria =
        document
            .getElementById('filtroCategoria')
            .value;

    const subcategoria =
        document
            .getElementById('filtroSubcategoria')
            .value;

    const raio = Number(document.getElementById('raioBusca').value);

    const linhas =
        document.querySelectorAll('.linha-problema');

    linhas.forEach(function(linha) {

        const titulo =
            linha.dataset.titulo;

        const categoriaId =
            linha.dataset.categoriaid;

        const subcategoriaId =
            linha.dataset.subcategoriaid;

        const usuario =
            linha.dataset.usuario;

        const latitude = Number(linha.dataset.latitude);
        const longitude = Number(linha.dataset.longitude);

        const correspondeBusca =
            titulo.includes(busca) ||
            usuario.includes(busca);

        const correspondeCategoria =
            categoria === '' ||
            categoria === categoriaId;

        const correspondeSubcategoria =
            subcategoria === '' ||
            subcategoria === subcategoriaId;

        const correspondeLocalizacao =
            !pontoBusca || !raio ||
            distanciaEmKm(
                pontoBusca.latitude,
                pontoBusca.longitude,
                latitude,
                longitude
            ) <= raio;

        if (
            correspondeBusca &&
            correspondeCategoria &&
            correspondeSubcategoria &&
            correspondeLocalizacao
        ) {

            linha.style.display = '';

        } else {

            linha.style.display = 'none';

        }

    });

    marcadores.forEach(function (item) {
        const distanciaValida = !pontoBusca || !raio ||
            distanciaEmKm(
                pontoBusca.latitude,
                pontoBusca.longitude,
                item.ocorrencia.latitude,
                item.ocorrencia.longitude
            ) <= raio;

        if (distanciaValida) {
            item.marcador.addTo(mapOcorrencias);
        } else {
            mapOcorrencias.removeLayer(item.marcador);
        }
    });

}


function buscarProblemas() {

    filtrarProblemas();

}

// Esconde, no filtro, as subcategorias que não pertencem à categoria
// escolhida — evita o usuário escolher uma combinação categoria+subcategoria
// que nunca vai bater com nenhuma denúncia (mesma ideia já usada em
// problemaForm.php no cadastro).
const filtroCategoriaSelect = document.getElementById('filtroCategoria');
const filtroSubcategoriaSelect = document.getElementById('filtroSubcategoria');

const opcoesSubcategoriaFiltro = Array.from(filtroSubcategoriaSelect.options).map(function (opcao) {
    return {
        opcao: opcao,
        categoriaid: opcao.dataset.categoriaid || ''
    };
});

function atualizarSubcategoriasFiltro() {

    const categoriaid = filtroCategoriaSelect.value;

    opcoesSubcategoriaFiltro.forEach(function (item, indice) {
        const ehOpcaoPadrao = indice === 0;
        const pertence = item.categoriaid === categoriaid;

        item.opcao.hidden = !ehOpcaoPadrao && (categoriaid !== '' && !pertence);
        item.opcao.disabled = item.opcao.hidden;
    });

    if (filtroSubcategoriaSelect.options[filtroSubcategoriaSelect.selectedIndex]?.disabled) {
        filtroSubcategoriaSelect.value = '';
    }
}

</script>

<style>
    .modal-observacoes {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-observacoes.active {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal-content-observacoes {
        background-color: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 90%;
        max-width: 500px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .modal-header-obs {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #eee;
        padding-bottom: 15px;
    }

    .modal-header-obs h2 {
        margin: 0;
        color: #333;
        font-size: 18px;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #999;
    }

    .close-modal:hover {
        color: #333;
    }

    .observacao-existente {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        border-left: 4px solid #667eea;
    }

    .observacao-existente p {
        margin: 5px 0;
        font-size: 14px;
        line-height: 1.6;
        color: #555;
    }

    .observacao-data {
        font-size: 12px;
        color: #999;
        margin-top: 10px;
        font-style: italic;
    }

    .form-observacao {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .form-observacao textarea {
        width: 100%;
        min-height: 120px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        resize: vertical;
    }

    .form-observacao textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-observacao button {
        padding: 10px 20px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
    }

    .form-observacao button:hover {
        background: #5568d3;
    }

    .btn-observacao {
        padding: 8px 12px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
    }

    .btn-observacao:hover {
        background: #5568d3;
    }

    .btn-mensagens {
        padding: 8px 12px;
        background: #10b981;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
    }

    .btn-mensagens:hover {
        background: #059669;
    }

    .lista-mensagens {
        max-height: 320px;
        overflow-y: auto;
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding-right: 4px;
    }

    .mensagem-item {
        padding: 10px 14px;
        border-radius: 10px;
        max-width: 85%;
        font-size: 14px;
        line-height: 1.5;
        word-wrap: break-word;
    }

    .mensagem-item .mensagem-autor {
        font-weight: 700;
        font-size: 12px;
        margin-bottom: 4px;
        display: block;
    }

    .mensagem-item .mensagem-data {
        font-size: 11px;
        color: #999;
        margin-top: 6px;
        display: block;
    }

    .mensagem-gestor {
        background: #eef2ff;
        align-self: flex-start;
        border-left: 3px solid #667eea;
    }

    .mensagem-cidadao {
        background: #f0fdf4;
        align-self: flex-start;
        border-left: 3px solid #10b981;
    }

    .mensagem-propria {
        align-self: flex-end;
        background: #667eea;
        color: white;
    }

    .mensagem-propria .mensagem-data {
        color: rgba(255, 255, 255, 0.75);
    }

    @media (max-width: 480px) {
        .modal-content-observacoes {
            width: calc(100% - 24px);
            padding: 20px;
            max-height: 88vh;
        }

        .modal-header-obs h2 {
            font-size: 16px;
        }
    }
</style>

<!-- Modal de Observações -->
<div id="modalObservacoes" class="modal-observacoes">
    <div class="modal-content-observacoes">
        <div class="modal-header-obs">
            <h2>Observações Administrativas</h2>
            <button class="close-modal" onclick="fecharModalObservacoes()">&times;</button>
        </div>

        <div id="observacaoExistente"></div>

        <form id="formAdicionarObservacao" class="form-observacao" method="POST" action="../controller/routes.php?acao=adicionarObservacao">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" id="problemaIdObservacao" name="id">
            <label for="observacoesText" style="font-weight: 600; color: #333;">Adicione uma observação:</label>
            <textarea 
                id="observacoesText" 
                name="observacoes" 
                placeholder="Digite aqui as observações sobre esta denúncia..."
                required
            ></textarea>
            <button type="submit">Salvar Observação</button>
        </form>
    </div>
</div>

<script>
    function abrirModalObservacoes(problemaId) {
        document.getElementById('problemaIdObservacao').value = problemaId;
        
        // Buscar observações existentes
        fetch('../api/getObservacao.php?id=' + problemaId)
            .then(response => response.json())
            .then(data => {
                if (data && data.observacoes) {
                    const dataFormatada = new Date(data.data_observacao).toLocaleString('pt-BR');
                    document.getElementById('observacaoExistente').innerHTML = `
                        <div class="observacao-existente">
                            <p><strong>Última observação:</strong></p>
                            <p>${data.observacoes}</p>
                            <div class="observacao-data">Atualizado em: ${dataFormatada}</div>
                        </div>
                    `;
                } else {
                    document.getElementById('observacaoExistente').innerHTML = '';
                }
            })
            .catch(() => {
                document.getElementById('observacaoExistente').innerHTML = '';
            });

        document.getElementById('modalObservacoes').classList.add('active');
    }

    function fecharModalObservacoes() {
        document.getElementById('modalObservacoes').classList.remove('active');
        document.getElementById('formAdicionarObservacao').reset();
    }

    // Fechar modal ao clicar fora
    document.getElementById('modalObservacoes').addEventListener('click', function(event) {
        if (event.target === this) {
            fecharModalObservacoes();
        }
    });
</script>

<!-- Modal de Mensagens (RF-44: canal cidadão-gestor) -->
<div id="modalMensagens" class="modal-observacoes">
    <div class="modal-content-observacoes">
        <div class="modal-header-obs">
            <h2 id="tituloModalMensagens">Mensagens</h2>
            <button class="close-modal" onclick="fecharModalMensagens()">&times;</button>
        </div>

        <div id="listaMensagens" class="lista-mensagens">
            <p class="text-muted">Carregando mensagens...</p>
        </div>

        <form id="formEnviarMensagem" class="form-observacao" onsubmit="return enviarMensagem(event)">
            <label for="mensagemTexto" style="font-weight: 600; color: #333;">Escreva uma mensagem:</label>
            <textarea
                id="mensagemTexto"
                maxlength="2000"
                placeholder="Digite sua mensagem para o gestor/cidadão..."
                required
            ></textarea>
            <button type="submit">Enviar mensagem</button>
        </form>
    </div>
</div>

<script>
    const usuarioLogadoId = <?= $usuarioLogadoId ?>;
    let problemaIdMensagemAtual = null;

    function abrirModalMensagens(problemaId, codigoDenuncia) {
        problemaIdMensagemAtual = problemaId;

        document.getElementById('tituloModalMensagens').textContent = 'Mensagens — ' + codigoDenuncia;
        document.getElementById('listaMensagens').innerHTML = '<p class="text-muted">Carregando mensagens...</p>';
        document.getElementById('modalMensagens').classList.add('active');

        carregarMensagens();
    }

    function carregarMensagens() {
        if (!problemaIdMensagemAtual) {
            return;
        }

        fetch('../api/listarMensagens.php?problemaid=' + problemaIdMensagemAtual)
            .then(function (resposta) { return resposta.json(); })
            .then(function (dados) {
                if (Array.isArray(dados)) {
                    renderizarMensagens(dados);
                } else {
                    document.getElementById('listaMensagens').innerHTML =
                        '<p class="text-muted">' + escapeHtml(dados.error || 'Não foi possível carregar as mensagens.') + '</p>';
                }
            })
            .catch(function () {
                document.getElementById('listaMensagens').innerHTML =
                    '<p class="text-muted">Não foi possível carregar as mensagens.</p>';
            });
    }

    function renderizarMensagens(mensagens) {
        const container = document.getElementById('listaMensagens');

        if (!mensagens || mensagens.length === 0) {
            container.innerHTML = '<p class="text-muted">Nenhuma mensagem ainda. Comece a conversa!</p>';
            return;
        }

        container.innerHTML = mensagens.map(function (m) {
            const propria = Number(m.usuarioid) === usuarioLogadoId;
            const ehGestor = Number(m.nivelAcesso) === 0;
            const classe = propria ? 'mensagem-propria' : (ehGestor ? 'mensagem-gestor' : 'mensagem-cidadao');
            const rotulo = ehGestor ? (m.nomeUsuario + ' (Gestor)') : m.nomeUsuario;
            const dataFormatada = new Date(m.dataenvio.replace(' ', 'T')).toLocaleString('pt-BR');

            return '<div class="mensagem-item ' + classe + '">' +
                '<span class="mensagem-autor">' + escapeHtml(rotulo) + '</span>' +
                escapeHtml(m.mensagem).replace(/\n/g, '<br>') +
                '<span class="mensagem-data">' + dataFormatada + '</span>' +
            '</div>';
        }).join('');

        container.scrollTop = container.scrollHeight;
    }

    function enviarMensagem(evento) {
        evento.preventDefault();

        const campoTexto = document.getElementById('mensagemTexto');
        const texto = campoTexto.value.trim();

        if (!texto || !problemaIdMensagemAtual) {
            return false;
        }

        const dados = new FormData();
        dados.append('problemaid', problemaIdMensagemAtual);
        dados.append('mensagem', texto);

        fetch('../api/enviarMensagem.php', {
            method: 'POST',
            body: dados
        })
            .then(function (resposta) { return resposta.json(); })
            .then(function (resultado) {
                if (resultado && resultado.ok) {
                    campoTexto.value = '';
                    carregarMensagens();
                } else {
                    alert(resultado.error || 'Não foi possível enviar a mensagem.');
                }
            })
            .catch(function () {
                alert('Não foi possível enviar a mensagem. Tente novamente.');
            });

        return false;
    }

    function fecharModalMensagens() {
        document.getElementById('modalMensagens').classList.remove('active');
        problemaIdMensagemAtual = null;
    }

    document.getElementById('modalMensagens').addEventListener('click', function (evento) {
        if (evento.target === this) {
            fecharModalMensagens();
        }
    });
</script>

<?php include 'footer.php'; ?>