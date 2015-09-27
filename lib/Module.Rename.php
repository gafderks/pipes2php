<?php
// /lib/Module.Rename.php

/**
 * 
 */

namespace Module;

class Rename implements \Module\Module {
    
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
        $this->input = $in;
    }
    
    /**
     * 
     */
    public function out() {
        $xml = $this->input;
        $entries = $xml->xpath("//a:entry");
        foreach ($entries as $entryKey => $entry) {
            foreach ($this->conf->rules as $rule) {
                $field = $rule->field;
                $newValue = $rule->newval;
                $operation = $rule->op;
                
                if (!$entry->{$newValue}) {
                    throw new \Exception("Field $newValue was not found");
                }
                
                if ($operation == "copy") {
                    if (!$entry->{$field}) {
                        // create node if it does not yet exist
                        $entries[$entryKey]->addChild($field, "");
                    }
                    $entries[$entryKey]->{$field} = (string) $entry->{$newValue};
                }
            }
        }
        
        return $xml;
    }
}
