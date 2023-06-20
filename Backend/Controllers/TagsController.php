<?php

require_once ('../Services/vendor/autoload.php');

include_once "../Services/TagService.php";
include_once "../Services/TokenService.php";
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    die();
}
header('Access-Control-Allow-Origin: *');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$requestMethod = $_SERVER["REQUEST_METHOD"];

$auth = new TokenService();
$jwt = $auth->checkJWTExistance();
$auth->validateJWT($jwt);
$service = new tagService();
switch ($uri[5]) {
    case "tag":
        if ($requestMethod === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $tag = $data['tag']; 
            $collectionId = $data['collectionId']; 
            $service->createTag($tag, $collectionId);
        }
         else {
            header("HTTP/1.1 405 Method Not Allowed");
            exit();
        }
        break;
        default:
        header("HTTP/1.1 404 Not Found");
        exit();
    }