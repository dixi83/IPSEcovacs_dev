<?php

require_once(__DIR__ . "/../libs/EcovacsModule.php");

class EcovacsDeebot extends IPSModule
{
    // IPS functions needed for the Module:
    public function Create() {
        parent::Create(); //Never delete this line!
        
        $this->RegisterPropertyBoolean("active", false);
        $this->RegisterPropertyString("robotSerialNr", "");
        $this->RegisterPropertyInteger("RefreshStatus", 0);
        $this->RegisterTimer("RefreshStatus", 0, "EVDB_RefreshStatus();");
        $this->RegisterTimer("RefreshBatery", 0, "EVDB_RefreshBatery();");
    }
        
    public function ApplyChanges() {
		parent::ApplyChanges();	//Never delete this line!
        
        $this->RegisterVariableString ("DeebotStatus", "DeebotStatus"); 
        $this->RegisterVariableString ("DeebotBatery", "DeebotBattery");
        
        if($this->ReadPropertyBoolean("active")) {
            $this->SetStatus(102); // activated
            //$this->SetTimerInterval("RefreshBatery", (1000 * 150)); // 2,5 minutes 
            //$this->SetTimerInterval("RefreshStatus", (1000 * $this->ReadPropertyInteger));
        } else {
            $this->SetStatus(104); // inactive
            $this->SetTimerInterval("RefreshBatery", 0);
            $this->SetTimerInterval("RefreshStatus", 0);
        }
	}
    
    protected function SetCommand($command) {        
        $EvovacsXMPP    = new EcovacsXMPP($this->InstanceID);
        $robotSerialNr  = $this->ReadPropertyString("robotSerialNr");
        
        try {
            $EvovacsXMPP->XMPPsetCommand($robotSerialNr,$command);
        } catch(Exception $error) {
            IPS_LogMessage("Ecovacs Deebot", "XMPP: ".$error);
        }
    }
    
    protected function GetCommand($command) {
        $EvovacsXMPP    = new EcovacsXMPP($this->InstanceID);
        $robotSerialNr  = $this->ReadPropertyString("robotSerialNr");
        
        try {
            $EvovacsXMPP->XMPPgetCommand($robotSerialNr,$command);
        } catch(Exception $error) {
            IPS_LogMessage("Ecovacs Deebot", "XMPP: ".$error);
        }
    }

    public function CMD_Stop() {
        $command = EcovacsXMPP::SET_STOP;
        $this->SetCommand($command);
    }
    
    public function CMD_AutoClean($speed = EcovacsXMPP::SPEED_STANDARD) {
        if (($speed==EcovacsXMPP::SPEED_STRONG)) {
            $command = EcovacsXMPP::SET_CLEAN_AUTO_STRONG;
        } else {
            $command = EcovacsXMPP::SET_CLEAN_AUTO_STANDARD;
        }
        $this->SetCommand($command);
    }
    
    public function CMD_BorderClean($speed = EcovacsXMPP::SPEED_STANDARD) {
        if (($speed==EcovacsXMPP::SPEED_STRONG)) {
            $command = EcovacsXMPP::SET_CLEAN_BORDER_STRONG;
        } else {
            $command = EcovacsXMPP::SET_CLEAN_BORDER_STANDARD;
        }
        $this->SetCommand($command);
    }
    
    public function CMD_SpotClean($speed = EcovacsXMPP::SPEED_STANDARD) {
        if (($speed==EcovacsXMPP::SPEED_STRONG)) {
            $command = EcovacsXMPP::SET_CLEAN_SPOT_STRONG;
        } else {
            $command = EcovacsXMPP::SET_CLEAN_SPOT_STANDARD;
        }
        $this->SetCommand($command);
    }
    
    public function CMD_SingleroomClean($speed = EcovacsXMPP::SPEED_STANDARD) {
        if (($speed==EcovacsXMPP::SPEED_STRONG)) {
            $command = EcovacsXMPP::SET_CLEAN_BORDER_STRONG;
        } else {
            $command = EcovacsXMPP::SET_CLEAN_BORDER_STANDARD;
        }
        $this->SetCommand($command);
    }
    
    public function RefreshStatus() { // experimental
        $command = EcovacsXMPP::GET_CLEANSTATE; // TODO: also include GET_CHARGESTATE
        $return = $this->GetCommand($command);
        
        echo $return; // not shure how to handle these XMPP replies yet
    }
    
    public function RefreshBatery() { // experimental
        $command = EcovacsXMPP::GET_BATTERYINFO;
        $return = $this->GetCommand($command);
        
        echo $return; // not shure how to handle these XMPP replies yet
    }
    
}

?>