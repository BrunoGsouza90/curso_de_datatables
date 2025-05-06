new DataTable('#listar_usuarios', {

    ajax: 'listar_usuarios.php',
    processing: true,
    serverSide: true,
    language: {

        url: "http://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"

    }

})