<?php
class Loader  
{
    public function Load($lib){
        require_once getcwd() . "/lib/$lib.php";
    }

    public function LoadMuliple($libs){
        foreach ($libs as $lib) {
            $this->Load($lib); 
        }
    }
}
