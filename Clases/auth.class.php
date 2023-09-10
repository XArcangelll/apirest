<?php

require_once 'Conexion/conexion.php';
require_once 'respuestas.class.php';

class Auth extends Conexion{

    public function login($json){
        $_respuestas = new Respuestas();

        $datos = json_decode($json,true);
        if(!isset($datos["usuario"]) || !isset($datos["password"])){
            return $_respuestas->error_400();
        }else{
            
            $usuario = $datos["usuario"];
            $password = $datos["password"];
            $password = $this->encriptar($password);

            $datos = $this->obtenerDatosUsuario($usuario);
            if($datos){
             
                if($password == $datos["Password"]){
                            if($datos["Estado"] == "Activo"){
                                    $verificar = $this->insertarToken($datos["UsuarioId"]);
                                    if($verificar){
                                          $_respuestas->setResponse(array("token" => $verificar)) ;
                                        return $_respuestas->getResponse();
                                    }else{
                                        return $_respuestas->error_500("Error interno, no hemos podido guardar");
                                    }
                            }else{  
                                return $_respuestas->error_200("El usuario esta inactivo");
                            }
                }else{
                    return $_respuestas->error_200("El password es inválido");
                }

            }else{  
                return $_respuestas->error_200("El usuario $usuario no existe");
            }

        }

    }

    private function obtenerDatosUsuario($correo){
        $query = $this->connect()->prepare("SELECT UsuarioId, Password, Estado FROM usuarios  WHERE usuario = '$correo'");
        $query->execute();
        $dato = $query->fetch(PDO::FETCH_ASSOC);
        return $dato;
    }

    private function insertarToken($usuarioid){
        $val = true;
        $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
        $date = date("Y-m-d H:i");
        $estado = "activo";
        $query = $this->connect()->prepare("INSERT INTO usuarios_token (UsuarioId,Token,Estado,Fecha) VALUES($usuarioid,'$token','$estado','$date')"); 
        $query->execute();
        if($query->rowCount()){
            return $token;
        } else{
            return 0;
        }
    }


}