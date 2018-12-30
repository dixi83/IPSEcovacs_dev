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
        
        $this->RegisterVariableString ("AccountInfo", "AccountInfo");
	}
    
    public function __construct($InstanzID) {
        parent::__construct($InstanzID);       
    }
        
    public function getAccountInfo() {
        $json = $this->GetValue("AccountInfo");
        if ($json=="false"){
            return false;
        } else {
            return json_decode($json,true);
        }
    }

    public function setAccountInfo(string $country, string $continent, string $httpServer, string $xmppServer, string $account, string $password) {
        //$md5pw  = md5($password); do this in the form.json
        $array  = array("httpServer"=>$httpServer,"xmppServer"=>$xmppServer, "country"=>$country, "continent"=>$continent ,"account"=>$account, "password"=>$password);
        $json   = json_encode($array);
        $this->SetValue("AccountInfo", $json);
    }
    
    public function TestAndSaveLogin(string $country, string $continent, string $httpServer, string $xmppServer, string $username, string $password) {
        $this->setAccountInfo($country, $continent, $httpServer, $xmppServer, $username, $password);
        $EcovacsHTTP = new EcovacsHTTP($this->InstanceID);
        if($EcovacsHTTP->HTTPS_Login()) {
            echo "Login succesful and saved";
        } else {
            echo "Login failed, please check your entered account information";
        }
    }
} // end class EcovacsSplitter

?>