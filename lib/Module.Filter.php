<?php
// /lib/Module.Filter.php

/**
 * 
 */

namespace Module;

class Filter implements \Module\Module {
    
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
        foreach ($entries as $entryKey => $entry) {
            if ($this->conf->mode == "permit") {
                if ($this->matchesCombinedCondition($entry, $this->conf->rules, $this->conf->combine)) {
                    array_push($filteredEntries, $entry);
                }
            } elseif ($this->conf->mode == "deny") {
                if (!$this->matchesCombinedCondition($entry, $this->conf->rules, $this->conf->combine)) {
                    array_push($filteredEntries, $entry);
                }
            } else {
                throw new \Exception("Mode $this->conf->mode not accepted");
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
    
    private function matchesCombinedCondition($entry, $rules, $combine) {
        if ($combine == "and") {
            foreach ($rules as $rule) {
                if (!$this->matchesSingleCondition($entry, $rule)) {
                    return false;
                }
            }
            return true;
        } elseif ($combine == "or") {
            foreach ($rules as $rule) {
                if ($this->matchesSingleCondition($entry, $rule)) {
                    return true;
                }
            }
            return false;
        } else {
            throw new \Exception("Combination $combine is not implemented");
        }
    }
    
    private function matchesSingleCondition($entry, $rule) {
        $entryValue = $entry->{$rule->field};
        switch ($rule->op) {
            case "after":
                return strtotime($entryValue) > strtotime($rule->value);
                break;
            case "before":
                return strtotime($entryValue) < strtotime($rule->value);
                break;
            default:
                throw new \Exception("Operation $rule->op not implemented");
        }
    }
}
