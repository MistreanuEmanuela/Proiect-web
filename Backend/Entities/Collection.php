<?PHP

class Collection
{
    private $id;
    private $name;
    private $plantColor;

    /**
     * @param $id
     * @param $name
     * @param $plantColor
     */
    public function __construct($id, $name, $plantColor)
    {
        $this->id = $id;
        $this->name = $name;
        $this->plantColor = $plantColor;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPlantColor()
    {
        return $this->plantColor;
    }

    /**
     * @param mixed $plantColor
     */
    public function setPlantColor($plantColor)
    {
        $this->plantColor = $plantColor;
    }
}