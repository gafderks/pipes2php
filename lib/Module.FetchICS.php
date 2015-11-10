<?php
// /lib/Module.FetchICS.php

/**
 *
 */

namespace Module;

class FetchICS implements \Module\Module {
    
    private $id;
    private $input;
    private $conf;
    
    /**
     * 
     */
    public function __construct($id, $conf) {
        $this->id = $id;
        $this->conf = $conf;
    }
    
    /**
     * 
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * 
     */
    public function in(\SimpleXMLElement $in) {
        throw new \Exception("Input is not yet supported for FetchICS Module");
    }
    
    /**
     * 
     */
    public function out() {
        // load file
        if (($file = file_get_contents($this->conf->URL)) === FALSE) {
            throw new \Exception("URL is not reachable");
        }
        
        // load xml template
        if (($xml = simplexml_load_string($this->getXMLTemplate())) === FALSE) {
            throw new \Exception("Unable to load template");
        }
        
        foreach($xml->getDocNamespaces() as $strPrefix => $strNamespace) {
            if(strlen($strPrefix) == 0) {
                $strPrefix = "a"; // assign an arbitrary namespace prefix
            }
            $xml->registerXPathNamespace($strPrefix, $strNamespace);
        }
        
        $ical = new \Common\ICal($this->conf->URL);
        $events = $ical->events();
        
        
        // insert events into template
        foreach ($events as $event) {
            $entry = $xml->addChild("entry");
            $entry->addChild("published", date("c", strtotime(@$event['CREATED'])));
            $entry->addChild("updated", date("c", strtotime(@$event['LAST-MODIFIED'])));
            $entry->addChild("start", date("c", strtotime(@$event['DTSTART'])));
            $entry->addChild("end", date("c", strtotime(@$event['DTEND'])));
            $entry->addChild("title", @$event['SUMMARY']);
            $entry->addChild("content", $this->getFriendlyDescription(strtotime($event['DTSTART']), strtotime($event['DTEND']), @$event['LOCATION']));
            $entry->addChild("summary", $this->getFriendlyDescription(strtotime($event['DTSTART']), strtotime($event['DTEND']), @$event['LOCATION']));
        }
        
        return $xml;
    }
    
    private function getFriendlyDescription($start, $end, $location) {
        $friendly;
        $startDay = date("D j M Y", $start);
        $endDay = date("D j M Y", $end);
        if ($startDay == $endDay) {
            $friendly = date("D j M Y G:i T", $start)." - ".date("G:i T", $end);
        } else {
            $friendly = date("D j M Y G:i T", $start)." - ".date("D j M Y G:i T", $end);
        }
        
        $search  = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        $replace = array_merge($this->conf->days, $this->conf->months);
        $friendly = str_replace($search, $replace, $friendly);
        
        return $friendly." ".$location;
    }
    
    private function getXMLTemplate() {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:openSearch="http://a9.com/-/spec/opensearchrss/1.0/" xmlns:gCal="http://schemas.google.com/gCal/2005" xmlns:gd="http://schemas.google.com/g/2005">
  <id>http://www.google.com/calendar/feeds/im9dkimg71lmvd5iu0461fkkcs%40group.calendar.google.com/public/basic</id>
  <updated>2015-11-04T03:49:23.000Z</updated>
  <category scheme="http://schemas.google.com/g/2005#kind" term="http://schemas.google.com/g/2005#event"/>
  <title type="text">{$this->conf->title}</title>
  <subtitle type="text"/>
  <link rel="alternate" type="text/html" href="https://www.google.com/calendar/embed?src=im9dkimg71lmvd5iu0461fkkcs%40group.calendar.google.com"/>
  <link rel="http://schemas.google.com/g/2005#feed" type="application/atom+xml" href="https://www.google.com/calendar/feeds/im9dkimg71lmvd5iu0461fkkcs%40group.calendar.google.com/public/basic"/>
  <link rel="http://schemas.google.com/g/2005#batch" type="application/atom+xml" href="https://www.google.com/calendar/feeds/im9dkimg71lmvd5iu0461fkkcs%40group.calendar.google.com/public/basic/batch"/>
  <link rel="self" type="application/atom+xml" href="https://www.google.com/calendar/feeds/im9dkimg71lmvd5iu0461fkkcs%40group.calendar.google.com/public/basic?max-results=25&amp;futureevents=true&amp;orderby=starttime&amp;singleevents=true&amp;sortorder=a"/>
  <author>
    <name>agenda@descouting.nl</name>
    <email>agenda@descouting.nl</email>
  </author>
  <generator version="1.0" uri="http://www.google.com/calendar">Google Calendar</generator>
  <openSearch:totalResults>13</openSearch:totalResults>
  <openSearch:startIndex>1</openSearch:startIndex>
  <openSearch:itemsPerPage>25</openSearch:itemsPerPage>
  <gCal:timezone value="Europe/Amsterdam"/>
  <gCal:timesCleaned value="0"/>
  <gd:where valueString="Dongen, Nederland"/>

</feed>
XML;
    }
}
