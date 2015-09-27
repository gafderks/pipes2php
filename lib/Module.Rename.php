<?php
// /lib/Module.Rename.php

/**
 * 
 */

namespace Module;

class Rename implements \Module\Module {
    
    private $input;
    private $options = array();
    
    public function __construct($opt) {
        $this->options = $opt;
    }
    
    public function in(\SimpleXMLElement $in) {
        $this->input = $in;
    }
    
    public function out() {
        $xml = $this->input;
        $entries = $xml->xpath("//a:entry");
        foreach ($entries as $entryKey => $entry) {
            foreach ($this->options["rules"] as $rule) {
                $field = $rule["field"];
                $newValue = $rule["newval"];
                // TODO check if newval actually exists
                $operation = $rule["op"];
                
                if ($operation == "copy") {
                    $entries[$entryKey]->addChild($field, "");
                    $entries[$entryKey]->{$field} = (string) $entry->{$newValue};
                }
            }
        }
        
        return $xml;
    }
}
