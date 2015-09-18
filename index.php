<?php

header('Content-type: application/xml');

require 'bootstrap.php';


$app = new \Slim\Slim();
$app->get("/test", function() {
   
    $ff = new \Module\FetchFeed(array("URL" => "http://www.descouting.nl/index.php/verkenner-ledenportaal-introductie/verkennerbrieven?format=feed&type=rss"));
    echo $ff->out()->asXML();
    
    
});
$app->run();