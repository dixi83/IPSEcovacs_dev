<?php

require_once(__DIR__ . "/../libs/EcovacsModule.php");

class EcovacsDeebot extends IPSModule
{
    // IPS functions needed for the Module:
    public function Create() {
        parent::Create(); //Never delete this line!
        
        $this->RequireParent("{8EB4291C-8EC8-4E10-B5D7-1F90CC37BD8D}"); // Ecovacs Splitter
        
        $id = $this->InstanceID;
        
        $this->RegisterPropertyBoolean("active", false);
        $this->RegisterPropertyString("RobotSerialNr", "");
        $this->RegisterPropertyInteger("RefreshStatus", 0);
        $this->RegisterTimer("RefreshStatus", 0, 'EVDB_RefreshStatus($id);');
        $this->RegisterTimer("RefreshBatery", 0, 'EVDB_RefreshBatery($id);');
    }
        
    public function ApplyChanges() {
		parent::ApplyChanges();	//Never delete this line!
        
        $this->RegisterVariableString ("DeebotStatus", "DeebotStatus"); 
        $this->RegisterVariableString ("DeebotBatery", "DeebotBattery");
        
        if($this->ReadPropertyBoolean("active")) {
            $this->SetStatus(102); // activated
            //$this->SetTimerInterval("RefreshBatery", (1000 * 150)); // 2,5 minutes            // get functions not tested yet
            //$this->SetTimerInterval("RefreshStatus", (1000 * $this->ReadPropertyInteger));    // get functions not tested yet
        } else {
            $this->SetStatus(104); // inactive
            $this->SetTimerInterval("RefreshBatery", 0);
            $this->SetTimerInterval("RefreshStatus", 0);
        }
	}
    
    //public function Test(){
    //    echo GetStatus($this->InstanceID);
    //}
    
    protected function Command(string $command) {        
        $EvovacsXMPP    = new EcovacsXMPP($this->InstanceID);
        $RobotSerialNr  = $this->ReadPropertyString("RobotSerialNr");
        
        try {
            return $EvovacsXMPP->XMPPcommand($RobotSerialNr,$command);
        } catch(Exception $error) {
            IPS_LogMessage("Ecovacs Deebot", "XMPP: ".$error);
            return false;
        }
        return $return;
    }

    public function CMD_Stop() {
        $command = EcovacsXMPP::SET_STOP;
        $this->Command($command);
    }
    
    public function CMD_AutoClean(string $speed = EcovacsXMPP::SPEED_STANDARD) {
        if (($speed==EcovacsXMPP::SPEED_STRONG)) {
            $command = EcovacsXMPP::SET_CLEAN_AUTO_STRONG;
        } else {
            $command = EcovacsXMPP::SET_CLEAN_AUTO_STANDARD;
        }
        $this->Command($command);
    }
    
    public function CMD_BorderClean(string $speed = EcovacsXMPP::SPEED_STANDARD) {
        if (($speed==EcovacsXMPP::SPEED_STRONG)) {
            $command = EcovacsXMPP::SET_CLEAN_BORDER_STRONG;
        } else {
            $command = EcovacsXMPP::SET_CLEAN_BORDER_STANDARD;
        }
        $this->Command($command);
    }
    
    public function CMD_SpotClean(string $speed = EcovacsXMPP::SPEED_STANDARD) {
        if (($speed==EcovacsXMPP::SPEED_STRONG)) {
            $command = EcovacsXMPP::SET_CLEAN_SPOT_STRONG;
        } else {
            $command = EcovacsXMPP::SET_CLEAN_SPOT_STANDARD;
        }
        $this->Command($command);
    }
    
    public function CMD_SingleroomClean(string $speed = EcovacsXMPP::SPEED_STANDARD) {
        if (($speed==EcovacsXMPP::SPEED_STRONG)) {
            $command = EcovacsXMPP::SET_CLEAN_BORDER_STRONG;
        } else {
            $command = EcovacsXMPP::SET_CLEAN_BORDER_STANDARD;
        }
        $this->Command($command);
    }
    
    public function CMD_GoCharge() {
        $command = EcovacsXMPP::SET_CHARGE_GO;
        $this->Command($command);
    }
    
    public function RefreshStatus() { // experimental
        $command = EcovacsXMPP::GET_CLEANSTATE; // TODO: also include GET_CHARGESTATE
        $return = $this->Command($command);
        
        return $return['clean']['@attributes']; // not shure how to handle these XMPP replies yet
    }
    
    public function RefreshBatery() { // experimental
        $command = EcovacsXMPP::GET_BATTERYINFO;
        $return = $this->Command($command);
        
        return $return['battery']['@attributes']['power']; // not shure how to handle these XMPP replies yet
    }
    
    public function RefreshError() { // experimental
        $command = EcovacsXMPP::GET_ERROR;
        $return = $this->Command($command);
        
        return $return; // not shure how to handle these XMPP replies yet
    }
}

?>
