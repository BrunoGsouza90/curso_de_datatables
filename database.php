<?php

    $servername = "mysql";
    $username = "root";
    $password = "root";

    try {

        $conn = new PDO("mysql:host=$servername;port=3306;dbname=datatables", $username, $password);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    } catch(PDOException $e) {

        echo "Falha na conexão com o Banco de Dados: " . $e->getMessage();

    }

?>