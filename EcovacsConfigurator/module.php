<?php

    require_once(__DIR__ . "/../libs/EcoVacsModule.php");

    class EcovacsSplitter extends IPSModule 
    {
        use EcovacsHTTP;
        
        // IPS functions needed for the Module:
        public function Create(){
            parent::Create(); //Never delete this line!
        }
        
        public function ApplyChanges(){
			parent::ApplyChanges();	//Never delete this line!
            
            $this->RegisterVariableString ("AccountInfo", "AccountInfo");
		}
        
    }

?>