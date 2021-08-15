
<?php
require_once 'Image.php';
class Product
{
    public $name; // Product Name (default first column)
    public $sheet_name; // Which sheet this product is found
    public $row; // Which row this product belongs
    public $image; // Which image won similarity contest
    public $image_similarity; // How similar the standard is with the selected candidate is by the algorithm.
    public $should_upload; // Upload the image or not? (depends if image_similarity passes)


    public function Product($name, $sheet_name, $row)
    {
        $this->name = $name;
        $this->sheet_name = $sheet_name;
        $this->row = $row;
        $this->image = null;
        $this->image_similarity = null;
        $this->should_upload = true;
    }
    public function setImage($image)
    {
        $this->image = $image;
    }
    public function getImage()
    {
        return $this->image;
    }
}
