<?php
// /lib/Module.RegEx.php

/**
 * 
 */

namespace Module;

class RegEx implements \Module\Module {
    
    private $input;
    private $options;
    
    public function __construct($opt) {
        $options = $opt;
    }
    
    public function in(SimpleXMLElement $in) {
        $input = $in;
    }
    
    public function out() {
        // return output
    }
}
