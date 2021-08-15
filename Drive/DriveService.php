<?php
require_once 'utils.php';
class DriveService
{
    public $service;
    public function DriveService($client)
    {
        $this->service = new Google_Service_Drive($client);
    }

    /**
     * @return Image[]
     */
    public function get_image_refs(): iterable
    {
        $jsonStr = file_get_contents("config.json");

        // Parse data from config file
        $config = json_decode($jsonStr);

        $root_file = $config->images_root_folder_id;

        // We start searching from the root folder
        $root_folders = $this->get_files($root_file);

        $images = [];

        // Get each subfolder's images, store it into $images
        foreach ($root_folders as $root_folder) {
            $newImages = $this->get_files($root_folder->id);
            $images = [...$images, ...$newImages];
        }

        // Convert images Image Objects
        return array_map(function ($img) {
            return new Image($img->name, $img->id);
        }, $images);
    }
    public function get_files(string $parent)
    {
        $optParams = array(
            'pageSize' => 1000,
            'fields' => 'nextPageToken, files(id, name, mimeType)',
            'q' => 'trashed = false and ' . "'" . $parent . "'" . ' in parents',
        );
        $results = $this->service->files->listFiles($optParams);
        $root_files = $results->getFiles();

        return array_filter($root_files, function ($f) {
            return $this->is_image($f->mimeType) or $this->is_folder($f->mimeType);
        });
    }
    public function copyFile($originFileId, $copyTitle, $output_folder_id)
    {
        // https://content.googleapis.com/drive/v2/files/1oMWw_d9lEPg7zzLBUhUJUuwVcnmmdoAL/copy

        $copiedFile = new Google_Service_Drive_DriveFile();
        $copiedFile->setName($copyTitle);
        $copiedFile->setParents([$output_folder_id]);
        try {
            return $this->service->files->copy($originFileId, $copiedFile);
        } catch (Exception $e) {
            print "An error occurred: " . $e->getMessage();
        }
        return null;
    }
    public function move_files($products)
    {
        $jsonStr = file_get_contents("config.json");
        $config = json_decode($jsonStr);

        // Get from config
        $output_folder_id = $config->output_folder_id;
        echo "\n\n" . "Copying images to outputfolder...";
        foreach ($products as $p) {
            $this->copyFile($p->image->id, normalize($p->image->file_name), $output_folder_id);
        }
        echo "\n" . "Finished.";
    }

    public static function is_image(string $mimeType): bool
    {
        return $mimeType == "image/jpeg";
    }

    public static function is_folder(string $mimeType): bool
    {
        return $mimeType == "application/vnd.google-apps.folder";
    }
}
