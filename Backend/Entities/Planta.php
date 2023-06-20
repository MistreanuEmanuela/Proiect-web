
<?php

include_once("../inregistrare/Config.php");

class planta implements JsonSerializable
{
    private $id;
    private $name;
    private $description;
    private $collectionId;
    private $photo;
    private $views;
    private $color;
    private $season;
    private $type;
    private $zone;

    public function __construct($id, $name, $description, $collectionId, $photo, $views, $color, $season, $type, $zone)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->collectionId = $collectionId;
        $this->photo = $photo;
        $this->views = $views;
        $this->color = $color;
        $this->season = $season;
        $this->type = $type;
        $this->zone = $zone;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'collectionId' => $this->collectionId,
            'photo' => $this->photo,
            'views' => $this->views,
            'color' => $this->color,
            'season' => $this->season,
            'type' => $this->type,
            'zone' => $this->zone,
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

    public function getDescription()
    {
        return $this->description;
    }

    public function getCollectionId()
    {
        return $this->collectionId;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function getViews()
    {
        return $this->views;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getSeason()
    {
        return $this->season;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getZone()
    {
        return $this->zone;
    }



    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setCollectionId($collectionId)
    {
        $this->collectionId = $collectionId;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    public function setViews($views)
    {
        $this->views = $views;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }

    public function setSeason($season)
    {
        $this->season = $season;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setZone($zone)
    {
        $this->zone = $zone;
    }
}