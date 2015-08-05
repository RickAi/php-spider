<?php
/**
 * Created by PhpStorm.
 * User: CIR
 * Date: 8/5/15
 * Time: 09:42
 */

use Example\DBManager;
use VDB\Spider\Discoverer\XPathExpressionDiscoverer;
use VDB\Spider\Spider;

require_once('example_complex_bootstrap.php');

// init config
$db_manager = DBManager::getInstance();
$seed = 'https://www.desktoppr.co/wallpapers';
$spider = new Spider($seed);
$spider->setMaxDepth(5);
$spider->setMaxQueueSize(10);

// add discovers
$spider->addDiscoverer(new XPathExpressionDiscoverer("//div[@class='wallpaper wallpaper-small-wrap']/div[@class='image']/a"));
$spider->addDiscoverer(new XPathExpressionDiscoverer("//span[@class='page']/a"));

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