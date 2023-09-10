<?php

require_once 'Clases/Conexion/conexion.php';

$db = new Conexion();

$query = $db->connect()->prepare("SELECT * FROM usuarios");

$query->execute();

$items = [];

while($row = $query->fetch(PDO::FETCH_ASSOC)){

    $item = [
        'UsuarioId'        => $row['UsuarioId'],
        'Usuario'    => $row['Usuario'],
        'Password'    => $row['Password'],
        'Estado' => $row['Estado'],
    ];

    array_push($items, $item);
}

header('Content-Type: application/json');
echo json_encode($items,true);