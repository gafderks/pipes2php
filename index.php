<?php

header('Content-type: application/xml');

require 'bootstrap.php';


$app = new \Slim\Slim();
$app->get("/test", function() {
   
    $ff = new \Module\FetchFeed(array("URL" => "https://www.google.com/calendar/feeds/im9dkimg71lmvd5iu0461fkkcs%40group.calendar.google.com/public/basic?futureevents=true&orderby=starttime&sortorder=a&language=nl_NL&country=NL&singleevents=true&hl=nl"));
    
    $rn = new \Module\Rename(
        array("rules" =>
            array(
                array(
                    "field" => "description",
                    "op" => "copy",
                    "newval" => "summary"
                )
            )
        )
    );
    $rn->in($ff->out());
    
    $re = new \Module\RegEx(
        array("rules" => 
            array(
                array(
                    "field" => "description",
                    "match" => "Status van afspraak: .*",
                    "replace" => ""
                ),
                array(
                    "field" => "description",
                    "match" => "Wie: .*",
                    "replace" => ""
                ),
                array(
                    "field" => "description",
                    "match" => "Wanneer: ",
                    "replace" => ""
                ),
                array(
                    "field" => "description",
                    "match" => "Waar:",
                    "replace" => ""
                ),
                array(
                    "field" => "description",
                    "match" => "Beschrijving van afspraak: .*",
                    "replace" => ""
                ),
                array(
                    "field" => "description",
                    "match" => "\n",
                    "replace" => "",
                    "globalmatch" => true,
                    "multilinematch" => true
                ),
                array(
                    "field" => "description",
                    "match" => "\r",
                    "replace" => "",
                    "globalmatch" => true,
                    "multilinematch" => true
                ),
                array(
                    "field" => "description",
                    "match" => "CET",
                    "replace" => "",
                    "globalmatch" => true
                ),
                array(
                    "field" => "description",
                    "match" => "(\d)\.",
                    "replace" => "$1",
                    "globalmatch" => true
                ),
                array(
                    "field" => "title",
                    "match" => "Verk: ",
                    "replace" => ""
                ),
                array(
                    "field" => "description",
                    "match" => "CEST",
                    "replace" => "",
                    "globalmatch" => true
                ),
                array(
                    "field" => "description",
                    "match" => "<br>",
                    "replace" => "",
                    "globalmatch" => true,
                    "multilinematch" => true
                ),
                array(
                    "field" => "description",
                    "match" => "&nbsp;",
                    "replace" => "",
                    "globalmatch" => true
                )
            )
        )
    );
    $re->in($rn->out());
    
    echo $re->out()->asXML();
    
    
});
$app->run();