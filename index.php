<?php
require_once "config/defaults.php";
require_once "config/requires_templates/requires.product.php";
$URL = $_SERVER['REQUEST_URI'];
/*
$validator = Validator::getInstance();
$URL = $validator->Check('Path',$URL,['output'=>'string']);
if ($URL === false){
    echo json_encode(['status'=>'error', 'message'=>"Wrong URL"]);
    exit;
}
*/
$exp = explode('?',$URL);
$action = substr(array_shift($exp),1);
$params = $_GET+$_POST;
unset($params['index_php']);
unset($params[$action]);
$params = array_values($params);
header('Access-Control-Allow-Origin: *');
@$action(...$params);
?>
