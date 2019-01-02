<?php

require_once(__DIR__ . "/../libs/EcovacsModule.php");

class EcovacsConfigurator extends IPSModule
{
    // IPS functions needed for the Module:
    public function Create() {
        parent::Create(); //Never delete this line!
        
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