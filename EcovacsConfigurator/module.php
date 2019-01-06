<?php

require_once(__DIR__ . "/../libs/EcovacsModule.php");

class EcovacsConfigurator extends IPSModule
{
    // IPS functions needed for the Module:
    public function Create() {
        parent::Create(); //Never delete this line!
        
        $this->ConnectParent("{8EB4291C-8EC8-4E10-B5D7-1F90CC37BD8D}"); // Ecovacs Splitter
    }
        
    public function ApplyChanges() {
		parent::ApplyChanges();	//Never delete this line!
          
	}
    
    public function __construct($InstanzID) {
        parent::__construct($InstanzID); //Never delete this line!       
    }
    
    public function CreateRobotInstance(string $device) { 
        $device = json_decode($device, true);

        if (($device['InstanceID'] > 0)) {
            echo 'Instance already created';
            return;
        }
        
        $InstanceID = @IPS_CreateInstance('{071BCBF7-66BA-4341-8258-A8BED6F1000C}');
        
        if (strlen($device['Name']) < 1) { //check if name given
            $device['Name'] = 'Nr '.$device['RobotNr'];
        }
        $device['InstanceID'] = $InstanceID;
        
        if ($InstanceID > 0) {
            if (!SetRobotInfo($deice)) { // save "unsaved" name and check if device excists, a double check in case of a user changed 'XMPP_Robots'
                echo 'Failed to create device doesn\'t excist. Please refresh the XMPP info in the Splitter Module' ;
                return;
            } else {
                if (IPS_GetInstance($InstanceID)['ConnectionID'] != IPS_GetInstance($this->InstanceID)['ConnectionID']) {
                    if (IPS_GetInstance($InstanceID)['ConnectionID'] > 0) {
                        IPS_DisconnectInstance($InstanceID);
                    }
                    IPS_ConnectInstance($InstanceID, IPS_GetInstance($this->InstanceID)['ConnectionID']);
                }
                @IPS_SetProperty($InstanceID, 'RobotSerialNr', $device['RobotSerialNr']);
                @IPS_ApplyChanges($InstanceID);
                IPS_SetName($InstanceID,  'Deebot '. $device['Name']);
                echo 'Instance created with ID '. $InstanceID;
            }
        } else {
            echo 'Failed to create instance';
        }
    }

    public function SetRobotInfo(string $device){
        
        $device = json_decode($device, true);
        
        $SplitterID     = IPS_GetInstance($this->InstanceID)['ConnectionID'];
        $RobotsDataID   = IPS_GetObjectIDByIdent('XMPP_Robots', $SplitterID);
        $devices        = json_decode(GetValue($RobotsDataID), true);
        
        $deviceFound = false;
        $i = 0;
        
        foreach ($devices as $value) {
            if ($device['RobotSerialNr'] == $value['RobotSerialNr']){ // search for DeviceSerialNr
                $devices[$i]['Name']        = $device['Name'];
                $devices[$i]['InstanceID']  = $device['InstanceID'];
                $deviceFound = true;
            }
            $i++;
        }
        
        if (!$deviceFound) {
            return false;
        }
        
        SetValue($RobotsDataID,json_encode($devices));
        return true;
    }
    
    private function GetRobotInfo() {
        $SplitterID     = IPS_GetInstance($this->InstanceID)['ConnectionID'];
        $RobotsDataID   = IPS_GetObjectIDByIdent('XMPP_Robots',$SplitterID);
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