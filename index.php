<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.bootstrap5.css">
    <title>DataTables</title>
</head>
<body>
    
    <h1 id="titulo">DataTables</h1>

    <h2>Listar Usuários</h2>

    <div style="max-width: 800px; margin: 0 auto;">

        <table id="listar_usuarios" class="table table-striped">

            <thead>

                <tr>

                    <th>#</th>
                    <th>Nome</th>
                    <th>Salário</th>
                    <th>Idade</th>

                </tr>

            </thead>

            <tfoot>

                <tr>

                    <th>#</th>
                    <th>Nome</th>
                    <th>Salário</th>
                    <th>Idade</th>

                </tr>

            </tfoot>

        </table>

    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap5.js"></script>

    <script src="index.js"></script>

</body>
</html>