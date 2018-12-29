<?php

    require_once(__DIR__ . "/../libs/EcoVacsModule.php");

    class Configurator extends IPSModule 
    {
        // IPS functions needed for the Module:
        
        public function Create(){
            //Never delete this line!
            parent::Create();
        }
        
        public function ApplyChanges(){
			//Never delete this line!
			parent::ApplyChanges();	
            $this->RegisterVariableString ("AccountInfo", "AccountInfo","",0);
		}
    }

?>