<?php
// /lib/Module.RegEx.php

/**
 * 
 */

namespace Module;

class RegEx implements \Module\Module {
    
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
            //echo $entry->asXML(); die;
            foreach ($this->options["rules"] as $rule) {
                $field = $rule["field"];
                // TODO check if field actually exists
                $pattern = $rule["match"];
                $replace = $rule["replace"];
                
                // set flags
                $flag = "";
                if (isset($rule["multilinematch"])) {
                    if ($rule["multilinematch"] == true) {
                        $flag .= "m";
                    }
                }
                                
                //throw new \Exception("/".str_replace("/", "\/", $pattern)."/");
                //$entries[$entryKey]->{$field} = preg_replace("/".preg_quote($pattern, "/")."/", $replace, $entries[$entryKey]->{$field});
                $entries[$entryKey]->{$field} = preg_replace("/".str_replace("/", "\/", $pattern)."/".$flag, $replace, $entries[$entryKey]->{$field});
            }
            //throw new \Exception($entries[$entryKey]->{$field});
            
        }
        
        return $xml;
    }
}
