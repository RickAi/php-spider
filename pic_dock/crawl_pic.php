<?php
/**
 * Created by PhpStorm.
 * User: CIR
 * Date: 8/5/15
 * Time: 09:42
 */

use Example\DBManager;
use VDB\Spider\Discoverer\XPathExpressionDiscoverer;
use VDB\Spider\Event\SpiderEvents;
use VDB\Spider\EventListener\PolitenessPolicyListener;
use VDB\Spider\Spider;

require_once('example_complex_bootstrap.php');

// generate random integer for page number
$page_number = rand(1, 3500);
$page_str = "/page/".$page_number;

// init config
$db_manager = DBManager::getInstance();
$seed = 'https://www.desktoppr.co/wallpapers'.$page_str;
echo "start curl page:".$seed;

$spider = new Spider($seed);
$spider->setMaxDepth(5);
$spider->setMaxQueueSize(10);

// add discovers
$spider->addDiscoverer(new XPathExpressionDiscoverer("//div[@class='wallpaper wallpaper-small-wrap']/div[@class='image']/a"));
$spider->addDiscoverer(new XPathExpressionDiscoverer("//span[@class='page']/a"));

// spider config
$spider->setTraversalAlgorithm(Spider::ALGORITHM_BREADTH_FIRST);
// We add an eventlistener to the crawler that implements a politeness policy. We wait 450ms between every request to the same domain
$politenessPolicyEventListener = new PolitenessPolicyListener(450);
$spider->getDispatcher()->addListener(
    SpiderEvents::SPIDER_CRAWL_PRE_REQUEST,
    array($politenessPolicyEventListener, 'onCrawlPreRequest')
);

// begin to crawl
$spider->crawl();

// processing on the downloaded resources
echo "\n\nDOWNLOADED RESOURCES: ";
foreach ($spider->getPersistenceHandler() as $resource) {
    $datas = $resource->getCrawler()
        ->filterXpath("//div[@class='wallpaper wallpaper-large-wrap']/div[@class='preview']/div[@class='image']/img")
        ->each(function($node, $i){
            return $node->attr('src');
        });

    foreach ($datas as $data) {
        $is_success = $db_manager->insertPic($data, new DateTime());
        echo $data."\n";
    }
}

// finish
$db_manager->closeDB();
