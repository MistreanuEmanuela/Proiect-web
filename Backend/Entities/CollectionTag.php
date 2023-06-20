<?PHP
class CollectionTag
{
    private $collectionId;
    private $tag;
    private $id;

    public function __construct($collectionId, $tag, $id)
    {
        $this->collectionId = $collectionId;
        $this->tag = $tag;
        $this->id = $id;
    }

    public function getCollectionId()
    {
        return $this->collectionId;
    }

    public function setCollectionId($collectionId)
    {
        $this->collectionId = $collectionId;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}
