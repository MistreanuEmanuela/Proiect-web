<?php
include_once("Config.php");
include_once("StatsService.php");
include_once("../Entities/Collection.php");
class CollectionService
{
    private $CONFIG;
    /* Db handler */
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
            }
        } catch (mysqli_sql_exception $e) {
            trigger_error("Could not connect to database: " . $e->getMessage(), E_USER_ERROR);
        }
    }
    public function createCollection($name, $desc, $userId)
    {
        $coll= new Collection(0,$name,$userId,0,$desc);
        $coll_name=$coll->getName();
        $coll_userId=$coll->getUserId();
        $coll_desc=$coll->getDescription();
        $query = "INSERT INTO collections (name, userId,description) VALUES (?, ?,?)";
        $statement = $this->Db->prepare($query);
        $statement->bind_param("sss", $coll_name, $coll_userId, $coll_desc);
        if ($statement->execute()) {
            $collectionId = $this->Db->insert_id;
            $response = array(
                'collectionId' => $collectionId,
                'message' => 'New collection created successfully'
            );
            $jsonResponse = json_encode($response);
            header('Content-Type: application/json');
            http_response_code(200);
            echo $jsonResponse;
            $stats = new StatsService();
            $stats->incCollections();
        } else {
            header('Content-Type: application/json');
            http_response_code(500);

            $errorResponse = array(
                'message' => 'Failed to create a new collection'
            );

            echo json_encode($errorResponse);
        }
    }
    public function getCollectionsByUserId()
    {
        $userId = $_GET['userId'];
        try {
            $statement = $this->Db->prepare("SELECT id,name,views FROM collections WHERE userId = ?");
            $statement->bind_param("i", $userId);
            $statement->execute();
            $result = $statement->get_result();
            $raspuns = array();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $collectionId = $row['id'];
                    $collectionName = $row['name'];
                    $views = $row['views'];
                    $responseRow = array(
                        "id" => $collectionId,
                        "name" => $collectionName,
                        "views" => $views
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
    public function getCollectionsByCuloare()
    {
        $c = $_GET['culoare'];
        $a = $_GET['anotimp'];
        $r = $_GET['regiune'];
        $i = $_GET['tip'];
        try {

            if ($c !== 'c' && $a !== 'a' && $r !== 'r' && $i !== 'i') {
                $statement = $this->Db->prepare("
                    SELECT collections.id, collections.name, collections.views, collections.userId,
                  users.lastName, users.firstName
                  FROM collections
                  JOIN users ON collections.userId = users.id
                  JOIN collectiontags AS ct1 ON ct1.collectionId = collections.id AND ct1.tag = ?
                  JOIN collectiontags AS ct2 ON ct2.collectionId = collections.id AND ct2.tag = ?
                  JOIN collectiontags AS ct3 ON ct3.collectionId = collections.id AND ct3.tag = ?
                  JOIN collectiontags AS ct4 ON ct4.collectionId = collections.id AND ct4.tag = ?
              ");
                $statement->bind_param("ssss", $c, $a, $r, $i);
            } elseif ($c !== 'c' && $a !== 'a' && $r !== 'r' && $i === 'i') {
                $statement = $this->Db->prepare("
                    SELECT collections.id, collections.name, collections.views, collections.userId,
                    users.lastName, users.firstName
                    FROM collections
                    JOIN users ON collections.userId = users.id
                    JOIN collectiontags AS ct1 ON ct1.collectionId = collections.id AND ct1.tag = ?
                    JOIN collectiontags AS ct2 ON ct2.collectionId = collections.id AND ct2.tag = ?
                    JOIN collectiontags AS ct3 ON ct3.collectionId = collections.id AND ct3.tag = ?
                ");
                $statement->bind_param("sss", $c, $a, $r);
            } elseif ($c !== 'c' && $a !== 'a' && $r === 'r' && $i !== 'i') {
                $statement = $this->Db->prepare("
                    SELECT collections.id, collections.name, collections.views, collections.userId,
                    users.lastName, users.firstName
                    FROM collections
                    JOIN users ON collections.userId = users.id
                    JOIN collectiontags AS ct1 ON ct1.collectionId = collections.id AND ct1.tag = ?
                    JOIN collectiontags AS ct2 ON ct2.collectionId = collections.id AND ct2.tag = ?
                    JOIN collectiontags AS ct3 ON ct3.collectionId = collections.id AND ct3.tag = ?
                ");
                $statement->bind_param("sss", $c, $a, $i);
            } elseif ($c === 'c' && $a !== 'a' && $r !== 'r' && $i !== 'i') {
                $statement = $this->Db->prepare("
                    SELECT collections.id, collections.name, collections.views, collections.userId,
                    users.lastName, users.firstName
                    FROM collections
                    JOIN users ON collections.userId = users.id
                    JOIN collectiontags AS ct1 ON ct1.collectionId = collections.id AND ct1.tag = ?
                    JOIN collectiontags AS ct2 ON ct2.collectionId = collections.id AND ct2.tag = ?
                    JOIN collectiontags AS ct3 ON ct3.collectionId = collections.id AND ct3.tag = ?
                ");
                $statement->bind_param("sss", $a, $r, $i);
            } elseif ($c !== 'c' && $a === 'a' && $r !== 'r' && $i !== 'i') {
                $statement = $this->Db->prepare("
                    SELECT collections.id, collections.name, collections.views, collections.userId,
                    users.lastName, users.firstName
                    FROM collections
                    JOIN users ON collections.userId = users.id
                    JOIN collectiontags AS ct1 ON ct1.collectionId = collections.id AND ct1.tag = ?
                    JOIN collectiontags AS ct2 ON ct2.collectionId = collections.id AND ct2.tag = ?
                    JOIN collectiontags AS ct3 ON ct3.collectionId = collections.id AND ct3.tag = ?
                ");
                $statement->bind_param("sss", $c, $r, $i);
            } elseif ($c !== 'c' && $a !== 'a' && $r === 'r' && $i === 'i') {
                $statement =
                    $this->Db->prepare("
                    SELECT collections.id, collections.name, collections.views, collections.userId,
                    users.lastName, users.firstName
                    FROM collections
                    JOIN users ON collections.userId = users.id
                    JOIN collectiontags AS ct1 ON ct1.collectionId = collections.id AND ct1.tag = ?
                    JOIN collectiontags AS ct2 ON ct2.collectionId = collections.id AND ct2.tag = ?
                    ");
                $statement->bind_param("ss", $c, $a);
            } elseif ($c !== 'c' && $a === 'a' && $r !== 'r' && $i === 'i') {
                $statement =
                    $this->Db->prepare("
                    SELECT collections.id, collections.name, collections.views, collections.userId,
                    users.lastName, users.firstName
                    FROM collections
                    JOIN users ON collections.userId = users.id
                    JOIN collectiontags AS ct1 ON ct1.collectionId = collections.id AND ct1.tag = ?
                    JOIN collectiontags AS ct2 ON ct2.collectionId = collections.id AND ct2.tag = ?
                    ");
                $statement->bind_param("ss", $c, $r);
            } elseif ($c !== 'c' && $a === 'a' && $r === 'r' && $i !== 'i') {
                $statement =
                    $this->Db->prepare("
                    SELECT collections.id, collections.name, collections.views, collections.userId,
                    users.lastName, users.firstName
                    FROM collections
                    JOIN users ON collections.userId = users.id
                    JOIN collectiontags AS ct1 ON ct1.collectionId = collections.id AND ct1.tag = ?
                    JOIN collectiontags AS ct2 ON ct2.collectionId = collections.id AND ct2.tag = ?
                    ");
                $statement->bind_param("ss", $c, $i);
            } elseif ($c === 'c' && $a !== 'a' && $r !== 'r' && $i === 'i') {
                $statement =
                    $this->Db->prepare("
                    SELECT collections.id, collections.name, collections.views, collections.userId,
                    users.lastName, users.firstName
                    FROM collections
                    JOIN users ON collections.userId = users.id
                    JOIN collectiontags AS ct1 ON ct1.collectionId = collections.id AND ct1.tag = ?
                    JOIN collectiontags AS ct2 ON ct2.collectionId = collections.id AND ct2.tag = ?
                    ");
                $statement->bind_param("ss", $a, $r);
            } elseif ($c === 'c' && $a !== 'a' && $r === 'r' && $i !== 'i') {
                $statement =
                    $this->Db->prepare("
                    SELECT collections.id, collections.name, collections.views, collections.userId,
                    users.lastName, users.firstName
                    FROM collections
                    JOIN users ON collections.userId = users.id
                    JOIN collectiontags AS ct1 ON ct1.collectionId = collections.id AND ct1.tag = ?
                    JOIN collectiontags AS ct2 ON ct2.collectionId = collections.id AND ct2.tag = ?
                    ");
                $statement->bind_param("ss", $a, $i);
            } elseif ($c === 'c' && $a === 'a' && $r !== 'r' && $i !== 'i') {
                $statement =
                    $this->Db->prepare("
                    SELECT collections.id, collections.name, collections.views, collections.userId,
                    users.lastName, users.firstName
                    FROM collections
                    JOIN users ON collections.userId = users.id
                    JOIN collectiontags AS ct1 ON ct1.collectionId = collections.id AND ct1.tag = ?
                    JOIN collectiontags AS ct2 ON ct2.collectionId = collections.id AND ct2.tag = ?
                    ");
                $statement->bind_param("ss", $r, $i);
            } elseif ($c !== 'c' && $a === 'a' && $r === 'r' && $i === 'i') {

                $statement = $this->Db->prepare("SELECT collections.id, collections.name, 
                    collections.views, collections.userId, users.lastName, users.firstName
                    FROM collections JOIN users ON collections.userId = users.id JOIN collectiontags 
                    on collectiontags.collectionId = collections.id where collectiontags.tag=?");
                $statement->bind_param("s", $c);

            } elseif ($c === 'c' && $a !== 'a' && $r === 'r' && $i === 'i') {

                $statement = $this->Db->prepare("SELECT collections.id, collections.name, 
                    collections.views, collections.userId, users.lastName, users.firstName
                    FROM collections JOIN users ON collections.userId = users.id JOIN collectiontags 
                    on collectiontags.collectionId = collections.id where collectiontags.tag=?");
                $statement->bind_param("s", $a);

            } elseif ($c === 'c' && $a === 'a' && $r !== 'r' && $i === 'i') {

                $statement = $this->Db->prepare("SELECT collections.id, collections.name, 
                    collections.views, collections.userId, users.lastName, users.firstName
                    FROM collections JOIN users ON collections.userId = users.id JOIN collectiontags 
                    on collectiontags.collectionId = collections.id where collectiontags.tag=?");

                $statement->bind_param("s", $r);
            } elseif ($c === 'c' && $a === 'a' && $r === 'r' && $i !== 'i') {

                $statement = $this->Db->prepare("SELECT collections.id, collections.name, 
                    collections.views, collections.userId, users.lastName, users.firstName
                    FROM collections JOIN users ON collections.userId = users.id JOIN collectiontags 
                    on collectiontags.collectionId = collections.id where collectiontags.tag=?");
                $statement->bind_param("s", $i);

            }
            $statement->execute();
            $result = $statement->get_result();
            $raspuns = array();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $collectionId = $row['id'];
                    $collectionName = $row['name'];
                    $userId = $row['userId'];
                    $views = $row['views'];
                    $firstName = $row['firstName'];
                    $lastName = $row['lastName'];
                    $responseRow = array(
                        "id" => $collectionId,
                        "name" => $collectionName,
                        "views" => $views,
                        "userId" => $userId,
                        "lastName" => $firstName,
                        "firstName" => $lastName
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
    public function getAllCollection()
    {
        try {
            $statement = $this->Db->prepare("SELECT collections.id, collections.name, collections.views, collections.userId, users.lastName, users.firstName FROM collections JOIN users ON collections.userId = users.id");
            $statement->execute();
            $result = $statement->get_result();
            $raspuns = array();
            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {
                    $collectionId = $row['id'];
                    $collectionName = $row['name'];
                    $userId = $row['userId'];
                    $views = $row['views'];
                    $firstName = $row['firstName'];
                    $lastName = $row['lastName'];
                    $responseRow = array(
                        "id" => $collectionId,
                        "name" => $collectionName,
                        "views" => $views,
                        "userId" => $userId,
                        "lastName" => $firstName,
                        "firstName" => $lastName
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
    public function getViewCount()
    {
        $id = $_GET['collectionId'];
        try {
            $statement = $this->Db->prepare("SELECT views FROM collections WHERE id = ?");
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
    public function incViewCount()
    {
        $id = file_get_contents('php://input');
        try {

            $statement = $this->Db->prepare("UPDATE collections SET views = views+1 WHERE id = ?");
            $statement->bind_param("i", $id);
            $statement->execute();
            if ($statement->affected_rows > 0) {
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'View incremented Successfully']);
                $stats = new StatsService();
                $stats->incCollectionViews();
            } else {
                $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'Collection not found']);
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
    public function getbyCollectionId($collectionId)
    {
        try {
            $statement = $this->Db->prepare("SELECT id, name, description,userId from collections where id= ? ");
            $statement->bind_param("i", $collectionId);
            $statement->execute();
            $result = $statement->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $collectionId = $row['id'];
                $collectionName = $row['name'];
                $description = $row['description'];
                $userId = $row['userId'];
                $responseRow = array(
                    'id' => $collectionId,
                    'name' => $collectionName,
                    'description' => $description,
                    'user' => $userId
                );

                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode($responseRow);
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
    public function deleteCollectionById()
    {
        $id = $_GET['collectionId'];
        try {
            $statement = $this->Db->prepare("DELETE FROM collections WHERE id = ?");
            $statement->bind_param("i", $id);
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
    public function getBiggest3()
    {
        try {
            $statement = $this->Db->prepare("SELECT c.id, c.name, u.username, COUNT(*) from collections c join users u on c.userId=u.id join plants p on p.collectionId=c.id GROUP BY c.id, c.name, u.username ORDER BY COUNT(*) DESC");
            $statement->execute();
            $result = $statement->get_result();
            $raspuns = array();
            if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                    $responseRow = array(
                        'id' => $row['id'],
                        'colName' => $row['name'],
                        'userName' => $row['username'],
                        'pCount' => $row['COUNT(*)']
                    );
                    $raspuns[] = $responseRow;
                    $i = $i + 1;
                    if ($i > 3)
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
    public function getMostViewed3()
    {
        try {
            $statement = $this->Db->prepare("SELECT c.id, c.name, u.username, c.views from collections c join users u on c.userId=u.id ORDER BY c.views DESC");
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
                        'views' => $row['views']
                    );
                    $raspuns[] = $responseRow;
                    $i = $i + 1;
                    if ($i > 3)
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
    public function getTop10BySizeRSS()
    {
        try {
            $statement = $this->Db->prepare("SELECT c.id, c.name, u.username, COUNT(*) from collections c join users u on c.userId=u.id join plants p on p.collectionId=c.id GROUP BY c.id, c.name, u.username ORDER BY COUNT(*) DESC");
            $statement->execute();
            $result = $statement->get_result();
            $raspuns = array();
            if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                    $responseRow = array(
                        'id' => $row['id'],
                        'colName' => $row['name'],
                        'userName' => $row['username'],
                        'pCount' => $row['COUNT(*)'],
                        'rank' => $i
                    );
                    $raspuns[] = $responseRow;
                    $i = $i + 1;
                    if ($i > 10)
                        break;
                }
                $rssFeed = new SimpleXMLElement('<rss version="2.0"></rss>');
                $channel = $rssFeed->addChild('channel');
                $channel->addChild('title', 'Top Collections By Size');
                $channel->addChild('link', 'http://localhost/Proiect/Frontend/tops/tops.html');
                $channel->addChild('description', 'RSS feed for the top 10 collections, in order of how many plants they currently have');

                foreach ($raspuns as $collection) {
                    $item = $channel->addChild('item');
                    $item->addChild('title', $collection['colName']);
                    $item->addChild('description', 'for the link to work, add cookie named "collection" with the value being a json with "id" and "name" elements. The name will be the title, the id is specified bellow');
                    $item->addChild('link', 'http://localhost/Proiect/Frontend/plante/plante.html');
                    $item->addChild('owner', $collection['userName']);
                    $item->addChild('plantCount', $collection['pCount']);
                    $item->addChild('rank', $collection['rank']);
                    $item->addChild('id', $collection['id']);
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
    public function getTop10ByViewsRSS()
    {
        try {
            $statement = $this->Db->prepare("SELECT c.id, c.name, u.username, c.views from collections c join users u on c.userId=u.id ORDER BY c.views DESC");
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
                        'rank' => $i
                    );
                    $raspuns[] = $responseRow;
                    $i = $i + 1;
                    if ($i > 10)
                        break;
                }
                $rssFeed = new SimpleXMLElement('<rss version="2.0"></rss>');
                $channel = $rssFeed->addChild('channel');
                $channel->addChild('title', 'Top Collections By View Count');
                $channel->addChild('link', 'http://localhost/Proiect/Frontend/tops/tops.html');
                $channel->addChild('description', 'RSS feed for the top 10 collections, in order of how many views they currently have');

                foreach ($raspuns as $collection) {
                    $item = $channel->addChild('item');
                    $item->addChild('title', $collection['name']);
                    $item->addChild('description', 'for the link to work, add cookie named "collection" with the value being a json with "id" and "name" elements. The name will be the title, the id is specified bellow');
                    $item->addChild('link', 'http://localhost/Proiect/Frontend/plante/plante.html');
                    $item->addChild('owner', $collection['userName']);
                    $item->addChild('views', $collection['views']);
                    $item->addChild('rank', $collection['rank']);
                    $item->addChild('id', $collection['id']);
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
            $statement = $this->Db->prepare("UPDATE collections SET name = ? WHERE id = ?");
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
            $statement = $this->Db->prepare("UPDATE collections SET description = ? WHERE id = ?");
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