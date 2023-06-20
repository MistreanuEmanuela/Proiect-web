<?php

require_once('../Services/vendor/autoload.php');

include_once "../Services/PlantService.php";
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

$service = new PlantService();
file_put_contents('./log.txt', $requestMethod, FILE_APPEND);
switch ($uri[5]) {
    case "planta":
        $jwt = $auth->checkJWTExistance();
        $auth->validateJWT($jwt);
        if ($requestMethod === 'POST') {
            $name = $_POST['name'];
            $desc = $_POST['desc'];
            $file = $_FILES['image']['tmp_name'];
            $collectionId = $_POST['collection'];
            $color = $_POST['culoare'];
            $type = $_POST['type'];
            $zona = $_POST['zona'];
            $anotimp = $_POST['anotimp'];
            $service->createPlant($name, $desc, $file, $collectionId, $color, $type, $zona, $anotimp);
        } else {
            header("HTTP/1.1 405 Method Not Allowed");
            exit();
        }
        break;
    case 'plante':
        $jwt = $auth->checkJWTExistance();
        $auth->validateJWT($jwt);
        $requestType = $uri[6];
        switch ($requestMethod) {
            case 'GET':
                if ($requestType == 'list')
                    $service->getPlantsByCollectionId();
                else if ($requestType == 'image')
                    $service->getImageById();
                else
                    $service->notFoundResponse();
                break;
            default:
                $service->notFoundResponse();
                break;
        }
        break;
    case 'info':
        $jwt = $auth->checkJWTExistance();
        $auth->validateJWT($jwt);
        file_put_contents('./log.txt', $requestMethod, FILE_APPEND);
        switch ($requestMethod) {
            case 'GET':
                $requestType = $uri[6];
                if ($requestType == 'list')
                    $service->getPlantById();
                else if ($requestType == 'image')
                    $service->getImageById();
                else
                    $service->getViewCount();
                break;
            case 'DELETE':
                $requestType = $uri[6];
                if ($requestType == 'delete')
                    $service->deletePlantByPlantId();
                else
                    $service->notFoundResponse();
                break;
            case 'PUT':
                file_put_contents('./log.txt', "hi", FILE_APPEND);
                $service->IncViewCount();
                break;
            default:
                $service->notFoundResponse();
                break;
        }
        break;
    case 'colectii':
        $jwt = $auth->checkJWTExistance();
        $auth->validateJWT($jwt);
        switch ($requestMethod) {
            case 'GET':
                $service->getImageByCollectionId();
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
                $service->getMostViewed();
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
        switch ($requestMethod) {
            case 'GET':
                $service->getTop10ByViewsRSS();
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