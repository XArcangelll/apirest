<?php

require_once 'Conexion/conexion.php';

class Token extends Conexion{

    private $db;

    function __construct()
    {
        $this->db = new Conexion();
    }

    public function actualizarToken($fecha){
        $date = date("Y-m-d H:i");
        $stm = $this->db->connect();
        $query = $stm->prepare("UPDATE usuarios_token SET Estado = 'Inactivo' WHERE Fecha < '$fecha' and Estado = 'Activo'"); 
        $query->execute();
        if($query->rowCount()) return  1;
        return 0;
    }
}