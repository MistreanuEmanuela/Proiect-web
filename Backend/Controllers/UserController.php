<?php

require_once('../Services/vendor/autoload.php');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        die();
    }
header('Access-Control-Allow-Origin: *');
include_once("../Services/TokenService.php");
include_once "../Services/UserService.php";
include_once("../Services/StatsService.php");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$requestMethod = $_SERVER["REQUEST_METHOD"];
$auth = new TokenService();
$service = new UserService();
switch ($uri[5]) {
        case 'login':
                switch ($requestMethod) {
                        case 'POST':
                                $response = $service->login();
                                if($response['status_code_header']=="HTTP/1.1 200 OK"){
                                        $token = new TokenService();
                                        $jwt = $token->generateJWT($response['body']);
                                        $response['body'] = json_encode(['token' => $jwt]);
                                }
                                header($response['status_code_header']);
                                header($response['content_type_header']);
                                if ($response['body']) {
                                        echo $response['body'];
                                }
                                break;
                        default:
                                echo ($requestMethod);
                                header("HTTP/1.1 404 Not Found");
                                exit();
                }
                break;
        case 'profile':
                $jwt = $auth->checkJWTExistance();
                $auth->validateJWT($jwt);
                switch ($requestMethod) {
                        case 'POST':
                                $service->getUserById();
                                break;
                        case 'PUT':
                                $service->updateUserPassword();
                                break;
                        case 'PATCH':
                                $service->updateUserProfile();
                                break;
                        default:
                                $service->notFoundResponse();
                                break;
                }
                break;
        case 'inregistrare':
                switch ($requestMethod) {
                        case 'POST':
                                $response = $service->createUserFromFormData();
                                if($response=="User created"){
                                        $stats = new StatsService();
                                        $stats->incUsers();
                                }
                                echo $response;
                                break;
                        default:
                                $service->notFoundResponse();
                                break;
                }
                break;
        case 'finduser':
                switch ($requestMethod) {
                        case 'POST':
                                $service->getUserByUsername();
                                break;
                        default:
                                $service->notFoundResponse();
                                break;
                }
                break;
        case 'mostCol':
                $jwt = $auth->checkJWTExistance();
                $auth->validateJWT($jwt);
                switch ($requestMethod) {
                        case 'GET':
                                $service->getTop3ByCollections();
                                break;
                        default:
                                $service->notFoundResponse();
                                break;
                }
                break;
        case 'mostPlants':
                $jwt = $auth->checkJWTExistance();
                $auth->validateJWT($jwt);
                switch ($requestMethod) {
                        case 'GET':
                                $service->getTop3ByPlants();
                                break;
                        default:
                                $service->notFoundResponse();
                                break;
                }
                break;
        case 'RSS':
                switch ($requestMethod) {
                        case 'GET':
                                $requestType = $uri[6];
                                switch ($requestType) {
                                        case 'col':
                                                $service->getTop10ByCollectionsRSS();
                                                break;
                                        case 'plants':
                                                $service->getTop10ByPlantsRSS();
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