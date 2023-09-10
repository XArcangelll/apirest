<?php

require_once 'Clases/respuestas.class.php';
require_once 'Clases/pacientes.class.php';

$_respuestas = new Respuestas();
$_pacientes = new Pacientes();

if($_SERVER["REQUEST_METHOD"] == "GET"){
    header('Content-Type: application/json');
            if(isset($_GET["page"])){
                $pagina = $_GET["page"];
                $listaPacientes =  $_pacientes->listaPacientesPagina($pagina);
                http_response_code(200);
                echo json_encode($listaPacientes);
            }else if(isset($_GET["id"])){
                
                http_response_code(200);
                $pacienteId = $_GET["id"];
                $listaPaciente =  $_pacientes->obtenerPaciente($pacienteId);
                echo json_encode($listaPaciente,true);
            }else{
                http_response_code(200);
                $listaPacientes =  $_pacientes->listaPacientes();
                echo json_encode($listaPacientes,true);
            }
}else if($_SERVER["REQUEST_METHOD"] == "POST"){
    $postBody = file_get_contents("php://input");
    $datosArray = $_pacientes->post($postBody);
    $responseCode = $datosArray["result"]["error_id"] ?? 200;
    http_response_code($responseCode);
    echo json_encode($datosArray);
    

}else if($_SERVER["REQUEST_METHOD"] == "PUT"){
    header('Content-Type: application/json');
    $postBody = file_get_contents("php://input");
    $datosArray = $_pacientes->put($postBody);
    $responseCode = $datosArray["result"]["error_id"] ?? 200;
    http_response_code($responseCode);
    echo json_encode($datosArray);


}else if($_SERVER["REQUEST_METHOD"] == "DELETE"){
    header('Content-Type: application/json');
    $headers = getallheaders();
    if(isset($headers["token"]) && isset($headers["pacienteid"])){
        $send = [
            "token" => $headers["token"],
            "pacienteid" => $headers["pacienteid"]
        ];
        $postBody = json_encode($send);
    }else{
        $postBody = file_get_contents("php://input");
    }
    $datosArray = $_pacientes->delete($postBody);
    $responseCode = $datosArray["result"]["error_id"] ?? 200;
    http_response_code($responseCode);
    echo json_encode($datosArray);
}else{
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}