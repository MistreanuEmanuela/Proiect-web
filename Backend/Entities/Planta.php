
<?php

include_once("../inregistrare/Config.php");
class planta implements JsonSerializable
{
    private $id;
    private $name;
    private $collectionId;
    private $photo;
    private $description; // New property

    public function __construct($id, $name, $collectionId, $photo, $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->collectionId = $collectionId;
        $this->photo = $photo;
        $this->description = $description;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'collectionId' => $this->collectionId,
            'photo' => $this->photo,
            'description' => $this->description, // Include description in serialization
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCollectionId()
    {
        return $this->collectionId;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
}