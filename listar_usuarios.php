<?php

    include_once "./database.php";

    // Quantidade de usuários.
    $query = "SELECT COUNT(*) FROM usuarios";

    $resultado = $conn->prepare($query);

    $resultado->execute();

    $quantidade_usuarios = $resultado->fetch(PDO::FETCH_ASSOC);

    $quantidade_usuarios = $quantidade_usuarios['COUNT(*)'];

    // Listar usuários.
    $colunas = [

        "id",
        "nome",
        "salario",
        "idade"

    ];

    // Caso seja filtrado pelo campo de pesquisa.
    if(!empty($_REQUEST['search']['value'])) {

        $query = "SELECT id, nome, salario, idade
        FROM usuarios
        WHERE id LIKE :id
        OR
        nome LIKE :nome
        OR
        salario LIKE :salario
        OR
        idade LIKE :idade
        ORDER BY {$colunas[$_REQUEST['order'][0]['column']]} ASC
        LIMIT :inicio, :quantidade";

        $search = true;

    } else {

        $query = "SELECT id, nome, salario, idade
        FROM usuarios
        ORDER BY {$colunas[$_REQUEST['order'][0]['column']]} ASC
        LIMIT :inicio, :quantidade";

        $search = false;

    }   

    $resultado = $conn->prepare($query);

    if($search) {

        $operador = "%{$_REQUEST['search']['value']}%";

        $resultado->bindParam(":id", $operador, PDO::PARAM_STR);
        $resultado->bindParam(":nome", $operador, PDO::PARAM_STR);
        $resultado->bindParam(":salario", $operador, PDO::PARAM_STR);
        $resultado->bindParam(":idade", $operador, PDO::PARAM_STR);

    }

    $resultado->bindParam(":inicio", $_REQUEST['start'], PDO::PARAM_INT);
    $resultado->bindParam(":quantidade", $_REQUEST['length'], PDO::PARAM_INT);

    $resultado->execute();

    $usuarios = $resultado->fetchAll(PDO::FETCH_ASSOC);


    // Array de retorno.
    $array_ajax = [

        "draw" => intval($_REQUEST['draw']),
        "recordsTotal" => intval($quantidade_usuarios),
        "recordsFiltered" => intval($quantidade_usuarios),
        "data" => []

    ];

    foreach($usuarios as $usuario) {

        $array_ajax["data"][] = [

            $usuario['id'],
            $usuario['nome'],
            "R$ " . number_format($usuario['salario'], 2, ",", "."),
            $usuario['idade']

        ];

    }

    // Transformando o Array para JSON.
    header('Content-Type: application/json');

    echo json_encode($array_ajax);

?>