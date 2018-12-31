<?php

class EcovacsHTTP extends IPSModule
{    
    // IPS functions needed for the Module:
    public function Create() {
        parent::Create(); //Never delete this line!
    }
    
    public function ApplyChanges() {
		parent::ApplyChanges();	//Never delete this line!
	}
    
    // Functions needed for EcoVacs Vac
    public $key;        // = 'MIIB/TCCAWYCCQDJ7TMYJFzqYDANBgkqhkiG9w0BAQUFADBCMQswCQYDVQQGEwJjbjEVMBMGA1UEBwwMRGVmYXVsdCBDaXR5MRwwGgYDVQQKDBNEZWZhdWx0IENvbXBhbnkgTHRkMCAXDTE3MDUwOTA1MTkxMFoYDzIxMTcwNDE1MDUxOTEwWjBCMQswCQYDVQQGEwJjbjEVMBMGA1UEBwwMRGVmYXVsdCBDaXR5MRwwGgYDVQQKDBNEZWZhdWx0IENvbXBhbnkgTHRkMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDb8V0OYUGP3Fs63E1gJzJh+7iqeymjFUKJUqSD60nhWReZ+Fg3tZvKKqgNcgl7EGXp1yNifJKUNC/SedFG1IJRh5hBeDMGq0m0RQYDpf9l0umqYURpJ5fmfvH/gjfHe3Eg/NTLm7QEa0a0Il2t3Cyu5jcR4zyK6QEPn1hdIGXB5QIDAQABMA0GCSqGSIb3DQEBBQUAA4GBANhIMT0+IyJa9SU8AEyaWZZmT2KEYrjakuadOvlkn3vFdhpvNpnnXiL+cyWy2oU1Q9MAdCTiOPfXmAQt8zIvP2JC8j6yRTcxJCvBwORDyv/uBtXFxBPEC6MDfzU2gKAaHeeJUWrzRv34qFSaYkYta8canK+PSInylQTjJK9VqmjQ';
    public $ckey;       // = 'eJUWrzRv34qFSaYk';
    public $secret;     // = 'Cyu5jcR4zyK6QEPn1hdIGXB5QIDAQABMA0GC';

    public $meta = array();
    public $function = array();
    
    public function __construct($InstanzID) {
        parent::__construct($InstanzID);       
    }

    public function HTTPS_Login()
    {            
        $this->key      = 'MIIB/TCCAWYCCQDJ7TMYJFzqYDANBgkqhkiG9w0BAQUFADBCMQswCQYDVQQGEwJjbjEVMBMGA1UEBwwMRGVmYXVsdCBDaXR5MRwwGgYDVQQKDBNEZWZhdWx0IENvbXBhbnkgTHRkMCAXDTE3MDUwOTA1MTkxMFoYDzIxMTcwNDE1MDUxOTEwWjBCMQswCQYDVQQGEwJjbjEVMBMGA1UEBwwMRGVmYXVsdCBDaXR5MRwwGgYDVQQKDBNEZWZhdWx0IENvbXBhbnkgTHRkMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDb8V0OYUGP3Fs63E1gJzJh+7iqeymjFUKJUqSD60nhWReZ+Fg3tZvKKqgNcgl7EGXp1yNifJKUNC/SedFG1IJRh5hBeDMGq0m0RQYDpf9l0umqYURpJ5fmfvH/gjfHe3Eg/NTLm7QEa0a0Il2t3Cyu5jcR4zyK6QEPn1hdIGXB5QIDAQABMA0GCSqGSIb3DQEBBQUAA4GBANhIMT0+IyJa9SU8AEyaWZZmT2KEYrjakuadOvlkn3vFdhpvNpnnXiL+cyWy2oU1Q9MAdCTiOPfXmAQt8zIvP2JC8j6yRTcxJCvBwORDyv/uBtXFxBPEC6MDfzU2gKAaHeeJUWrzRv34qFSaYkYta8canK+PSInylQTjJK9VqmjQ';
        $this->ckey     = 'eJUWrzRv34qFSaYk';
        $this->secret   = 'Cyu5jcR4zyK6QEPn1hdIGXB5QIDAQABMA0GC';

        // statics needed for logging in and the rest of the communication
        $this->meta['lang']           = 'en';
        $this->meta['appCode']        = 'i_eco_e';
        $this->meta['appVersion']     = '1.3.5';
        $this->meta['channel']        = 'c_googleplay';
        $this->meta['deviceType']     = '1';
        $this->meta['authTimespan']   = round(microtime(true)*1000);
        $this->meta['authTimeZone']   = 'GMT-8';
        $this->meta['deviceId']       = md5(time()/5); 
        $this->meta['resource']       = substr($this->meta['deviceId'], 0, 8);
        $this->meta['authAppkey']     = $this->ckey;
        $this->meta['realm']          = 'ecouser.net';

        $this->function['login']			= 'user/login';
        $this->function['getAuthCode']	    = 'user/getAuthCode';
        $this->function['loginByItToken']   = 'loginByItToken';
        
        $EcovacsSplitter = new EcovacsSplitter($this->InstanceID);
        
        $account   = $EcovacsSplitter->ReadPropertyString("account");
        $password  = $EcovacsSplitter->ReadPropertyString("password");
        
        $this->meta['requestId']	= md5(round(microtime(true)*1000));	 // this have to be different every call you make to the HTTPS API
        $this->meta['country']      = $EcovacsSplitter->ReadPropertyString("country");
        $this->meta['continent']    = $EcovacsSplitter->ReadPropertyString("continent");
        $this->meta['account']      = $this->encrypt($account);
        $this->meta['password']     = $this->encrypt(md5($password));

        $MAIN_URL_FORMAT = 'https://eco-'.$this->meta['country'].'-api.ecovacs.com/v1/private/'.$this->meta['country'].'/'.$this->meta['lang'].'/'.$this->meta['deviceId'].'/'.$this->meta['appCode'].'/'.$this->meta['appVersion'].'/'.$this->meta['channel'].'/'.$this->meta['deviceType'];

        $order 				= array('account','appCode','appVersion','authTimeZone','authTimespan','channel','country','deviceId','deviceType','lang','password','requestId');
        $info4Sign 			= $this->orderArray($order, $this->meta);	
        $authSign 			= $this->sign($info4Sign);
        $this->meta['authSign']	= md5($authSign);

        $order 		= array('account','password','requestId','authTimespan','authTimeZone','authAppkey','authSign');
        $info4Url 	= $this->orderArray($order, $this->meta);	
        $query 		= "?".http_build_query($info4Url, '', '&');	
        $url	 	= $MAIN_URL_FORMAT.'/'.$this->function['login'].$query;

        $response = file_get_contents($url);

        if($response==false) {
            IPS_LogMessage("Ecovacs", 'Login Failed!  No connection or wrong URL'); 
            return false;
        } else {
            $return = json_decode($response,true);
            if($return['code']!='0000') { // 0000 = login succes
                IPS_LogMessage("Ecovacs", 'Login Failed! '.$this->showMsg($return['code'])); 
                return false;
            } else {
                unset($this->meta['requestId']);
                $this->meta = array_merge($this->meta,$return['data']);
                return $return;
            }
        }
    }

    public function HTTPS_getAuthCode(){
        $this->meta['requestId']	= md5(round(microtime(true)*1000));  // this have to be different every call you make to the HTTPS API

        $MAIN_URL_FORMAT = 'https://eco-'.$this->meta['country'].'-api.ecovacs.com/v1/private/'.$this->meta['country'].'/'.$this->meta['lang'].'/'.$this->meta['deviceId'].'/'.$this->meta['appCode'].'/'.$this->meta['appVersion'].'/'.$this->meta['channel'].'/'.$this->meta['deviceType'];

        $order 				= array('accessToken','appCode','appVersion','authTimeZone','authTimespan','channel','country','deviceId','deviceType','lang','requestId','uid');
        $info4Sign			= $this->orderArray($order, $this->meta);
        $authSign 			= $this->sign($info4Sign);
        $this->meta['authSign']	= md5($authSign);


        $order 		= array('uid','accessToken','requestId','authTimespan','authTimeZone','authAppkey','authSign');
        $info4Url 	= $this->orderArray($order, $this->meta);
        $query 		= "?".http_build_query($info4Url, '', '&');	
        $url	 	= $MAIN_URL_FORMAT.'/'.$this->function['getAuthCode'].$query;

        $response = file_get_contents($url);

        if($response==false) {
            IPS_LogMessage("Ecovacs", 'getAuthCode Failed! No connection or wrong URL'); //echo 'Error! no connection or URL is wrong.';
            return false;
        } else {
            $return = json_decode($response,true);
            if($return['code']!='0000') {
                IPS_LogMessage("Ecovacs", 'getAuthCode Failed! '.$this->showMsg($return['code']));
                return false;
            } else {
                unset($this->meta['requestId']);
                $this->meta = array_merge($this->meta,$return['data']);
                //print_r($this->meta);
                return $return;
            }
        }
    }
    
    public function HTTPS_loginByItToken(){
        $USER_URL_FORMAT = 'https://users-'.$this->meta['continent'].'.ecouser.net:8000/user.do';

        $ch = curl_init($USER_URL_FORMAT);

        $this->meta['todo'] = 'loginByItToken';

        $order 		= array('authCode','realm','uid','resource','todo','country');
        $info4Post  = $this->orderArray($order, $this->meta);
        $newKeys	= array('token','realm','userId','resource','todo','country');
        $info4Post  = $this->renameKeysInArray($order,$newKeys,$info4Post);

        $info4Post['country'] = strtoupper($info4Post['country']);

        $json_str = json_encode($info4Post);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_str);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $result = curl_exec($ch);
        curl_close($ch);

        if($result==false) {
            IPS_LogMessage("Ecovacs", 'LoginByToken Failed! No connection or wrong URL');
            return false;
        } else {
            $return = json_decode($result,true);
            if($return['result']!='ok') {
                IPS_LogMessage("Ecovacs", 'LoginByToken Failed! '.$return['error']);
                return false;
            } else {
                $this->meta['token'] = $return['token'];
                //print_r($this->meta);
                return $return;
            }
        }
    }

    function EcoVacsHTTPS_GetDeviceList(){
        $USER_URL_FORMAT = 'https://users-'.$this->meta['continent'].'.ecouser.net:8000/user.do';

        $ch = curl_init($USER_URL_FORMAT);

        $this->meta['todo'] 	= 'GetDeviceList';
        $this->meta['with'] 	= 'users';

        $order			= array('with','realm','token','uid','resource');
        $auth	 		= $this->orderArray($order, $this->meta);
        $newKeys		= array('with','realm','token','userid','resource');
        $this->meta['auth']	= $this->renameKeysInArray($order,$newKeys,$auth);

        $order 		= array('todo','uid','auth');
        $info4Post 	= $this->orderArray($order, $this->meta);
        $newKeys	= array('todo','userid','auth');
        $info4Post	= $this->renameKeysInArray($order,$newKeys,$info4Post);

        $json_str = json_encode($info4Post);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_str);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $result = curl_exec($ch);
        curl_close($ch);

        if($result==false) {
            echo 'Error! no connection or URL is wrong.';
            return false;
        } else {
            $return = json_decode($result,true);
            if($return['result']!='ok') {
                echo 'Error! '.$return['error'];
                return false;
            } else {
                $EcovacsSplitter = new EcovacsSplitter($this->InstanceID);
                $oldRobotInfo = $EcovacsSplitter->GetValue("XMPP_Robots");
                if(($oldRobotInfo!="")) {
                    $oldRobotInfo = json_decode($oldRobotInfo);
                }
                $i = 0;                
                foreach($return['devices'] as $value){
                    if(is_array($oldRobotInfo)){
                        print_r($oldRobotInfo);
                        //foreach($oldRobotInfo as $key => $value){
                        //    if(($return['devices'][$i]['did']==$value[$key]['RobotSerialNr'])) {
                        //        $prevName   = $value[$key]['RobotName'];
                        //        $prevId     = $value[$key]['InstanceId'];
                        //    } else {
                                $prevName   = "";
                                $prevId     = 0;
                        //    }
                        //}
                    } else {
                        $prevName   = "";
                        $prevId     = 0;
                    }
                    
                    $Robot[$i]['RobotNr']       = $i;
                    $Robot[$i]['RobotName']     = $prevName;
                    $Robot[$i]['InstanceId']    = $prevId;
                    $Robot[$i]['RobotSerialNr'] = $return['devices'][$i]['did'];
                    $Robot[$i]['XMPPaddress']   = $return['devices'][$i]['did'].'@'.$return['devices'][$i]['class'].'.ecorobot.net/'.$return['devices'][$i]['resource'];
                    ++$i;
                }
                $this->meta['Robot'] = $Robot;
                return $Robot;
            }
        }
    }

    public function encrypt($plaintext) {
        require_once('crypt/Crypt/RSA.php');
        require_once('crypt/File/X509.php');
        
        $key = "-----BEGIN CERTIFICATE-----\r\n" . chunk_split($this->key) . "\r\n-----END CERTIFICATE-----";

        $x509 = new File_X509();
        $x509->loadX509($key);
        $pkey = $x509->getPublicKey();

        openssl_public_encrypt( $plaintext , $result , $pkey );

        $result = base64_encode($result);
        return $result;	

    } // end of function encrypt

    public function sign($meta) {
        global $ckey, $secret;

        ksort($meta);

        $string = '';

        foreach($meta as $key => $value) {
            $string = $string.$key.'='.$value;
        }

        return $this->ckey.$string.$this->secret;
    }

    public function orderArray($order, $array) {
        foreach($order as $value) {
            $return[$value] = $array[$value];
        }
        return $return;
    }

    public function renameKeysInArray($oldNames, $newNames, $array) {
        foreach($oldNames as $key => $value) {
            $return[$newNames[$key]] = $array[$value];
        }
        return $return;
    }

    public function showMsg($nr) {
        $code['0000'] = 'login OK';
        $code['0001'] = 'operation failed';
        $code['0002'] = 'interface authentication failed';
        $code['0003'] = 'abnormal parameter';
        $code['1005'] = 'wrong username/password';
        $code['9001'] = 'Authorization code expired!';

        return $nr.': '.$code[$nr];
    }
}

class EcovacsXMPP extends IPSModule {
        // IPS functions needed for the Module:
    public function Create(){
        parent::Create(); //Never delete this line!
    }
    
    public function ApplyChanges(){
		parent::ApplyChanges();	//Never delete this line!
	}
    
    // Functions needed for EcoVacs Vac
    
}

?>