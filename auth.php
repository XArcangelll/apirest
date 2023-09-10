<?php

require_once 'Clases/auth.class.php';
require_once 'Clases/respuestas.class.php';

$_auth = new Auth();
$_respuestas = new Respuestas();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    header('Content-Type: application/json');
    $postBody = file_get_contents("php://input");
     $datosArray = $_auth->login($postBody);
     if(isset($datosArray["result"]["error_id"])){
        $responsecode = $datosArray["result"]["error_id"];
        http_response_code($responsecode);
     }else{
        http_response_code(200);
     }

     
     echo json_encode($datosArray);

}else{
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);

}