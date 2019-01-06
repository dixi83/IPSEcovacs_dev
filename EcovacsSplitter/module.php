<?php

require_once(__DIR__ . "/../libs/EcovacsModule.php");

class EcovacsSplitter extends IPSModule
{
    // IPS functions needed for the Module:
    public function __construct($InstanzID) {
        parent::__construct($InstanzID);       
    }
    
    public function Create(){
        
        parent::Create(); //Never delete this line!
        
        $this->RegisterPropertyBoolean("active", false);
        $this->RegisterPropertyString("account", "");
        $this->RegisterPropertyString("password", "");
        $this->RegisterPropertyString("country", "");
        $this->RegisterPropertyString("continent", "");
        $this->RegisterPropertyInteger("RefreshXMPPinfo", 0);
        $this->RegisterTimer("RefreshXMPPinfo", 0, 'EVSP_RefreshXMPPinfo($this->InstanceID);');
        //$this->RegisterTimer("SendData", (1000 * 3600), 'EVSP_SendData($this->InstanceID)));');
    }
        
    public function ApplyChanges(){
		parent::ApplyChanges();	//Never delete this line!
        
        $this->RegisterVariableString ("XMPP_Info", "XMPP_Info"); // info for the EcoVacs XMPP comunication
        $this->RegisterVariableString ("XMPP_Robots", "XMPP_Robots");
        
        if($this->ReadPropertyBoolean("active")) {
            $this->SetStatus(102);
            $this->SetTimerInterval("RefreshXMPPinfo", (1000 * 3600));
        } else {
            $this->SetStatus(104);
            $this->SetTimerInterval("RefreshXMPPinfo", 0);
        }
	}
    
    // Module functions
    public function Test(){ // module for DEV to test some things
        //print_r($_IPS);
    }
    
    public function TestLogin() {
        $account   = $this->ReadPropertyString("account");
        $password  = $this->ReadPropertyString("password");
        $country   = $this->ReadPropertyString("country");
        $continent = $this->ReadPropertyString("continent");
        
        if(filter_var($account, FILTER_VALIDATE_EMAIL)) { //check email address
            if(($password!="")) {
                if(($country!="")) {
                    if(($continent!="")) {
                        $EcovacsHTTP = new EcovacsHTTP($this->InstanceID);
                        if($EcovacsHTTP->HTTPS_Login()) {
                            echo "Login succesfull, the module can be activated now";
                        } else {
                            echo "Login failed, please check all your entered account information";
                            $this->SetStatus(104);
                        }
                    } else {
                        echo "Login failed, please select a continent";
                        $this->SetStatus(104);
                    } 
                } else {
                    echo "Login failed, please select a country";
                    $this->SetStatus(104);
                }   
            } else {
                echo "Login failed, please enter a password";
                $this->SetStatus(104);
            }
        } else {
            echo "Login failed, please check the entered email address";
            $this->SetStatus(104);
        }
    }
    
    public function RefreshXMPPinfo() {
        $EcovacsHTTP = new EcovacsHTTP($this->InstanceID);
        
        if($EcovacsHTTP->HTTPS_Login()) {
            if($EcovacsHTTP->HTTPS_getAuthCode()) {
                if ($EcovacsHTTP->HTTPS_loginByItToken()) {
                    $XMPP['username'] 	= $EcovacsHTTP->meta['uid'];
                    $XMPP['password'] 	= '0/'.$EcovacsHTTP->meta['resource'].'/'.$EcovacsHTTP->meta['token'];
                    $XMPP['continent']	= $EcovacsHTTP->meta['continent'];
                    $XMPP['resource']	= $EcovacsHTTP->meta['resource'];
                    $XMPP['domain']		= $EcovacsHTTP->meta['realm'];
                    
                    $this->SetValue("XMPP_Info", json_encode($XMPP));
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function RefreshDeviceList() {
        $EcovacsHTTP = new EcovacsHTTP($this->InstanceID);
        
        if($EcovacsHTTP->HTTPS_Login()) {
            if($EcovacsHTTP->HTTPS_getAuthCode()) {
                if ($EcovacsHTTP->HTTPS_loginByItToken()) {
                    $XMPP['username'] 	= $EcovacsHTTP->meta['uid'];
                    $XMPP['password'] 	= '0/'.$EcovacsHTTP->meta['resource'].'/'.$EcovacsHTTP->meta['token'];
                    $XMPP['continent']	= $EcovacsHTTP->meta['continent'];
                    $XMPP['resource']	= $EcovacsHTTP->meta['resource'];
                    $XMPP['domain']		= $EcovacsHTTP->meta['realm'];
                    
                    $this->SetValue("XMPP_Info", json_encode($XMPP));
                    
                    if ($EcovacsHTTP->EcoVacsHTTPS_GetDeviceList()) {
                        $this->SetValue("XMPP_Robots", json_encode($EcovacsHTTP->meta['Robot']));
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
                       
} // end class EcovacsSplitter

?>