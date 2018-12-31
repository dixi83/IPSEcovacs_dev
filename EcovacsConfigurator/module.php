<?php

    require_once(__DIR__ . "/../libs/EcoVacsModule.php");

class EcovacsSplitter extends IPSModule
{
    // IPS functions needed for the Module:
    public function Create(){
        parent::Create(); //Never delete this line!
    }
        
    public function ApplyChanges(){
		parent::ApplyChanges();	//Never delete this line!
        
        $this->RegisterPropertyString("account", "");
        $this->RegisterPropertyString("password", "");
        $this->RegisterPropertyString("country", "");
        $this->RegisterPropertyString("continent", "");
        
        $this->RegisterVariableString ("XMPP_Info", "XMPP_Info"); // info for the EcoVacs XMPP comunication
	}
    
    public function __construct($InstanzID) {
        parent::__construct($InstanzID);       
    }
        
    //public function getAccountInfo() {
    //    $json = $this->GetValue("AccountInfo");
    //    if ($json=="false"){
    //        return false;
    //    } else {
    //        return json_decode($json,true);
    //    }
    //}

    //public function setAccountInfo(string $country, string $continent, string $httpServer, string $xmppServer, string $account, string $password) {
    //    //$md5pw  = md5($password); do this in the form.json
    //    $array  = array("country"=>$country, "continent"=>$continent, "httpServer"=>$httpServer, "xmppServer"=>$xmppServer, "account"=>$account, "password"=>$password);
    //    $json   = json_encode($array);
    //    $this->SetValue("AccountInfo", $json);
    //}
    
    public function TestAndSaveLogin() {
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
                            echo "Login succesful and saved";
                        } else {
                            echo "Login failed, please check your entered account information";
                        }
                    } else {
                        echo "Login failed, please select a continent";
                    } 
                } else {
                    echo "Login failed, please select a country";
                }   
            } else {
                echo "Login failed, please enter a password";
            }
        } else {
            echo "Login failed, please check the entered email address";
        }
    }
                       
} // end class EcovacsSplitter

?>