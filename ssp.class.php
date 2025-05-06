<?php

class SSP {
    static function data_output($columns, $data) {
        $out = array();

        for ($i = 0, $ien = count($data); $i < $ien; $i++) {
            $row = array();

            for ($j = 0, $jen = count($columns); $j < $jen; $j++) {
                $column = $columns[$j];

                $column_name = $column['db'];

                if (isset($column['formatter'])) {
                    $row[$j] = $column['formatter']($data[$i][$column_name], $data[$i]);
                } else {
                    $row[$j] = $data[$i][$column_name];
                }
            }

            $out[] = $row;
        }

        return $out;
    }

    static function limit($request) {
        $limit = '';

        if (isset($request['start']) && $request['length'] != -1) {
            $limit = "LIMIT " . intval($request['start']) . ", " . intval($request['length']);
        }

        return $limit;
    }

    static function order($request, $columns) {
        $order = '';

        if (isset($request['order']) && count($request['order'])) {
            $orderBy = array();

            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];

                if ($requestColumn['orderable'] == 'true') {
                    $dir = $request['order'][$i]['dir'] === 'asc' ? 'ASC' : 'DESC';

                    $orderBy[] = "`" . $columns[$columnIdx]['db'] . "` " . $dir;
                }
            }

            $order = 'ORDER BY ' . implode(', ', $orderBy);
        }

        return $order;
    }

    static function filter($request, $columns, &$bindings) {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = self::pluck($columns, 'dt');

        if (isset($request['search']) && $request['search']['value'] != '') {
            $str = $request['search']['value'];

            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ($requestColumn['searchable'] == 'true') {
                    $binding = self::bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
                    $globalSearch[] = "`" . $column['db'] . "` LIKE " . $binding;
                }
            }
        }

        for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
            $requestColumn = $request['columns'][$i];
            $columnIdx = array_search($requestColumn['data'], $dtColumns);
            $column = $columns[$columnIdx];

            $str = $requestColumn['search']['value'];

            if ($requestColumn['searchable'] == 'true' &&
                $str != ''
            ) {
                $binding = self::bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
                $columnSearch[] = "`" . $column['db'] . "` LIKE " . $binding;
            }
        }

        $where = '';

        if (count($globalSearch)) {
            $where = '(' . implode(' OR ', $globalSearch) . ')';
        }

        if (count($columnSearch)) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where . ' AND ' . implode(' AND ', $columnSearch);
        }

        if ($where !== '') {
            $where = 'WHERE ' . $where;
        }

        return $where;
    }

    static function simple($request, $sql_details, $table, $primaryKey, $columns) {
        $bindings = array();
        $db = self::sql_connect($sql_details);

        $limit = self::limit($request);
        $order = self::order($request, $columns);
        $where = self::filter($request, $columns, $bindings);

        $col = self::pluck($columns, 'db');
        $sql = "SELECT `" . implode("`, `", $col) . "` FROM `$table` $where $order $limit";

        $data = self::sql_exec($db, $bindings, $sql);

        $resFilterLength = self::sql_exec($db, $bindings, "SELECT COUNT(`{$primaryKey}`) FROM `$table` $where");
        $recordsFiltered = $resFilterLength[0][0];

        $resTotalLength = self::sql_exec($db, "SELECT COUNT(`{$primaryKey}`) FROM `$table`");
        $recordsTotal = $resTotalLength[0][0];

        return array(
            "draw"            => intval($request['draw']),
            "recordsTotal"    => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data"            => self::data_output($columns, $data)
        );
    }

    static function sql_connect($sql_details) {
        try {
            $dsn = "mysql:host={$sql_details['host']};port={$sql_details['port']};dbname={$sql_details['db']}";
            $pdo = new PDO($dsn, $sql_details['user'], $sql_details['pass'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch (PDOException $e) {
            self::fatal("Erro na conexão com o banco de dados: " . $e->getMessage());
        }

        return $pdo;
    }

    static function sql_exec($db, $bindings, $sql = null) {
        if ($sql === null) {
            $sql = $bindings;
        }

        $stmt = $db->prepare($sql);

        if (is_array($bindings)) {
            for ($i = 0, $ien = count($bindings); $i < $ien; $i++) {
                $binding = $bindings[$i];
                $stmt->bindValue($binding['key'], $binding['val'], $binding['type']);
            }
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    static function fatal($msg) {
        echo json_encode(array(
            "error" => $msg
        ));
        exit(0);
    }

    static function bind(&$a, $val, $type) {
        $key = ':binding_' . count($a);

        $a[] = array(
            'key' => $key,
            'val' => $val,
            'type' => $type
        );

        return $key;
    }

    static function pluck($a, $prop) {
        $out = array();

        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $out[] = $a[$i][$prop];
        }

        return $out;
    }
}
