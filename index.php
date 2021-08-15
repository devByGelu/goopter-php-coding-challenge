
<?php
require __DIR__ . '/vendor/autoload.php';
require_once 'GoogleClient/GoogleClient.php';
require_once 'Drive/DriveService.php';
require_once 'Sheets/SheetsService.php';
require_once 'utils.php';

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}

// Get the API client and construct the service object.
$google = new GoogleClient();
$client = $google->client;

// Initialize Services
$drive_service = new DriveService($client);
$sheets_service = new SheetsService($client);

// Get the target Products from Sheet
$products = $sheets_service->get_product_names();
$images = $drive_service->get_image_refs();

$jsonStr = file_get_contents("config.json");

// Parse data from config file
$config = json_decode($jsonStr);
$similarity_tolerance = $config->similarity_tolerance;

foreach ($products as $p) {
    $curr_similar_index = 0; // Hold index of currently most similar
    foreach ($images as $i => $img) {
        if ($p->getImage() == null) {
            $p->image = $img; // Initialize
            $curr_similar_index = $i;

        } else {

            $standard = normalize($p->name); // The given from the source file
            $current = normalize($p->getImage()->file_name); // The current best match
            $contender = normalize($img->file_name); // The contender to the best match so far

            $result = compute_similarity($standard, $current, $contender);

            // If we see improvement, then update the product's image
            if ($result['hasImproved'] == 'true') {
                $p->image = $img;
                $p->image_similarity = $result['image_similarity'];
                $curr_similar_index = $i;
            }

        }
    }

    $tolerated = floatval($p->image_similarity) > floatval($similarity_tolerance);

    // Only if image_similarity is > similarity_tolerance, we upload the image. Else we leave it blank
    if (!$tolerated) {
        echo '[ NO MATCH ] ' . $p->name . " is most similar to " . $p->getImage()->file_name . " (" . $p->image_similarity . "%) " . "\n\n ";
        $p->should_upload = false;
    } else {
        echo $p->name . " is most similar to " . $p->getImage()->file_name . " (" . $p->image_similarity . "%) " . "\n\n";

        // Remove the image since it was already selected.
        unset($images[$curr_similar_index]);

    }
}
// Map final file names from products array
$values = array_map(function ($p) {
    return [$p->should_upload ? normalize($p->getImage()->file_name) : ''];
}, $products);

$sheets_service->write_to_source_file($values, "Sheet1!B2:B113");

$drive_service->move_files(array_filter($products, function ($p) {return $p->should_upload == true;}));