<?php

require_once 'Clases/Conexion/conexion.php';
require_once 'Clases/respuestas.class.php';

class Pacientes extends Conexion{

    private $table = 'pacientes';
    private $pacienteid = "";
    private $dni = "";
    private $nombre = "";
    private $direccion = "";
    private $codigopostal = "";
    private $genero = "";
    private $telefono = "";
    private $fechaNacimiento = "0000-00-00";
    private $correo = "";
    private $token = "";
    private $imagen = "";

    private $db;

    function __construct()
    {
        $this->db = new Conexion();
    }

    public function listaPacientesPagina($pagina = 1){



        $inicio = 0;
        $cantidad = 3;

        if($pagina > 1){
            $inicio = $cantidad * ($pagina -1);
        }

        $query = $this->db->connect()->prepare("SELECT PacienteId,Nombre,DNI,Telefono,Correo FROM " . $this->table . " limit $inicio,$cantidad"); 
        $query->execute();
        $dato = $query->fetchAll(PDO::FETCH_ASSOC);
        return $dato;
    }

    public function obtenerPaciente($id){
        $query = $this->db->connect()->prepare("SELECT * FROM pacientes WHERE PacienteId = $id"); 
        $query->execute();
        $dato = $query->fetch(PDO::FETCH_ASSOC);
        return $dato;
    }

    public function listaPacientes(){
        $query = $this->db->connect()->prepare("SELECT * FROM pacientes"); 
        $query->execute();
        $dato = $query->fetchAll(PDO::FETCH_ASSOC);
        return $dato;
    }

    private function procesarImagen($img){
        $direccion = dirname(__DIR__) . "\public\imagenes\\";
        $partes = explode(";base64,",$img);
        $extension = explode('/',mime_content_type($img))[1];
        $imagen_base64 = base64_decode($partes[1]);
        $file = $direccion . uniqid() . "." . $extension;
        //$nuevadireccion = str_replace('\\','/',$file); por si falla la direccion del guardado
        file_put_contents($file,$imagen_base64);
        return $file;
    }

    public function post($json){
        $_respuestas = new Respuestas();
        $datos = json_decode($json,true);

        
        if(!isset($datos["token"])) return $_respuestas->error_401();
        $this->token = $datos["token"];
        $arrayToken = $this->buscarToken($this->token);
        if(!$arrayToken) return $_respuestas->error_401("El token que envio es invalido o ha caducado");

        if(!isset($datos["nombre"]) || !isset($datos["dni"]) || !isset($datos["correo"])){
            return $_respuestas->error_400();
        }else{
            $this->nombre = $datos["nombre"];
            $this->dni = $datos["dni"];
            $this->correo = $datos["correo"];
            $this->telefono = $datos["telefono"] ?? "";
            $this->direccion = $datos["direccion"] ?? "";
            $this->codigopostal = $datos["codigopostal"] ?? "";
            $this->genero = $datos["genero"] ?? "";
            $this->fechaNacimiento = $datos["fechanacimiento"] ?? "0000-00-00";
            $this->imagen = $this->procesarImagen($datos["imagen"]) ?? "";
            
            $resp = $this->insertarPaciente();
           
            if($resp){
                $_respuestas->setResponse(array("pacienteId"=> $resp));
                return $_respuestas->getResponse();
            }else{
                return $_respuestas->error_500();
            }

    }
    }

    public function delete($json){
        $_respuestas = new Respuestas();
        $datos = json_decode($json,true);

        if(!isset($datos["token"])) return $_respuestas->error_401();
        $this->token = $datos["token"];
        $arrayToken = $this->buscarToken($this->token);
        if(!$arrayToken) return $_respuestas->error_401("El token que envio es invalido o ha caducado");

        if(!isset($datos["pacienteid"]) ){
            return $_respuestas->error_400();
        }else{
            $this->pacienteid = $datos["pacienteid"];
            $resp = $this->eliminaPaciente();
            if($resp){
                $_respuestas->setResponse(array("pacienteId"=> $this->pacienteid));
                return $_respuestas->getResponse();
            }else{
                return $_respuestas->error_200("Datos Incorrectos o no se eliminaron");
            }

    }
    }

    public function put($json){
        $_respuestas = new Respuestas();
        $datos = json_decode($json,true);

        if(!isset($datos["token"])) return $_respuestas->error_401();
        $this->token = $datos["token"];
        $arrayToken = $this->buscarToken($this->token);
        if(!$arrayToken) return $_respuestas->error_401("El token que envio es invalido o ha caducado");

        if(!isset($datos["pacienteid"])){
            return $_respuestas->error_400();
        }else{
           $datosActuales = $this->obtenerPaciente($datos["pacienteid"]);
           $this->pacienteid = $datos["pacienteid"];
           $this->nombre =  $datos["nombre"] ?? $datosActuales["Nombre"];
           $this->dni = $datos["dni"]  ?? $datosActuales["DNI"];
           $this->correo = $datos["correo"]  ?? $datosActuales["Correo"];
           $this->telefono = $datos["telefono"] ?? $datosActuales["Telefono"];
           $this->direccion = $datos["direccion"] ?? $datosActuales["Direccion"];
           $this->codigopostal = $datos["codigopostal"] ?? $datosActuales["CodigoPostal"];
           $this->genero = $datos["genero"] ?? $datosActuales["Genero"];
           $this->fechaNacimiento = $datos["fechanacimiento"] ?? $datosActuales["FechaNacimiento"];
           $this->imagen = $datos["imagen"] ?? $datosActuales["Imagen"];
           $resp = $this->actualizaPaciente();
           
           if($resp){
            $_respuestas->setResponse(array("pacienteId"=> $this->pacienteid));
            return $_respuestas->getResponse();
        }else{
            return $_respuestas->error_200("Datos Incorrectos o no se actualizaron");
        }

     }
    }



    private function insertarPaciente(){
      //  $query = "INSERT INTO " . $this->table . "(DNI,Nombre,Direccion,CodigoPostal,Telefono,Genero,FechaNacimiento,Correo) VALUES('$this->dni','$this->nombre','$this->direccion','$this->codigopostal','$this->telefono','$this->genero','$this->fechaNacimiento','$this->correo')";
       
        $stm = $this->db->connect();

      $query = $stm->prepare("INSERT INTO " . $this->table . "(DNI,Nombre,Direccion,CodigoPostal,Telefono,Genero,FechaNacimiento,Correo,Imagen) VALUES(:dni,:nombre,:direccion,:codigopostal,:telefono,:genero,:fechanacimiento,:correo,:imagen)");
        $query->execute([
            "dni" => $this->dni,
            "nombre" => $this->nombre,
            "direccion" => $this->direccion,
            "codigopostal" => $this->codigopostal,
            "telefono" => $this->telefono,
            "genero" => $this->genero,
            "fechanacimiento" => $this->fechaNacimiento,
            "correo" => $this->correo,
            "imagen" => $this->imagen
        ]);

        if($query->rowCount()) return  $stm->lastInsertId();
        return 0;
        


    }

    private function actualizaPaciente(){
        //  $query = "INSERT INTO " . $this->table . "(DNI,Nombre,Direccion,CodigoPostal,Telefono,Genero,FechaNacimiento,Correo) VALUES('$this->dni','$this->nombre','$this->direccion','$this->codigopostal','$this->telefono','$this->genero','$this->fechaNacimiento','$this->correo')";
        $query = $this->db->connect()->prepare("UPDATE pacientes SET DNI = :dni , Nombre = :nombre , Direccion = :direccion , CodigoPostal = :codigopostal ,Telefono = :telefono , Genero = :genero , FechaNacimiento = :fechanacimiento , Correo = :correo, Imagen = :imagen  WHERE PacienteId = :pacienteid");
          $query->execute([
            "dni" => $this->dni,
            "nombre" => $this->nombre,
            "direccion" => $this->direccion,
            "codigopostal" => $this->codigopostal,
            "telefono" => $this->telefono,
            "genero" => $this->genero,
            "fechanacimiento" => $this->fechaNacimiento,
            "correo" => $this->correo,
            "imagen" => $this->imagen,
            "pacienteid" => $this->pacienteid
          ]);
  
          if($query->rowCount()) return  true;
          return false;
      }

      private function eliminaPaciente(){
        //  $query = "INSERT INTO " . $this->table . "(DNI,Nombre,Direccion,CodigoPostal,Telefono,Genero,FechaNacimiento,Correo) VALUES('$this->dni','$this->nombre','$this->direccion','$this->codigopostal','$this->telefono','$this->genero','$this->fechaNacimiento','$this->correo')";
        $query = $this->db->connect()->prepare("DELETE FROM pacientes WHERE PacienteId = :pacienteid ");
          $query->execute([
            "pacienteid" => $this->pacienteid
          ]);
  
          if($query->rowCount()) return  true;
          return false;
      }

      private function buscarToken(){
        $query = $this->db->connect()->prepare("SELECT TokenId, UsuarioId, Estado FROM usuarios_token WHERE Token = :token AND Estado = 'Activo'"); 
        $query->execute(["token"=>$this->token]);
        $dato = $query->fetch(PDO::FETCH_ASSOC);
        return $dato;
      }

      private function actualizarToken($tokenid){
        $date = date("Y-m-d H:i");
        $stm = $this->db->connect();
        $query = $stm->prepare("UPDATE usuarios_token SET fecha = '$date' WHERE TokenId = $tokenid"); 
        $query->execute(["token"=>$this->token]);
        if($query->rowCount()) return  $stm->lastInsertId();
        return 0;
      }


}
