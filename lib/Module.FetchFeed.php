<?php
// /lib/Module.FetchFeed.php

/**
 * 
 */

namespace Module;

class FetchFeed implements \Module\Module {
    
    private $input;
    private $options = array(
            "URL" => ""
    );
    
    public function __construct($opt) {
        $this->options = $opt;
    }
    
    public function in(SimpleXMLElement $in) {
        throw new \Exception("Input is not yet supported for FetchFeed Module");
    }
    
    public function out() {
        if (($xml = simplexml_load_string(file_get_contents($this->options["URL"]))) === FALSE) {
            throw new \Exception("Unable to load Feed");
        }
        return $xml;
    }
}
