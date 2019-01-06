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
    
    public function __construct($InstanzID) {
        parent::__construct($InstanzID); //Never delete this line!       
    }
    
    public function CreateRobotInstance(string $devices) { 
        $devices = json_decode($devices, true);

        if (($devices['InstanceID'] > 0)) {
            echo 'Instance already created';
            return;
        }
        
        $InstanceID = @IPS_CreateInstance('{071BCBF7-66BA-4341-8258-A8BED6F1000C}');
        
        if ($InstanceID >0) {
            if (IPS_GetInstance($InstanceID)['ConnectionID'] != IPS_GetInstance($id)['ConnectionID']) {
                if (IPS_GetInstance($InstanceID)['ConnectionID'] > 0) {
                    IPS_DisconnectInstance($InstanceID);
                }
                IPS_ConnectInstance($InstanceID, IPS_GetInstance($id)['ConnectionID']);
            }
            @IPS_SetProperty($InstanceID, 'RobotSerialNr', $devices['RobotSerialNr']);
            @IPS_ApplyChanges($InstanceID);
            if (strlen($devices['Name']) < 1) { //check if name given
                $devices['Name'] = 'Nr '.$devices['RobotNr'];
                SetRobotInfo($deices);
            }
            IPS_SetName($InstanceID,  'Deebot '. $devices['Name']);
            echo 'Instance created with ID '. $InstanceID;
        } else {
            echo 'Failed to create instance';
        }
    }

    public function SetRobotInfo(string $devices){
        $devices = json_decode($devices, true);
        $SplitterID     = IPS_GetInstance($this->InstanceID)['ConnectionID'];
        $RobotsDataID   = IPS_GetObjectIDByIdent("XMPP_Robots",$SplitterID);
        SetValue($RobotsDataID,json_encode($devices));
    }
    
    private function GetRobotInfo() {
        $SplitterID     = IPS_GetInstance($this->InstanceID)['ConnectionID'];
        $RobotsDataID   = IPS_GetObjectIDByIdent("XMPP_Robots",$SplitterID);
        $RobotsData     = GetValue($RobotsDataID);

        if ((strlen($RobotsData) > 2)) {
            $values = substr($RobotsData, 1, -1); // remove first [ and last ], created by the json_encode()
        } else {
            $values = '';
        }

        return ',
                    "values": [' .$values. ']';
    }
    
    public function GetConfigurationForm() {
        include('form.php');
        return $form;
    }
    
}
    
?>