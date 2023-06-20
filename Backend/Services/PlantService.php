<?php
include_once("Config.php");
include_once("StatsService.php");
class PlantService
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
        try {
            $this->Db = new mysqli($this->CONFIG["servername"], $this->CONFIG["username"], $this->CONFIG["password"], $this->CONFIG["db"]);
            if ($this->Db->connect_error) {
                echo "Not connected to DB";
            } else {
                //    echo "Successfully connected to DB";
            }
        } catch (mysqli_sql_exception $e) {
            trigger_error("Could not connect to the database: " . $e->getMessage(), E_USER_ERROR);
        }
    }

    public function createPlant($name, $desc, $image, $collection, $color, $type, $zona, $anotimp)
    {
       
        $query = "INSERT INTO plants (name, description, photo, collectionId, color, type,zone, season) VALUES (?, ?, ?, ?, ?, ?, ?,? )";
        $statement = $this->Db->prepare($query);
        $timestamp = date('YmdHis');
        $imagePath = '../Storage/' . $collection . $timestamp . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        $statement->bind_param("sssissss", $name, $desc, $imagePath, $collection, $color, $type, $zona, $anotimp);
        $statement = $this->Db->prepare($query);

        $statement->bind_param("sssissss", $name, $desc, $imagePath, $collection, $color, $type, $zona, $anotimp);

        if ($statement->execute()) {
            $plantId = $this->Db->insert_id;

            $response = array(
                'plantId' => $plantId,
                'message' => 'New plant created successfully'
            );
            $stats = new StatsService();
            $stats->incPlants();

            $jsonResponse = json_encode($response);
            header('Content-Type: application/json');
            http_response_code(200);
            echo $jsonResponse;
        } else {
            header('Content-Type: application/json');
            http_response_code(500);
            $errorResponse = array(
                'message' => 'Failed to create a new plant'
            );
            echo json_encode($errorResponse);
        }
    }
    public function getPlantsByCollectionId()
    {
        $collectionId = $_GET['collectionId'];
        try {
            $statement = $this->Db->prepare("SELECT id,name FROM plants WHERE collectionId = ?");
            $statement->bind_param("i", $collectionId);
            $statement->execute();
            $result = $statement->get_result();
            $raspuns = array();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $collectionId = $row['id'];
                    $collectionName = $row['name'];
                    $responseRow = array(
                        "id" => $collectionId,
                        "name" => $collectionName,
                    );
                    $raspuns[] = $responseRow;
                }
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode($raspuns);

            } else {
                $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'User not found']);
            }
        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
        }
        header($response['status_code_header']);
        header($response['content_type_header']);
        echo $response['body'];
    }
    public function getImageByCollectionId()
    {
        $collectionId = $_GET['collectionId'];
        try {
            $statement = $this->Db->prepare("SELECT photo FROM plants WHERE collectionId = ?");
            $statement->bind_param("i", $collectionId);
            $statement->execute();
            $plants = $statement->get_result();
            if ($plants->num_rows > 0) {
                $plant = $plants->fetch_assoc();
                if ($plant['photo'] != null) {
                    $plantImage = file_get_contents($plant['photo']);
                } else
                    $plantImage = file_get_contents('./p1.jpg');
            } else
                $plantImage = file_get_contents('./p1.jpg');
            header('Content-Type: image/jpeg');
            echo $plantImage;
        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
            header($response['status_code_header']);
            header($response['content_type_header']);
            echo $response['body'];
        }
    }
    public function getImageById()
    {
        $id = $_GET['id'];
        try {
            $statement = $this->Db->prepare("SELECT photo FROM plants WHERE id = ?");
            $statement->bind_param("i", $id);
            $statement->execute();
            $plants = $statement->get_result();
            if ($plants->num_rows > 0) {
                $plant = $plants->fetch_assoc();
                if ($plant['photo'] != null) {
                    $plantImage = file_get_contents($plant['photo']);
                } else
                    $plantImage = file_get_contents('./p1.jpg');
            } else
                $plantImage = file_get_contents('./p1.jpg');
            header('Content-Type: image/jpeg');
            echo $plantImage;
        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
            header($response['status_code_header']);
            header($response['content_type_header']);
            echo $response['body'];
        }
    }
    public function getPlantById()
    {
        $id = $_GET['id'];
        try {
            $statement = $this->Db->prepare("SELECT name,description,color,season,type,zone FROM plants WHERE id = ?");
            $statement->bind_param("i", $id);
            $statement->execute();
            $result = $statement->get_result();
            $raspuns = array();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $name = $row['name'];
                $desc = $row['description'];
                $color = $row['color'];
                $season = $row['season'];
                $type = $row['type'];
                $zone = $row['zone'];

                $raspuns = array(
                    "name" => $name,
                    "desc" => $desc,
                    "color" => $color,
                    "season" => $season,
                    "type" => $type,
                    "zone" => $zone
                );
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode($raspuns);

            } else {
                $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'User not found']);
            }
        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
        }
        header($response['status_code_header']);
        header($response['content_type_header']);
        echo $response['body'];
    }
    public function deletePlantByPlantId()
    {
        $plantaId = $_GET['plantaId'];
        try {
            $statement = $this->Db->prepare("SELECT photo FROM plants WHERE id = ?");
            $statement->bind_param("i", $plantaId);
            $statement->execute();
            $image = $statement->get_result()->fetch_assoc();
            $imagePath = $image['photo'];
            unlink($imagePath);
            $statement = $this->Db->prepare("DELETE FROM plants WHERE id = ?");
            $statement->bind_param("i", $plantaId);
            $statement->execute();
            if ($statement->affected_rows > 0) {
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'Plant deleted successfully']);
            } else {
                $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'Plant not found']);
            }
        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
        }
        header($response['status_code_header']);
        header($response['content_type_header']);
        echo $response['body'];
    }
    public function getViewCount()
    {
        $id = $_GET['plantId'];
        try {
            $statement = $this->Db->prepare("SELECT views FROM plants WHERE id = ?");
            $statement->bind_param("i", $id);
            $statement->execute();
            $result = $statement->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $views = $row['views'];
                $raspuns = array(
                    "views" => $views,
                );
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode($raspuns);

            } else {
                $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'Plant not found']);
            }
        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
        }
        header($response['status_code_header']);
        header($response['content_type_header']);
        echo $response['body'];
    }
    public function incViewCount()
    {
        $id = file_get_contents('php://input');
        try {
            $statement = $this->Db->prepare("UPDATE plants SET views = views+1 WHERE id = ?");
            $statement->bind_param("i", $id);
            $statement->execute();
            if ($statement->affected_rows > 0) {
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'View incremented Successfully']);
                $stats = new StatsService();
                $stats->incPlantViews();
            } else {
                $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'Plant not found']);
            }
        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
        }
        header($response['status_code_header']);
        header($response['content_type_header']);
        echo $response['body'];
    }
    public function getMostViewed()
    {
        try {
            $nr = $_GET['nr'];
            $statement = $this->Db->prepare("SELECT p.id, p.name, u.username, p.views, c.userId from collections c join users u on c.userId=u.id join plants p on p.collectionId=c.id ORDER BY p.views DESC");
            $statement->execute();
            $result = $statement->get_result();
            $raspuns = array();
            if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                    $responseRow = array(
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'userName' => $row['username'],
                        'views' => $row['views'],
                        'userId' => $row['userId']
                    );
                    $raspuns[] = $responseRow;
                    $i = $i + 1;
                    if ($i > $nr)
                        break;
                }
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode($raspuns);
            } else {
                $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'Collection not found']);
            }
            header($response['status_code_header']);
            header($response['content_type_header']);
            echo $response['body'];
        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);

            header($response['status_code_header']);
            header($response['content_type_header']);
            echo $response['body'];
        }
    }
    public function getTop10ByViewsRSS()
    {
        try {
            $statement = $this->Db->prepare("SELECT p.id, p.name, u.username, p.views, c.userId from collections c join users u on c.userId=u.id join plants p on p.collectionId=c.id ORDER BY p.views DESC");
            $statement->execute();
            $result = $statement->get_result();
            $raspuns = array();
            if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                    $responseRow = array(
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'userName' => $row['username'],
                        'views' => $row['views'],
                        'userId' => $row['userId'],
                        'rank' => $i
                    );
                    $raspuns[] = $responseRow;
                    $i = $i + 1;
                    if ($i > 10)
                        break;
                }
                $rssFeed = new SimpleXMLElement('<rss version="2.0"></rss>');
                $channel = $rssFeed->addChild('channel');
                $channel->addChild('title', 'Top Plants By View Count');
                $channel->addChild('link', 'http://localhost/Proiect/Frontend/tops/tops.html');
                $channel->addChild('description', 'RSS feed for the top 10 plants, in order of how many views they currently have');

                foreach ($raspuns as $plant) {
                    $item = $channel->addChild('item');
                    $item->addChild('title', $plant['name']);
                    $item->addChild('description', 'for the link to work, add 2 cookies, one named "plantId" with the id bellow, and one named "ownerId", with the ownerId bellow');
                    $item->addChild('link', 'http://localhost/Proiect/Frontend/plante/plante.html');
                    $item->addChild('owner', $plant['userName']);
                    $item->addChild('views', $plant['views']);
                    $item->addChild('rank', $plant['rank']);
                    $item->addChild('id', $plant['id']);
                    $item->addChild('ownerId', $plant['userId']);
                }
                $rssString = $rssFeed->asXML();
                $dom = new DOMDocument("1.0");
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $dom->loadXML($rssString);
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/xml';
                $response['body'] = $dom->saveXML();
            } else {
                $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'Collection not found']);
            }
            header($response['status_code_header']);
            header($response['content_type_header']);
            echo $response['body'];
        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);

            header($response['status_code_header']);
            header($response['content_type_header']);
            echo $response['body'];
        }
    }
    public function updateName()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);
        $id = $requestData['id'];
        $newName = $requestData['name'];
        try {
            $statement = $this->Db->prepare("UPDATE plants SET name = ? WHERE id = ?");
            $statement->bind_param("si", $newName, $id);
            $statement->execute();

            if ($statement->affected_rows === 1) {
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'Name updated successfully']);
            } else {
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'Nothing to change']);
            }
        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
        }
        header($response['status_code_header']);
        header($response['content_type_header']);
        echo $response['body'];
    }
    public function updateDesc()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);
        $id = $requestData['id'];
        $newDesc = $requestData['desc'];
        try {
            $statement = $this->Db->prepare("UPDATE plants SET description = ? WHERE id = ?");
            $statement->bind_param("si", $newDesc, $id);
            $statement->execute();

            if ($statement->affected_rows === 1) {
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'Name updated successfully']);
            } else {
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'Nothing to change']);
            }
        } catch (PDOException $e) {
            $response['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Internal Server Error']);
        }
        header($response['status_code_header']);
        header($response['content_type_header']);
        echo $response['body'];
    }
    public function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['content_type_header'] = 'Content-Type: application/json';
        $response['body'] = json_encode(array("Result" => "Not Found"));
        echo $response['body'];
    }
}