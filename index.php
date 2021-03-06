<?php // /index.php

require("bootstrap.php");

/**
 * 
 */
define("SCRIPT_DIR", __DIR__ .  DIRECTORY_SEPARATOR . "pipes" . DIRECTORY_SEPARATOR);

$app = new \Slim\Slim();

/**
 * 
 */
$app->get("/:pipe+", function($pipe) use ($app) {
    
    // escape pipe name
    $pipeNameArray = preg_replace("/[^A-Za-z0-9_\-]/", "_", $pipe);
    
    $pipeName = implode(DIRECTORY_SEPARATOR, $pipeNameArray);
    
    // check if pipe definition exists
    if (!file_exists(SCRIPT_DIR . $pipeName . ".json")) {
        // show 404
        $app->notFound();
        die();
    }
    // define content-type
    $app->response->headers->set('Content-Type', 'application/xml');
    
    // retrieve definition
    $pipeDefinition = json_decode(file_get_contents(SCRIPT_DIR . $pipeName . ".json"));
    
    // create pipe evaluator
    $ev = new \Common\Evaluator($pipeDefinition);
    
    // run evaluator
    $result = $ev->evaluate();
    
    // format result
    $dom = new DOMDocument("1.0");
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($result->asXML());
        
    // output
    echo $dom->saveXML();
    
});
$app->run();