<?php
// /lib/Module.RegEx.php

/**
 * This PHP class should only read an iCal file (*.ics), parse it and return an
 * array with its content.
 *
 * PHP Version 5
 * 
 * @author   Geert Derks <g.m.w.derks@home.nl>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     https://github.com/gafderks/pipes2php
 */

namespace Module;

/**
 * \Module\RegEx
 * 
 * Implements \Module\Module.
 * 
 * This module performs replaces on fields in entries with regular expressions.
 * 
 * Configuration:
 *  - rules: [
 *      (string)  field
 *      (string)  match
 *      (string)  replace
 *      (boolean) globalmatch
 *      (boolean) multilinematch
 *    ]
 */

class RegEx implements \Module\Module {
    
    private $id;
    private $input;
    private $conf;
    
    /**
     * Constructor. Sets the module id and the configuration.
     * 
     * @param integer id   ID of the module
     * @param object  conf configuration of the module
     */
    public function __construct($id, $conf) {
        $this->id = $id;
        $this->conf = $conf;
    }
    
    /**
     * Returns the id of the module.
     * 
     * @returns id of the module
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Sets the input of the module.
     * 
     * @param \SimpleXMLElement in input
     */
    public function in(\SimpleXMLElement $in) {
        $this->input = $in;
    }
    
    /**
     * Executes and returns the module with the current configuration and input.
     * 
     * @return \SimpleXMLElement Resulting XML after execution of the module
     */
    public function out() {
        // start off with current input  
        $xml = $this->input;
        // find entries
        $entries = $xml->xpath("//a:entry");
        
        // apply all rules on all entries
        foreach ($entries as $entryKey => $entry) {
            foreach ($this->conf->rules as $rule) {
                $field = $rule->field;
                $pattern = $rule->match;
                $replace = $rule->replace;

                // check if all fields are defined
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
                
                // perform actual replace
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
