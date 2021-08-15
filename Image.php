
<?php
class Image
{
    public $file_name;
    public $normalized_file_name;
    public $id;

    public function Image($file_name, $id)
    {
        $this->file_name = $file_name;
        $this->id = $id;
    }

}
