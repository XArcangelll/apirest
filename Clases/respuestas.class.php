<?php

class Respuestas{

    private $response = [
        "status" => "ok",
        "result" => array()
    ];

    public function getResponse(){
        return $this->response;
    }

    public function setResponse(array $arreglo){
        $this->response["result"] = $arreglo;
    }

    public function error_405(){
        $this->response["status"] = "error";
        $this->response["result"] = array("error_id" => "405", "error_msg" => "Método no permitido") ;
        return $this->response;
    }

    public function error_200($valor = "Datos Incorrectos"){
        $this->response["status"] = "error";
        $this->response["result"] = array("error_id" => "200", "error_msg" => $valor) ;
        return $this->response;
    }

    public function error_400(){
        $this->response["status"] = "error";
        $this->response["result"] = array("error_id" => "400", "error_msg" => "Datos enviados incompletos o con formato incorrecto") ;
        return $this->response;
    }

    public function error_500($valor = "Error interno del servidor"){
        $this->response["status"] = "error";
        $this->response["result"] = array("error_id" => "200", "error_msg" => $valor) ;
        return $this->response;
    }

    public function error_401($valor = "No autorizado"){
        $this->response["status"] = "error";
        $this->response["result"] = array("error_id" => "401", "error_msg" => $valor) ;
        return $this->response;
    }



}