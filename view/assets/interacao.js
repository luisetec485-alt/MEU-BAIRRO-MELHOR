let categoriaAtiva = '';
let subcategoriaAtiva = '';

function buscarProblemas() {

    const texto = document
        .getElementById('campoBusca')
        .value
        .toLowerCase();

    const linhas = document.querySelectorAll('.linha-problema');

    linhas.forEach(linha => {

        const titulo =
            linha.dataset.titulo.toLowerCase();

        const categoria =
            linha.dataset.categoria.toLowerCase();

        const subcategoria =
            linha.dataset.subcategoria.toLowerCase();

        const usuario =
            linha.dataset.usuario.toLowerCase();

        console.log("Busca:", texto);
        console.log("Título:", titulo);

        const bateBusca =
            titulo.includes(texto);

        

        const bateCategoria =
            categoriaAtiva === '' ||
            linha.dataset.categoriaid === categoriaAtiva;

        const bateSubcategoria =
            subcategoriaAtiva === '' ||
            linha.dataset.subcategoriaid === subcategoriaAtiva;

        console.log(
            titulo,
            texto,
            bateBusca,
            bateCategoria,
            bateSubcategoria
        );

        if (
            bateBusca &&
            bateCategoria &&
            bateSubcategoria
        ) {
            linha.style.display = '';
        } else {
            linha.style.display = 'none';
        }

    });
}

function filtrarCategoria() {

    categoriaAtiva =
        document.getElementById('filtroCategoria').value;

    buscarProblemas();
}

function filtrarSubcategoria() {

    subcategoriaAtiva =
        document.getElementById('filtroSubcategoria').value;

    buscarProblemas();
}