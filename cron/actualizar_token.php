<?php
require_once '../Clases/token.class.php';
$_token = new Token();
$fecha = date("Y-m-d H:i");
echo $_token->actualizarToken($fecha);