<?php
include_once ("Config.php");
include_once("../Entities/CollectionTag.php");
class tagService
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
    public function createTag($tag, $collectionId)
    {
        $query = "SELECT collectionId FROM collectiontags WHERE tag = ? and collectionId=?";
        $statement = $this->Db->prepare($query);
        $statement->bind_param("si", $tag,$collectionId);
        $statement->execute();
        $result = $statement->get_result();

        if ($result->num_rows > 0) {
            $response = array(
                'message' => 'Collection with the tag already exists'
            );
            $jsonResponse = json_encode($response);
            header('Content-Type: application/json');
            http_response_code(409);
            echo $jsonResponse;
        } else {
            $query = "INSERT INTO collectiontags (collectionId, tag) VALUES (?, ?)";
            $statement = $this->Db->prepare($query);
            $statement->bind_param("ss", $collectionId,  $tag);
            if ($statement->execute()) {
                $response = array(
                    'collectionId' => $collectionId,
                    'message' => 'New tag added successfully'
                );
                $jsonResponse = json_encode($response);
                header('Content-Type: application/json');
                http_response_code(200);
                echo $jsonResponse;
            } else {
                header('Content-Type: application/json');
                http_response_code(500);

                $errorResponse = array(
                    'message' => 'Failed to add collection tag'
                );

                echo json_encode($errorResponse);
            }
        }
    }
}