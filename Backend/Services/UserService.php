<?php

include_once("../Entities/User.php");
include_once("Config.php");
include_once("../Services/TokenService.php");

class UserService
{
    private $CONFIG;
    private $Db;
    private $secret_Key = '%aaSWvtJ98os_b<IQ_c$j<_A%bo_[xgct+j$d6LJ}^<pYhf+53k^-R;Xs<l%5dF';
    private $domainName = "https://127.0.0.1";
    private $response;
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
                //       echo "Successfully connected to DB";
            }
        } catch (mysqli_sql_exception $e) {
            trigger_error("Could not connect to database: " . $e->getMessage(), E_USER_ERROR);
        }
    }
    public function getUserBySignInfo($username, $password)
    {
        try {
            $statement = $this->Db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
            $statement->bind_param("ss", $username, $password);
            $statement->execute();
            $result = $statement->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $user = new User(
                    $row['id'],
                    $row['username'],
                    $row['password'],
                    $row['email'],
                    $row['firstName'],
                    $row['lastName']
                );
                return $user;
            }
            return null;
        } catch (PDOException $e) {
            trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
        }
    }

    public function login()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $user = $this->getUserBySignInfo($username, $password);
        if ($user) {
            file_put_contents('./log.txt',$user,FILE_APPEND);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = $user;
        } else {
            $response['status_code_header'] = 'HTTP/1.1 404 Not found';
            $response['content_type_header'] = 'Content-Type: application/json';
            $response['body'] = json_encode(['message' => 'Invalid credentials']);
        }
        return $response;
    }
    public function getUserById()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);
        $id = $requestData['id'];
        try {
            $statement = $this->Db->prepare("SELECT * FROM users WHERE id = ?");
            $statement->bind_param("i", $id);
            $statement->execute();
            $result = $statement->get_result();
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $user = new User(
                    $row['id'],
                    $row['username'],
                    $row['password'],
                    $row['email'],
                    $row['firstName'],
                    $row['lastName']
                );

                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode($user);

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
    public function updateUserPassword()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);
        $id = $requestData['id'];
        $newPassword = $requestData['password'];
        try {
            $statement = $this->Db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $statement->bind_param("si", $newPassword, $id);
            $statement->execute();

            if ($statement->affected_rows === 1) {
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'Password updated successfully']);
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

    public function updateUserProfile()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);
        $id = $requestData['id'];
        $firstName = $requestData['firstName'];
        $lastName = $requestData['lastName'];
        try {
            $statement = $this->Db->prepare("UPDATE users SET firstName = ?, lastName = ? WHERE id = ?");
            $statement->bind_param("ssi", $firstName, $lastName, $id);
            $statement->execute();
            if ($statement->affected_rows === 1) {
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode(['message' => 'Profile updated successfully']);
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
    public function createUser($user)
    {
        try {
            $username = $user->getUsername();
            $password = $user->getPassword();
            $email = $user->getEmail();
            $firstName = $user->getFirstName();
            $lastName = $user->getLastName();
            if ($username !== null) {
                $statement = $this->Db->prepare("INSERT INTO users (username, password, email, firstName, lastName) VALUES (?, ?, ?, ?, ?)");
                $statement->bind_param("sssss", $username, $password, $email, $firstName, $lastName);
                $statement->execute();
                $statement->close();
                return "User created";
            } else {
                return "Username cannot be null";
            }
        } catch (PDOException $e) {
            trigger_error("Error in " . __METHOD__ . ": " . $e->getMessage(), E_USER_ERROR);
        }
    }
    public function createUserFromFormData()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        echo '<pre>';
        print_r($data);
        echo '</pre>'; 
        if (isset($data['username']) && isset($data['password']) && isset($data['email']) && isset($data['firstName']) && isset($data['lastName'])) {
            $user = new User(
                0,
                $data['username'],
                $data['password'],
                $data['email'],
                $data['firstName'],
                $data['lastName']
            );

            return $this->createUser($user);
        } else {
            echo "Missing required form data";
        }
    }
    
    public function getUserByUsername()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);
        $username = $requestData['username'];
        try {
            $statement = $this->Db->prepare("SELECT * FROM users WHERE username = ?");
            $statement->bind_param("s", $username);
            $statement->execute();
            $result = $statement->get_result();
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $user = new User(
                    $row['id'],
                    $row['username'],
                    $row['password'],
                    $row['email'],
                    $row['firstName'],
                    $row['lastName']
                );
                $response['status_code_header'] = 'HTTP/1.1 200 OK';
                $response['content_type_header'] = 'Content-Type: application/json';
                $response['body'] = json_encode($user);
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
    
    public function getTop3ByCollections()
    {
        try {
            $statement = $this->Db->prepare("SELECT u.id, u.username, COUNT(*) from collections c join users u on c.userId=u.id GROUP BY u.id, u.username ORDER BY COUNT(*) DESC");
            $statement->execute();
            $result = $statement->get_result();
            $raspuns = array();
            if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                    $responseRow = array(
                        'userName' => $row['username'],
                        'cCount' => $row['COUNT(*)']
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
    public function getTop3ByPlants()
    {
        try {
            $statement = $this->Db->prepare("SELECT u.id, u.username, COUNT(*) from collections c join users u on c.userId=u.id join plants p on p.collectionId=c.id GROUP BY u.id, u.username ORDER BY COUNT(*) DESC");
            $statement->execute();
            $result = $statement->get_result();
            $raspuns = array();
            if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                    $responseRow = array(
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
    public function getTop10ByCollectionsRSS()
    {
        try {
            $statement = $this->Db->prepare("SELECT u.id, u.username, COUNT(*) from collections c join users u on c.userId=u.id GROUP BY u.id, u.username ORDER BY COUNT(*) DESC");
            $statement->execute();
            $result = $statement->get_result();
            $raspuns = array();
            if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                    $responseRow = array(
                        'userName' => $row['username'],
                        'cCount' => $row['COUNT(*)'],
                        'rank' => $i
                    );
                    $raspuns[] = $responseRow;
                    $i = $i + 1;
                    if ($i > 10)
                        break;
                }
                $rssFeed = new SimpleXMLElement('<rss version="2.0"></rss>');
                $channel = $rssFeed->addChild('channel');
                $channel->addChild('title', 'Top Users By Collection Count');
                $channel->addChild('link', 'http://localhost/Proiect/Frontend/tops/tops.html');
                $channel->addChild('description', 'RSS feed for the top 10 users, in order of how many collections they currently have');

                foreach ($raspuns as $user) {
                    $item = $channel->addChild('item');
                    $item->addChild('title', $user['userName']);
                    $item->addChild('description', 'there is no link, as profiles are private');
                    $item->addChild('collectionCount', $user['cCount']);
                    $item->addChild('rank', $user['rank']);
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
    public function getTop10ByPlantsRSS()
    {
        try {
            $statement = $this->Db->prepare("SELECT u.id, u.username, COUNT(*) from collections c join users u on c.userId=u.id join plants p on p.collectionId=c.id GROUP BY u.id, u.username ORDER BY COUNT(*) DESC");
            $statement->execute();
            $result = $statement->get_result();
            $raspuns = array();
            if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                    $responseRow = array(
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
                $channel->addChild('title', 'Top Users By Plant Count');
                $channel->addChild('link', 'http://localhost/Proiect/Frontend/tops/tops.html');
                $channel->addChild('description', 'RSS feed for the top 10 users, in order of how many plants they currently have');

                foreach ($raspuns as $user) {
                    $item = $channel->addChild('item');
                    $item->addChild('title', $user['userName']);
                    $item->addChild('description', 'there is no link, as profiles are private');
                    $item->addChild('plantCount', $user['pCount']);
                    $item->addChild('rank', $user['rank']);
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
    public function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['content_type_header'] = 'Content-Type: application/json';
        $response['body'] = json_encode(array("Result" => "Not Found"));
        return $response;
    }
}