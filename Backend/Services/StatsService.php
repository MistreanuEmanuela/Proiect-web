<?php
include_once ("Config.php");
require ('fpdf/fpdf.php');
class StatsService
{
private $CONFIG;
    private $Db;

    public function __construct()
    {
        $this->CONFIG = include "Config.php";
        $this->connectDB();
    }

    public function __destruct()
    {
        mysqli_close($this->Db);
    }

    public function connectDB()
    {
        try
        {
            $this->Db = new mysqli($this->CONFIG["servername"], $this->CONFIG["username"], $this->CONFIG["password"], $this->CONFIG["db"]);
            if ($this->Db->connect_error) {
                echo "Not connected to DB";
            } else {
            }
        }
        catch (mysqli_sql_exception $e)
        {
            trigger_error("Could not connect to database: " . $e->getMessage(), E_USER_ERROR);
        }
    }
    public function getStats(){
        try {
            $raspuns = array();
            $currentMonth = date('Y-m');

            $statement = $this->Db->prepare("SELECT * FROM stats WHERE month=?");
            $statement->bind_param("s", $currentMonth);
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $userMonth = $result['users'];
            $plantMonth = $result['plants'];
            $collectionMonth = $result['collections'];
            $pViewsMonth = $result['plantViews'];
            $cViewsMonth = $result['collectionViews'];

            $statement = $this->Db->prepare("SELECT SUM(users),SUM(plants),SUM(collections),SUM(plantViews),SUM(collectionViews) FROM stats");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $userTotal = $result['SUM(users)'];
            $plantTotal = $result['SUM(plants)'];
            $collectionTotal = $result['SUM(collections)'];
            $pViewsTotal = $result['SUM(plantViews)'];
            $cViewsTotal = $result['SUM(collectionViews)'];

            $statement = $this->Db->prepare("SELECT COUNT(*) FROM users");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $userCount = $result['COUNT(*)'];
            $statement = $this->Db->prepare("SELECT COUNT(*) FROM collections");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $collectionCount = $result['COUNT(*)'];
            $statement = $this->Db->prepare("SELECT COUNT(*) FROM plants");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $plantCount = $result['COUNT(*)'];

            $raspuns = array(
                "userCount" => $userCount,
                "plantCount" => $plantCount,
                "collectionCount" => $collectionCount,
                "userMonth" =>$userMonth,
                "plantMonth" =>$plantMonth,
                "collectionMonth" =>$collectionMonth,
                "pViewsMonth" => $pViewsMonth,
                "cViewsMonth" => $cViewsMonth,
                "userTotal" => $userTotal,
                "plantTotal" => $plantTotal,
                "collectionTotal" => $collectionTotal,
                "pViewsTotal" => $pViewsTotal,
                "cViewsTotal" => $cViewsTotal,
            );
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode($raspuns);
        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
        }
        header($response['status_code_header']);
        header($response['content_type_header']);
        echo $response['body'];
    }
    public function incUsers(){
        try {
            $this->checkMonth();
            $currentMonth = date('Y-m');
            $statement = $this->Db->prepare("UPDATE stats SET users=users+1 WHERE month=?");
            $statement->bind_param("s", $currentMonth);
            if(!$statement->execute()){
                header('Content-Type: application/json');
                http_response_code(500);
                $errorResponse = array(
                    'message' => 'Failed to increment user count'
                );
                echo json_encode($errorResponse);
            }

        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
            header($response['status_code_header']);
            header($response['content_type_header']);
            echo $response['body'];
        }
    }
    public function incPlants(){
        try {
            $this->checkMonth();
            $currentMonth = date('Y-m');
            $statement = $this->Db->prepare("UPDATE stats SET plants=plants+1 WHERE month=?");
            $statement->bind_param("s", $currentMonth);
            if(!$statement->execute()){
                header('Content-Type: application/json');
                http_response_code(500);
                $errorResponse = array(
                    'message' => 'Failed to increment user count'
                );
                echo json_encode($errorResponse);
            }

        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
            header($response['status_code_header']);
            header($response['content_type_header']);
            echo $response['body'];
        }
    }
    public function incCollections(){
        try {
            file_put_contents('./log.txt', "hi", FILE_APPEND);
            $this->checkMonth();
            $currentMonth = date('Y-m');
            $statement = $this->Db->prepare("UPDATE stats SET collections=collections+1 WHERE month=?");
            $statement->bind_param("s", $currentMonth);
            if(!$statement->execute()){
                header('Content-Type: application/json');
                http_response_code(500);
                $errorResponse = array(
                    'message' => 'Failed to increment user count'
                );
                echo json_encode($errorResponse);
            }

        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
            header($response['status_code_header']);
            header($response['content_type_header']);
            echo $response['body'];
        }
    }
    public function incPlantViews(){
        try {
            $this->checkMonth();
            $currentMonth = date('Y-m');
            $statement = $this->Db->prepare("UPDATE stats SET plantViews=plantViews+1 WHERE month=?");
            $statement->bind_param("s", $currentMonth);
            if(!$statement->execute()){
                header('Content-Type: application/json');
                http_response_code(500);
                $errorResponse = array(
                    'message' => 'Failed to increment user count'
                );
                echo json_encode($errorResponse);
            }

        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
            header($response['status_code_header']);
            header($response['content_type_header']);
            echo $response['body'];
        }
    }
    public function incCollectionViews(){
        try {
            $this->checkMonth();
            $currentMonth = date('Y-m');
            $statement = $this->Db->prepare("UPDATE stats SET collectionViews=collectionViews+1 WHERE month=?");
            $statement->bind_param("s", $currentMonth);
            if(!$statement->execute()){
                header('Content-Type: application/json');
                http_response_code(500);
                $errorResponse = array(
                    'message' => 'Failed to increment user count'
                );
                echo json_encode($errorResponse);
            }

        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
            header($response['status_code_header']);
            header($response['content_type_header']);
            echo $response['body'];
        }
    }
    public function checkMonth(){
        try {
            $currentMonth = date('Y-m');
            $statement = $this->Db->prepare("SELECT * FROM stats WHERE month=?");
            $statement->bind_param("s", $currentMonth);
            $statement->execute();
            $result = $statement->get_result();
            if($result->num_rows == 0){
                $statement = $this->Db->prepare("INSERT INTO stats VALUES (?,0,0,0)");
                $statement->bind_param("s", $currentMonth);
                if(!$statement->execute()){
                    header('Content-Type: application/json');
                    http_response_code(500);
                    $errorResponse = array(
                        'message' => 'Failed to create a new plant'
                    );
                    echo json_encode($errorResponse);
                }
            }
        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
            
            header($response['status_code_header']);
            header($response['content_type_header']);
            echo $response['body'];
        }
    }
    public function getCSV(){
        try{
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="HEMA_Statistics.csv"');
            $csvFile = fopen('php://output','w');
            $columns = array('Time', 'Users', 'Plants', 'Collumns', 'Plant Views', 'Collection Views');
            fputcsv($csvFile,$columns);
            $raspuns = array();
            $currentMonth = date('Y-m');

            $statement = $this->Db->prepare("SELECT SUM(users),SUM(plants),SUM(collections),SUM(plantViews),SUM(collectionViews) FROM stats");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $row = array('All Time', $result['SUM(users)'], $result['SUM(plants)'], $result['SUM(collections)'], $result['SUM(plantViews)'], $result['SUM(collectionViews)']);
            fputcsv($csvFile,$row);

            $statement = $this->Db->prepare("SELECT COUNT(*) FROM users");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $userCount = $result['COUNT(*)'];
            $statement = $this->Db->prepare("SELECT COUNT(*) FROM collections");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $collectionCount = $result['COUNT(*)'];
            $statement = $this->Db->prepare("SELECT COUNT(*) FROM plants");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $plantCount = $result['COUNT(*)'];
            $statement = $this->Db->prepare("SELECT SUM(views) FROM collections");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $cViewsCount = $result['SUM(views)'];
            $statement = $this->Db->prepare("SELECT SUM(views) FROM plants");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $pViewsCount = $result['SUM(views)'];
            $row = array('Current', $userCount, $plantCount, $collectionCount, $pViewsCount, $cViewsCount);
            fputcsv($csvFile,$row);

            $statement = $this->Db->prepare("SELECT * FROM stats");
            $statement->execute();
            $result = $statement->get_result();
            while($row = $result->fetch_assoc())
                fputcsv($csvFile,$row);

        }catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
            header($response['status_code_header']);
            header($response['content_type_header']);
            echo $response['body'];
        }
    }
    public function getPDF(){
        try{
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 12);
            $data = array();
            $data[] = array('Time', 'Users', 'Plants', 'Collumns', 'Plant Views', 'Collection Views');

            $currentMonth = date('Y-m');
            $statement = $this->Db->prepare("SELECT SUM(users),SUM(plants),SUM(collections),SUM(plantViews),SUM(collectionViews) FROM stats");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $row = array('All Time', $result['SUM(users)'], $result['SUM(plants)'], $result['SUM(collections)'], $result['SUM(plantViews)'], $result['SUM(collectionViews)']);
            $data[] = $row;

            $statement = $this->Db->prepare("SELECT COUNT(*) FROM users");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $userCount = $result['COUNT(*)'];
            $statement = $this->Db->prepare("SELECT COUNT(*) FROM collections");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $collectionCount = $result['COUNT(*)'];
            $statement = $this->Db->prepare("SELECT COUNT(*) FROM plants");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $plantCount = $result['COUNT(*)'];
            $statement = $this->Db->prepare("SELECT SUM(views) FROM collections");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $cViewsCount = $result['SUM(views)'];
            $statement = $this->Db->prepare("SELECT SUM(views) FROM plants");
            $statement->execute();
            $result = $statement->get_result()->fetch_assoc();
            $pViewsCount = $result['SUM(views)'];
            $row = array('Current', $userCount, $plantCount, $collectionCount, $pViewsCount, $cViewsCount);
            $data[] = $row;

            $statement = $this->Db->prepare("SELECT * FROM stats");
            $statement->execute();
            $result = $statement->get_result();
            while($row = $result->fetch_assoc())
                $data[] = $row;
            
            foreach($data as $row){
                foreach($row as $cell){
                    $pdf->Cell(32,10,$cell,1,0,'C');
                }
                $pdf->Ln();
            }
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="HEMA_Statistics.pdf"');
            $pdf->OutPut('php://output','F');
        }catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
            header($response['status_code_header']);
            header($response['content_type_header']);
            echo $response['body'];
        }
    }
    public function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['content_type_header'] = 'Content-Type: application/json';
        $response['body'] = json_encode(array("Result" => "Not Found"));
        echo $response['body'];
    }
}