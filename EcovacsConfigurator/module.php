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
        
    public function getAccountInfo() {
        $json = $this->GetValue("AccountInfo");
        if ($json=="false"){
            return false;
        } else {
            return json_decode($json,true);
        }
    }

    public function setAccountInfo(string $country, string $httpServer, string $xmppServer, string $account, string $password) {
        //$md5pw  = md5($password); do this in the form.json
        $array  = array("httpsServer"=>$httpServer,"xmppServer"=>$xmppServer, "country"=>$country,"account"=>$account,"password"=>$password);
        $json   = json_encode($array);
        $this->SetValue("AccountInfo", $json);
    }
    
    public function TestAndSaveLogin($country,$httpServer,$xmppServer,$username,$password) {
        $this->setAccountInfo($country, $httpServer, $xmppServer, $username, $password);
        $EcovacsHTTP = new EcovacsHTTP;
        if($EcovacsHTTP->HTTPS_Login()) {
            echo "Login succesful and saved";
        } else {
            echo "Login failed, please check your entered account information";
        }
    }
} // end class EcovacsSplitter

?>