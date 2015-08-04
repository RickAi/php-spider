<?php

use VDB\Spider\Discoverer\XPathExpressionDiscoverer;
use VDB\Spider\Spider;

require_once __DIR__ . '/../vendor/autoload.php';

// Create Spider
$spider = new Spider('http://www.dbmeinv.com/');

// Add a URI discoverer. Without it, the spider does nothing. In this case, we want <a> tags from a certain <div>
$spider->addDiscoverer(new XPathExpressionDiscoverer("//ul[@class='nav nav-pills']/li/a"));

// Set some sane options for this example. In this case, we only get the first 10 items from the start page.
$spider->setMaxDepth(5);
$spider->setMaxQueueSize(10);

// Execute crawl
$spider->crawl();

// Report
$stats = $spider->getStatsHandler();
echo "\nSPIDER ID: " . $stats->getSpiderId();
echo "\n  ENQUEUED:  " . count($stats->getQueued());
echo "\n  SKIPPED:   " . count($stats->getFiltered());
echo "\n  FAILED:    " . count($stats->getFailed());

// Finally we could do some processing on the downloaded resources
// In this example, we will echo the title of all resources
echo "\n\nDOWNLOADED RESOURCES: ";
foreach ($spider->getPersistenceHandler() as $resource) {
    $datas = $resource->getCrawler()
            ->filterXpath("//li[@class='span3']/div[@class='thumbnail']/div[@class='img_single']/a/img")
            ->each(function($node, $i){
                return $node->attr('src');
            });

    foreach ($datas as $data) {
        echo $data."\n";
    }

}
