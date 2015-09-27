<?php
// /lib/Common.TopologicalSort.php

/**
 * Class for sorting modules topologically such that a legal execution order can be obtained.
 */

namespace Common;

class TopologicalSort {
    
    private $wiresDefinition;
    private $modules;
    
    /**
     * Constructor for TopologicalSort.
     * 
     * @param array $wiresDefinition Definition of the wires in the pipe
     * @param array \Module\Module[] Array of modules in the pipe
     */
    public function __construct($wiresDefinition, $modules) {
        $this->wiresDefinition = $wiresDefinition;
        $this->modules = $modules;
    }
    
    /**
     * Returns an array of modules in a legal execution order.
     * 
     * Pseudocode from https://en.wikipedia.org/wiki/Topological_sorting#Algorithms
     * 
     * @return array \Module\Module[] Array of modules in a legal execution order.
     */
    public function sort() {
        $sorted = array();
        $noIncoming = $this->noIncoming($this->modules);
        
        while (count($noIncoming) > 0) {
            // remove a node n from noIncoming
            $n = array_shift($noIncoming);
            // add n to tail of sorted
            array_push($sorted, $n);
            // for each node m with an edge e from n to m
            foreach ($this->mapsToX($n, $this->modules) as $m) {
                // remove edge e from the graph
                $this->removeWire($n, $m);
                // if m has no other incoming edges
                if ($this->countIncoming($m) == 0) {
                    // insert m into noIncoming
                    array_push($noIncoming, $m);
                }
            }
        }
        // if graph has edges
        if (count($this->wiresDefinition) > 0) {
            throw new \Exception("Pipe contains cycle");
        }
        
        return $sorted;       
    }
    
    /**
     * Removes a wire from the array of wire definitions.
     * 
     * @param \Module\Module $from Source module of the wire
     * @param \Module\Module $to   Target module of the wire
     */
    private function removeWire($from, $to) {
        if (!$this->mapsTo($from, $to)) {
            throw new \Exception("Wire does not exist");
        }
        $fromId = $from->getId();
        $toId = $to->getId();
        foreach ($this->wiresDefinition as $wireKey => $wire) {
            if ($wire->src->module == $fromId && $wire->tgt->module == $toId) {
                unset($this->wiresDefinition[$wireKey]); // remove wire at index $wireKey
                $this->wiresDefinition = array_values($this->wiresDefinition); // 'reindex' array
            }
        }
    }
    
    /**
     * Returns an array of modules that the specified module is image of.
     * 
     * @param  \Module\Module   $to Target module
     * @param  \Module\Module[] $X  Search pool for source modules
     * @return \Module\Module[] Array of modules which are source for $to
     */
    private function XmapsTo($to, $X) {
        $from = array();
        foreach ($X as $module) {
            if (mapsTo($module, $to)) {
                array_push($from, $module);
            }
        }
        return $from;
    }
    
    /**
     * Returns an array of modules that the specified module maps to.
     * 
     * @param  \Module\Module   $from Source module
     * @param  \Module\Module[] $X    Search pool for target modules
     * @return \Module\Module[] Array of modules which are target to $from
     */
    private function mapsToX($from, $X) {
        $to = array();
        foreach ($X as $module) {
            if ($this->mapsTo($from, $module)) {
                array_push($to, $module);
            }
        }
        return $to;
    }
    
    /**
     * Returns whether a specified module has a directed wire to another specified module.
     * 
     * @param  \Module\Module $from Source module
     * @param  \Module\Module $to   Target module
     * @return boolean        whether the wire exists
     */
    private function mapsTo($from, $to) {
        $mapsTo = false;
        $fromId = $from->getId();
        $toId = $to->getId();
        foreach ($this->wiresDefinition as $wire) {
            if ($wire->src->module == $fromId && $wire->tgt->module == $toId) {
                $mapsTo = true;
                break;
            }
        }
        return $mapsTo;
    }
    
    /**
     * Returns an array with the modules from the input which have no incoming wires.
     * 
     * @param  array \Module\Module[] Modules to check
     * @return array Modules with no incoming wires
     */
    private function noIncoming($modules) {
        $noIncoming = array();
        foreach ($modules as $module) {
            if ($this->countIncoming($module) == 0) {
                array_push($noIncoming, $module);
            }
        }
        return $noIncoming;
    }
    
    /**
     * Returns the number of incoming wires for the specified module.
     * 
     * @param  \Module\Module $module Subject module
     * @return integer        Number of incoming wires
     */
    private function countIncoming($module) {
        $incoming = 0;
        foreach ($this->wiresDefinition as $wire) {
            if ($wire->tgt->module == $module->getId()) {
                $incoming++;
            }
        }
        return $incoming;
    }
    
    /**
     * Returns the number of outgoing wires for the specified module.
     * 
     * @param  \Module\Module $module Subject module
     * @return integer        Number of outgoing wires
     */
    private function countOutgoing($module) {
        $outgoing = 0;
        foreach ($this->wiresDefinition as $wire) {
            if ($wire->src->module == $module->getId()) {
                $outgoing++;
            }
        }
        return $outgoing;
    }
}
