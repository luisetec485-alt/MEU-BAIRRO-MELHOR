<?php 
session_start();

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}
?>

<?php include 'header.php'; ?>

<section class="screen">
    <div class="dashboard-container max-width-large">

        <div class="welcome-box">

            <h2>Problemas Registrados</h2>

            <p>
                Acompanhe e pesquise os problemas relatados na comunidade.
            </p>

            <div class="filtros-container">

                <input
                    type="text"
                    id="campoBusca"
                    placeholder="Buscar problema..."
                    onkeyup="buscarProblemas()"
                >

                <select
                    id="filtroCategoria"
                    onchange="filtrarCategoria()"
                >
                    <option value="">
                        Todas as Categorias
                    </option>

                    <?php foreach($categorias as $c): ?>

                        <option value="<?= $c['id'] ?>">
                            <?= htmlspecialchars($c['nome']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <select
                    id="filtroSubcategoria"
                    onchange="filtrarSubcategoria()"
                >
                    <option value="">
                        Todas as Subcategorias
                    </option>

                    <?php foreach($subcategorias as $s): ?>

                        <option value="<?= $s['id'] ?>">
                            <?= htmlspecialchars($s['nome']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="table-responsive">

                <table class="tabela-dados">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Categoria</th>
                            <th>Subcategoria</th>
                            <th>Usuário</th>
                            <th>Data</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if(isset($problemas) && !empty($problemas)): ?>

                            <?php foreach($problemas as $p): ?>

                                <tr
                                    class="linha-problema"

                                    data-titulo="<?= strtolower($p['titulo']) ?>"

                                    data-categoria="<?= htmlspecialchars($p['categoriaNome']) ?>"

                                    data-subcategoria="<?= htmlspecialchars($p['subcategoriaNome']) ?>"

                                    data-usuario="<?= htmlspecialchars($p['nomeUsuario']) ?>"

                                    data-categoriaid="<?= $p['categoriaid'] ?>"

                                    data-subcategoriaid="<?= $p['subcategoriaid'] ?>"
                                >

                                    <td>#<?= $p['id'] ?></td>

                                    <td>
                                        <strong>
                                            <?= htmlspecialchars($p['titulo']) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <span class="badge badge-gray">
                                            <?= htmlspecialchars($p['categoriaNome']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($p['subcategoriaNome']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($p['nomeUsuario']) ?>
                                    </td>

                                    <td>
                                        <?= date('d/m/Y H:i', strtotime($p['dataproblema'])) ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td
                                    colspan="6"
                                    class="text-center text-muted"
                                    style="padding:30px;"
                                >
                                    Nenhum problema encontrado.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

            <a
                href="../view/dashboard.php"
                class="btn-secondary btn-block"
            >
                Voltar ao Dashboard
            </a>

        </div>

    </div>
</section>

<?php include 'footer.php'; ?>