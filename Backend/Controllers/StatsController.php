<?php

require_once ('../Services/vendor/autoload.php');

include_once "../Services/StatsService.php";
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
$service = new StatsService();

switch ($uri[5]) {
    case "list":
        switch ($requestMethod) {
            case 'GET':
                    $service->getStats();
            break;        
            default:
                $service->notFoundResponse();
                break;
        }
    break;  
    case "file":
        switch ($requestMethod) {
            case 'GET':
                    $requestType = $uri[6];
                    switch($requestType){
                        case 'CSV':
                            $service->getCSV();
                            break;
                        case 'PDF':
                            $service->getPDF();
                            break;
                        default:
                            $service->notFoundResponse();
                            break;
                    }
            break;        
            default:
                $service->notFoundResponse();
                break;
            }
    break;    
    default:
        header("HTTP/1.1 404 Not Found");
        exit();
}
