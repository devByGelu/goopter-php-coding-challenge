<?php
function improved_similarity()
{}

function compute_similarity($standard, $current, $contender)
{
    similar_text($current, $standard, $perc1);
    similar_text($contender, $standard, $perc2);
    return (array("hasImproved" => $perc2 > $perc1 ? 'true' : 'false', "image_similarity" => (string) $perc2));
}

// Attempt to normalize/clean the string
function normalize(string $str): string
{
    // Remove brackets and its enclosure
    if (strpos($str, "(")) {
        $str = substr($str, 0, strpos($str, "("));
    }
    // Remove extension

    if (strpos($str, ".")) {
        $str = substr($str, 0, strpos($str, "."));
    }

    // Remove trailing whitespace
    $str = trim($str);

    // Make it lowercase
    $str = strtolower($str);

    // Replace spaces with dash
    $str = str_replace(' ', '-', $str);

    return $str;

}
