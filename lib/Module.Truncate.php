<?php
// /lib/Module.Truncate.php

/**
 * 
 */

namespace Module;

class Truncate implements \Module\Module {
    
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
    
    public function getId() {
        return $this->id;
    }
    
    /**
     * 
     */
    public function in(\SimpleXMLElement $in) {
        $this->input = $in;
    }
    
    /**
     * 
     */
    public function out() {
        $xml = $this->input;
        $entries = $xml->xpath("//a:entry");
        $filteredEntries = array();
        $cnt = 0;
        foreach ($entries as $entryKey => $entry) {
            
            if($cnt < $this->conf->count) {
                array_push($filteredEntries, $entry);
                $cnt++;
            }
        }
        
        // delete all entries from $xml
        $xml = $this->filterEntries($xml, $filteredEntries);
        
        return $xml;
    }
    
    private function filterEntries($xml, $filteredEntries) {
        // create DOMDocument
        $dom = new \DOMDocument();
        $dom->loadXML($xml->asXML());  
        $entries = $dom->getElementsByTagName('entry');
        
        // remove all entries
        while ($entries->length > 0) {
            $entry = $entries->item(0);
            $entry->parentNode->removeChild($entry);
        }
        
        // insert back all filtered entries
        $parent = $dom->getElementsByTagName("feed");
        foreach ($filteredEntries as $entry) {
            $entry = $dom->importNode(dom_import_simplexml($entry), true);
            $parent->item(0)->insertBefore($entry);
        }
        
        
        
        // convert back to SimpleXML format        
        $xml = simplexml_load_string($dom->saveXML());
        foreach($xml->getDocNamespaces() as $strPrefix => $strNamespace) {
            if(strlen($strPrefix) == 0) {
                $strPrefix = "a"; // assign an arbitrary namespace prefix
            }
            $xml->registerXPathNamespace($strPrefix, $strNamespace);
        }
        return $xml;
    }
}
