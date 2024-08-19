<?php
require 'vendor/autoload.php';

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Heroku\Progress\Bar;

function scrapeInstagram($url) {
    $client = new Client();
    $crawler = $client->request('GET', $url);

    // Progress bar
    $progress = new Bar();
    $progress->display();

    $images = [];
    $captions = [];

    // Scrape images and captions
    $crawler->filter('article img')->each(function (Crawler $node, $i) use (&$images, &$captions, $progress) {
        $image = $node->attr('src');
        $caption = $node->attr('alt');

        $images[] = $image;
        $captions[] = $caption;

        // Update progress bar
        $progress->advance();
    });

    // Complete the progress
    $progress->finish();

    return [
        'images' => $images,
        'captions' => $captions,
    ];
}

// CLI prompt
echo "Enter Instagram Profile URL: ";
$handle = fopen("php://stdin", "r");
$url = trim(fgets($handle));

// Scrape Instagram data
$data = scrapeInstagram($url);

// Save to JSON file
file_put_contents('instagram_data.json', json_encode($data, JSON_PRETTY_PRINT));

echo "\nData scraped and saved to instagram_data.json\n";
