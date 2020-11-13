<?php
$in = $_REQUEST;

include("./.dbconfig");

$mysqli = new mysqli($conf['host'], $conf['user'], $conf['passwd'], $conf['db']); //"localhost", "mail", "activate", "general");
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$db = "";

if (!$in['x']) {
    $in['x'] = "list_tables";
}
if ($in['db']) {
    $db = $in['db'];
} else {
    $db = 'general';
}

if ($in['x']) {
    if ($in['x'] == "list_tables") {
        $data = listTables($db, $mysqli);
    } else if ($in['x'] == "list_dbs") {
        $data = listDatabases($mysqli);
    }
}

header("Content-type: application/json");
print json_encode($data);

$mysqli->close();

function listDatabases($mysqli) {
    
    if ($result = $mysqli->query("show databases;")) {
        while ($row = $result->fetch_row()) {
            $dbs[] = $row[0];
        }
        /* free result set */
        $result->close();
    }
    return $dbs;

}

function listTables($db, $mysqli) {
    $data = new stdClass();
    $data->db = $db;
    $data->tables = array();

    $tables = getTables($db, $mysqli);

    $cnt = count($tables);
    for ($i=0; $i<$cnt; $i++) {
        $fields = getFields($db, $tables[$i], $mysqli);
        $data->tables[$tables[$i]] = $fields;
    }
    return $data;
}

function getTables($db="general", $mysqli) {
    $mysqli->select_db($db);

    /* Select queries return a resultset */
    if ($result = $mysqli->query("show tables;")) {
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }
        /* free result set */
        $result->close();
    }
    return $tables;
}

function getFields($db="general", $table="quotes", $mysqli) {
    $mysqli->select_db($db);
    
    $fields = array();
    /* Select queries return a resultset */
    if ($result = $mysqli->query("DESC $table;")) {
        while ($row = $result->fetch_assoc()) {
            $fields[] = $row;
        }
        /* free result set */
        $result->close();
    }
    return $fields;
}
?>
