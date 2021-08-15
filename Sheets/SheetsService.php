<?php
require_once 'Product.php';
class SheetsService
{
    private $service;
    public function SheetsService($client)
    {
        $this->service = new Google_Service_Sheets($client);
    }
    /**
     * @return Product[]
     */
    public function get_product_names(): iterable
    {
        $jsonStr = file_get_contents("config.json");

        // Parse data from config file
        $config = json_decode($jsonStr);

        // Read from config the sheet id
        $spreadsheetId = $config->source_file_id;
        $name_column = $config->source_file_name_column;
        $image_column = $config->source_file_image_column;
        $read_start = $config->source_file_read_start;

        $sheet_names = [];

        // Handle which sheets should we extract products from
        if (count($config->source_file_target_sheet_names)) {
            $sheet_names = $config->source_file_target_sheet_names;
        } else {
            $sheet_names = $this->get_all_sheet_names($spreadsheetId);
        }

        $products = [];

        // For each sheet in the source file

        foreach ($sheet_names as $sheet_name) {

            $range = $sheet_name . "!" . $name_column . $read_start . ":" . $name_column;
            $response = $this->service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();
            foreach ($values as $index => $row) {

                // Add the products from the sheets under products collection
                array_push($products, new Product($row[0], $sheet_name, $index + (int) $read_start));
            }
        }

        // Return all products from all the sheets
        return $products;
    }
    public function write_to_source_file(iterable $values, $range)
    {
        $jsonStr = file_get_contents("config.json");
        // Parse data from config file
        $config = json_decode($jsonStr);
        // Read from config the sheet id
        $sheet_id = $config->source_file_id;

        $updateBody = new Google_Service_Sheets_ValueRange([
            'range' => $range,
            'majorDimension' => 'ROWS',
            'values' => [
                ...$values,
            ],
        ]);
        $this->service->spreadsheets_values->update(
            $sheet_id,
            $range,
            $updateBody,
            ['valueInputOption' => "RAW"]
        );
    }
    /**
     * @return string[]
     */
    private function get_all_sheet_names($spreadsheetId): iterable
    {

        $response = $this->service->spreadsheets->get($spreadsheetId);
        return array_map(function ($sheet) {return $sheet->properties->title;}, $response->sheets);

    }

}
