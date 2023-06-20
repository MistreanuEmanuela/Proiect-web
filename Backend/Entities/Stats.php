<?PHP
class Stats
{
    private $id;
    private $month;
    private $users;
    private $plants;
    private $collections;
    private $plantViews;
    private $collectionViews;

    public function __construct($id, $month, $users, $plants, $collections, $plantViews, $collectionViews)
    {
        $this->id = $id;
        $this->month = $month;
        $this->users = $users;
        $this->plants = $plants;
        $this->collections = $collections;
        $this->plantViews = $plantViews;
        $this->collectionViews = $collectionViews;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getMonth()
    {
        return $this->month;
    }

    public function setMonth($month)
    {
        $this->month = $month;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers($users)
    {
        $this->users = $users;
    }

    public function getPlants()
    {
        return $this->plants;
    }

    public function setPlants($plants)
    {
        $this->plants = $plants;
    }

    public function getCollections()
    {
        return $this->collections;
    }

    public function setCollections($collections)
    {
        $this->collections = $collections;
    }

    public function getPlantViews()
    {
        return $this->plantViews;
    }

    public function setPlantViews($plantViews)
    {
        $this->plantViews = $plantViews;
    }

    public function getCollectionViews()
    {
        return $this->collectionViews;
    }

    public function setCollectionViews($collectionViews)
    {
        $this->collectionViews = $collectionViews;
    }
}