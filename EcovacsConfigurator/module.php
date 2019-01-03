<?php

require_once(__DIR__ . "/../libs/EcovacsModule.php");

class EcovacsConfigurator extends IPSModule
{
    // IPS functions needed for the Module:
    public function Create() {
        parent::Create(); //Never delete this line!
        
        $this->ConnectParent("{8EB4291C-8EC8-4E10-B5D7-1F90CC37BD8D}");
    }
        
    public function ApplyChanges() {
		parent::ApplyChanges();	//Never delete this line!
        
	}
    
    public function GetConfigurationForm() {  
        include('form.php');
        return $form;
    }
}
    
?>