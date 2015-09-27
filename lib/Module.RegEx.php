<?php
// /lib/Module.RegEx.php

/**
 * 
 */

namespace Module;

class RegEx implements \Module\Module {
    
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
                $pattern = $rule->match;
                $replace = $rule->replace;
                
                if (!$entry->{$field}) {
                    throw new \Exception("Field $field was not found");
                }
                
                // set flags
                $flag = "";
                if (isset($rule->multilinematch)) {
                    if ($rule->multilinematch == true) {
                        $flag .= "m";
                    }
                }
                
                $entries[$entryKey]->{$field} 
                    = preg_replace("/" . str_replace("/", "\/", $pattern) . "/" . $flag, 
                                   $replace, 
                                   $entries[$entryKey]->{$field}
                                  );
            }
                        
        }
        
        return $xml;
    }
}
