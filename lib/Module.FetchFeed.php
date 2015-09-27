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
    
    public function in(\SimpleXMLElement $in) {
        throw new \Exception("Input is not yet supported for FetchFeed Module");
    }
    
    public function out() {
        // Load file
        if (($file = file_get_contents($this->options["URL"])) === FALSE) {
            throw new \Exception("URL is not reachable");
        }
        
//        // Strip namespaces
//        $file = preg_replace("/xmlns:(\w*)=/", "ns:$1=", $file);
//        echo $file;
//        die;
//        
        // Load feed
        if (($xml = simplexml_load_string($file)) === FALSE) {
            throw new \Exception("Unable to load Feed");
        }
        
        foreach($xml->getDocNamespaces() as $strPrefix => $strNamespace) {
            if(strlen($strPrefix)==0) {
                $strPrefix="a"; //Assign an arbitrary namespace prefix.
            }
            $xml->registerXPathNamespace($strPrefix,$strNamespace);
        }
        
        return $xml;
    }
}
