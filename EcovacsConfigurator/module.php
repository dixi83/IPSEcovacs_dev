<?php

require_once(__DIR__ . "/../libs/EcovacsModule.php");

class EcovacsConfigurator extends IPSModule
{
    // IPS functions needed for the Module:
    public function Create() {
        parent::Create(); //Never delete this line!
        
        $this->ConnectParent("{8EB4291C-8EC8-4E10-B5D7-1F90CC37BD8D}"); // Ecovacs Splitter
        
        $this->RegisterVariableInteger("SplitterID", "SplitterID");
    }
        
    public function ApplyChanges() {
		parent::ApplyChanges();	//Never delete this line!
        
        
	}
    
    public function ReceiveData($JSONString) {
        $data = json_decode($JSONString,true);
        $this->SetValue("SplitterID",$data['SplitterID']);
    }
    
    public function GetConfigurationForm() {  
        include('form.php');
        return $form;
    }
    
    public function FindParentSplitter() {
        $ids = IPS_GetInstanceListByModuleID("{8EB4291C-8EC8-4E10-B5D7-1F90CC37BD8D}");
        
        if(sizeof($ids) > 0) {
            $hooks = json_decode(IPS_GetProperty($ids[0], "Hooks"), true);
            $found = false;
            foreach($ids as $index => $id) {
                if($hook['Hook'] == $WebHook) {
                    if($hook['TargetID'] == $this->InstanceID)
                        return;
                    $hooks[$index]['TargetID'] = $this->InstanceID;
                    $found = true;
                }
            }
        } else {
            return false;
        }
    }
}
    
?>