<?php

class Conexion{
    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    private $conexion;
    private $charset;

    public function __construct(){
        $listaDatos = $this->datoConexion();
        foreach($listaDatos as $key => $value){
            $this->server = $value["server"];
            $this->user = $value["user"];
            $this->password = $value["password"];
            $this->database = $value["database"];
            $this->port = $value["port"];
            $this->charset = $value["charset"];
        }

    }


    private function datoConexion(){
        $direccion = dirname(__FILE__);
        $jsondata = file_get_contents($direccion . "/" . "config.json");
        return json_decode($jsondata,true);
    }

    public function connect(){
    
        try{
            
           $this->conexion = "mysql:host=" . $this->server . ";dbname=" . $this->database . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($this->conexion, $this->user, $this->password, $options);
    
            return $pdo;

        }catch(PDOException $e){
            print_r('Error connection: ' . $e->getMessage());
        }   
    }

    protected function encriptar($string){
        return md5($string);
    }


}