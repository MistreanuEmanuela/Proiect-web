<?php

require_once('../Services/vendor/autoload.php');

include_once "../Services/CollectionService.php";
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
$uri = explode('/', $uri);

$requestMethod = $_SERVER["REQUEST_METHOD"];

$auth = new TokenService();

$service = new CollectionService();

switch ($uri[5]) {
    case "addcolection":
        $jwt = $auth->checkJWTExistance();
        $auth->validateJWT($jwt);
        if ($requestMethod === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $name = $data['name'];
            $desc = $data['desc'];
            $userId = $data['userId'];
            $response = $service->createCollection($name, $desc, $userId);
            if(http_response_code()==200){
                $stats = new StatsService();
                $stats->incCollections();
            }
            echo $response;
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
            exit();
        }
        break;
    case "colectii":
        $jwt = $auth->checkJWTExistance();
        $auth->validateJWT($jwt);
        $requestType = $uri[6];
        switch ($requestMethod) {
            case 'GET':
                if ($requestType == 'list')
                    $service->getCollectionsByUserId();
                else if ($requestType == "all")
                    $service->getAllCollection();
                else
                    $service->notFoundResponse();
                break;
            default:
                $service->notFoundResponse();
                break;
        }
        break;
    case "filtru":
        $jwt = $auth->checkJWTExistance();
        $auth->validateJWT($jwt);
        $requestType = $uri[6];
        switch ($requestMethod) {
            case 'GET':
                if ($requestType == 'culoare')
                    $service->getCollectionsByCuloare();
                break;
            default:
                $service->notFoundResponse();
                break;
        }
        break;
    case 'view':
        $jwt = $auth->checkJWTExistance();
        $auth->validateJWT($jwt);
        switch ($requestMethod) {
            case 'GET':
                $service->getViewCount();
                break;
            case 'PUT':
                $service->incViewCount();
                break;
            default:
                $service->notFoundResponse();
                break;
        }
        break;
    case "info":
        $jwt = $auth->checkJWTExistance();
        $auth->validateJWT($jwt);
        $CollectionId = $_GET['collectionId'];
        switch ($requestMethod) {
            case 'GET':
                $service->getbyCollectionId($CollectionId);
                break;
            default:
                $service->notFoundResponse();
                break;
        }

        break;
    case 'delete':
        $jwt = $auth->checkJWTExistance();
        $auth->validateJWT($jwt);
        switch ($requestMethod) {
            case 'DELETE':
                $service->deleteCollectionById();
                break;
            default:
                $service->notFoundResponse();
                break;
        }
        break;
    case 'biggest':
        $jwt = $auth->checkJWTExistance();
        $auth->validateJWT($jwt);
        switch ($requestMethod) {
            case 'GET':
                $service->getBiggest3();
                break;
            default:
                $service->notFoundResponse();
                break;
        }
        break;
    case 'mostViews':
        $jwt = $auth->checkJWTExistance();
        $auth->validateJWT($jwt);
        switch ($requestMethod) {
            case 'GET':
                $service->getMostViewed3();
                break;
            default:
                $service->notFoundResponse();
                break;
        }
        break;
    case 'updateName':
        $jwt = $auth->checkJWTExistance();
        $auth->validateJWT($jwt);
        switch ($requestMethod) {
            case 'PUT':
                $service->updateName();
                break;
            default:
                $service->notFoundResponse();
                break;
        }
        break;
    case 'updateDesc':
        $jwt = $auth->checkJWTExistance();
        $auth->validateJWT($jwt);
        switch ($requestMethod) {
            case 'PUT':
                $service->updateDesc();
                break;
            default:
                $service->notFoundResponse();
                break;
        }
        break;
    case "RSS":
        $requestType = $uri[6];
        switch ($requestMethod) {
            case 'GET':
                if ($requestType == 'big')
                    $service->getTop10BySizeRSS();
                else if ($requestType == 'view')
                    $service->getTop10ByViewsRSS();
                else
                    $service->notFoundResponse();
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