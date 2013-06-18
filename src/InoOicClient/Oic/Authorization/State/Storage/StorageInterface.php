<?php

namespace InoOicClient\Oic\Authorization\State\Storage;

use InoOicClient\Oic\Authorization\State\State;


interface StorageInterface
{


    /**
     * Saves the state to the storage.
     * 
     * @param State $state
     */
    public function saveState(State $state);


    /**
     * Returns the previously saved state or null if no state has been saved.
     * 
     * @return State
     */
    public function loadState();
}

