<?php
// /lib/Module.FetchFeed.php

/**
 * 
 */

namespace Module;

class FetchFeed implements \Module\Module {
    
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
        throw new \Exception("Input is not yet supported for FetchFeed Module");
    }
    
    /**
     * 
     */
    public function out() {
        // load file
        if (($file = file_get_contents($this->conf->URL)) === FALSE) {
            throw new \Exception("URL is not reachable");
        }

        // load feed
        if (($xml = simplexml_load_string($file)) === FALSE) {
            throw new \Exception("Unable to load Feed");
        }
        
        foreach($xml->getDocNamespaces() as $strPrefix => $strNamespace) {
            if(strlen($strPrefix) == 0) {
                $strPrefix = "a"; // assign an arbitrary namespace prefix
            }
            $xml->registerXPathNamespace($strPrefix, $strNamespace);
        }
        
        return $xml;
    }
}
