<?php
require_once __DIR__.'/../model/Seguranca.php';
Seguranca::exigirLogin();

$categorias = $categorias ?? [];
$subcategorias = $subcategorias ?? [];

?>

<?php
$pageTitle = 'Registrar Problema - Meu Bairro Melhor';
include __DIR__ . '/header.php';
?>

<section class="screen">

    <div class="dashboard-container max-width-medium">

        <div class="welcome-box">

            <h2>Registrar Novo Problema</h2>

            <p>
                Ajude a melhorar sua comunidade relatando um incidente urbano.
            </p>

            <form
                method="POST"
                action="../controller/routes.php?acao=salvarProblema"
                class="form-moderno"
                enctype="multipart/form-data"
            >
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Seguranca::token(), ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-group">

                    <label for="titulo">
                        Título do Problema:
                    </label>

                    <input
                        type="text"
                        id="titulo"
                        name="titulo"
                        required
                        maxlength="255"
                        placeholder="Ex: Buraco na via principal"
                    >

                </div>

                <div class="form-group">

                    <label for="descricao">
                        Descrição detalhada:
                    </label>

                    <textarea
                        id="descricao"
                        name="descricao"
                        rows="4"
                        required
                        placeholder="Descreva os detalhes do problema encontrado..."
                    ></textarea>

                </div>

                <div class="form-group">

                    <label for="bairro">
                        Bairro:
                    </label>

                    <input
                        type="text"
                        id="bairro"
                        name="bairro"
                        placeholder="Informe o bairro onde ocorre o problema"
                    >

                </div>

                <div class="form-group">

                    <label for="imagem">
                        Imagem ou vídeo do problema:
                    </label>

                    <input
                        type="file"
                        id="imagem"
                        name="imagem"
                        accept="
                            image/png,
                            image/jpeg,
                            image/jpg,
                            video/mp4,
                            video/webm,
                            video/quicktime,
                            video/avi
                        "
                    >

                    <small class="text-muted">
                        Máximo: 10MB para imagem e 50MB para vídeo.
                    </small>

                </div>

                <div class="form-group">

                    <label for="categoriaid">
                        Categoria:
                    </label>

                    <select
                        id="categoriaid"
                        name="categoriaid"
                        required
                    >

                        <option value="">
                            Selecione uma categoria
                        </option>

                        <?php foreach ($categorias as $categoria): ?>

                            <option
                                value="<?= $categoria['id'] ?>"
                            >
                                <?= htmlspecialchars($categoria['nome']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label for="subcategoriaid">
                        Subcategoria:
                    </label>

                    <select
                        id="subcategoriaid"
                        name="subcategoriaid"
                        required
                    >

                        <option value="">
                            Selecione uma subcategoria
                        </option>

                        <?php foreach ($subcategorias as $subcategoria): ?>

                            <option
                                value="<?= $subcategoria['id'] ?>"
                                data-categoriaid="<?= $subcategoria['categoriaid'] ?>"
                            >
                                <?= htmlspecialchars($subcategoria['nome']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label for="prioridade">
                        Prioridade/Urgência:
                    </label>

                    <select
                        id="prioridade"
                        name="prioridade"
                        required
                    >

                        <option value="">
                            Selecione uma prioridade
                        </option>

                        <option value="baixa">
                            Baixa - Problema menor, pode esperar
                        </option>

                        <option value="media">
                            Média - Problema moderado (padrão)
                        </option>

                        <option value="alta">
                            Alta - Problema importante, necessita atenção
                        </option>

                        <option value="critica">
                            Crítica - Problema urgente, risco de segurança
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label>
                        Localização do problema:
                    </label>

                    <div
                        id="mapa-problema"
                        class="map-container"
                        aria-label="Mapa para selecionar o local do problema"
                    ></div>

                    <small class="text-muted">
                        Clique no mapa para marcar o ponto exato do problema.
                    </small>

                    <div class="map-coordinates">

                        <div class="coord-box">

                            <label for="latitudeDisplay">
                                Latitude
                            </label>

                            <input
                                type="text"
                                id="latitudeDisplay"
                                readonly
                                placeholder="Ex: -23.3215"
                            >

                        </div>

                        <div class="coord-box">

                            <label for="longitudeDisplay">
                                Longitude
                            </label>

                            <input
                                type="text"
                                id="longitudeDisplay"
                                readonly
                                placeholder="Ex: -46.7285"
                            >

                        </div>

                    </div>

                    <input
                        type="hidden"
                        id="latitude"
                        name="latitude"
                        required
                    >

                    <input
                        type="hidden"
                        id="longitude"
                        name="longitude"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="btn-green btn-block"
                >
                    Registrar Problema
                </button>

                <a
                    href="../view/dashboard.php"
                    class="btn-secondary btn-block"
                >
                    Voltar ao Dashboard
                </a>

            </form>

        </div>

    </div>

</section>

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""
></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@7/turf.min.js"></script>

<script>

    const francoDaRocha = {
        lat: -23.3215,
        lng: -46.7285
    };

    const map = L.map('mapa-problema', {
        maxBoundsViscosity: 1.0,
        minZoom: 11,
        maxZoom: 18
    }).setView(
        [francoDaRocha.lat, francoDaRocha.lng],
        13
    );

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution: '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);

    const marker = L.marker(
        [
            francoDaRocha.lat,
            francoDaRocha.lng
        ],
        {
            draggable: true
        }
    ).addTo(map);

    const latitudeInput =
        document.getElementById('latitude');

    const longitudeInput =
        document.getElementById('longitude');

    const latitudeDisplay =
        document.getElementById('latitudeDisplay');

    const longitudeDisplay =
        document.getElementById('longitudeDisplay');

    let limiteFrancoDaRocha = null;

    fetch('../view/assets/geo/franco-da-rocha.geojson')
        .then(function (resposta) {
            return resposta.json();
        })
        .then(function (geojson) {
            limiteFrancoDaRocha = L.geoJSON(geojson, {
                filter: function (feature) {
                    return String(feature.properties.CD_MUN) === '3516408';
                },
                style: {
                    color: '#2563eb',
                    weight: 2,
                    fillColor: '#3b82f6',
                    fillOpacity: 0.12
                }
            }).addTo(map);

            if (limiteFrancoDaRocha.getBounds().isValid()) {
                map.setMaxBounds(limiteFrancoDaRocha.getBounds());
                map.fitBounds(limiteFrancoDaRocha.getBounds());
            }

            atualizarCoordenadas(
                francoDaRocha.lat,
                francoDaRocha.lng
            );
        })
        .catch(function () {
            alert('Não foi possível carregar o limite de Franco da Rocha.');
        });

    function coordenadaPermitida(lat, lng) {
        if (!limiteFrancoDaRocha) {
            return false;
        }

        const ponto = turf.point([lng, lat]);
        const geojson = limiteFrancoDaRocha.toGeoJSON();

        return geojson.features.some(function (feature) {
            return turf.booleanPointInPolygon(ponto, feature);
        });
    }

    function atualizarCoordenadas(lat, lng) {

        if (!coordenadaPermitida(lat, lng)) {
            alert('Selecione uma localização dentro de Franco da Rocha.');
            return false;
        }

        latitudeInput.value =
            Number(lat).toFixed(6);

        longitudeInput.value =
            Number(lng).toFixed(6);

        latitudeDisplay.value =
            Number(lat).toFixed(6);

        longitudeDisplay.value =
            Number(lng).toFixed(6);

        return true;
    }

    const categoriaInput =
        document.getElementById('categoriaid');

    const subcategoriaInput =
        document.getElementById('subcategoriaid');

    const subcategorias =
        Array.from(subcategoriaInput.options).map(function (opcao) {
            return {
                opcao: opcao,
                categoriaid: opcao.dataset.categoriaid || ''
            };
        });

    function atualizarSubcategorias() {
        const categoriaid = categoriaInput.value;
        let quantidadeVisivel = 0;

        subcategorias.forEach(function (item, indice) {
            const ehOpcaoPadrao = indice === 0;
            const pertence = item.categoriaid === categoriaid;

            item.opcao.hidden = !ehOpcaoPadrao && !pertence;
            item.opcao.disabled = !ehOpcaoPadrao && !pertence;

            if (pertence) {
                quantidadeVisivel++;
            }
        });

        if (categoriaid === '' || quantidadeVisivel === 0) {
            subcategoriaInput.value = '';
        } else if (subcategoriaInput.options[subcategoriaInput.selectedIndex]?.disabled) {
            subcategoriaInput.value = '';
        }
    }

    categoriaInput.addEventListener('change', atualizarSubcategorias);
    atualizarSubcategorias();

    map.on('click', function (evento) {

        const latlng = evento.latlng;

        if (atualizarCoordenadas(
            latlng.lat,
            latlng.lng
        )) {
            marker.setLatLng(latlng);
        }
    });

    marker.on('dragend', function () {

        const latlng = marker.getLatLng();

        if (!atualizarCoordenadas(
            latlng.lat,
            latlng.lng
        )) {
            marker.setLatLng([
                francoDaRocha.lat,
                francoDaRocha.lng
            ]);
        }
    });

</script>

<?php include __DIR__ . '/footer.php'; ?>
