<?php

    class EcoVacs extends Modules {
        
        // IPS functions needed for the Module:
        
        public function Create(){
            // Diese Zeile nicht löschen oder ändern.
            parent::Create();
            
            $this->RegisterAttributeString("AccountInfo", "false");
            //$this->RegisterAttributeBoolean("LoginSucces",false)
        }
        
        public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();	
		}
        
        
        // Functions needed for EcoVacs Vac
        public $key = 'MIIB/TCCAWYCCQDJ7TMYJFzqYDANBgkqhkiG9w0BAQUFADBCMQswCQYDVQQGEwJjbjEVMBMGA1UEBwwMRGVmYXVsdCBDaXR5MRwwGgYDVQQKDBNEZWZhdWx0IENvbXBhbnkgTHRkMCAXDTE3MDUwOTA1MTkxMFoYDzIxMTcwNDE1MDUxOTEwWjBCMQswCQYDVQQGEwJjbjEVMBMGA1UEBwwMRGVmYXVsdCBDaXR5MRwwGgYDVQQKDBNEZWZhdWx0IENvbXBhbnkgTHRkMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDb8V0OYUGP3Fs63E1gJzJh+7iqeymjFUKJUqSD60nhWReZ+Fg3tZvKKqgNcgl7EGXp1yNifJKUNC/SedFG1IJRh5hBeDMGq0m0RQYDpf9l0umqYURpJ5fmfvH/gjfHe3Eg/NTLm7QEa0a0Il2t3Cyu5jcR4zyK6QEPn1hdIGXB5QIDAQABMA0GCSqGSIb3DQEBBQUAA4GBANhIMT0+IyJa9SU8AEyaWZZmT2KEYrjakuadOvlkn3vFdhpvNpnnXiL+cyWy2oU1Q9MAdCTiOPfXmAQt8zIvP2JC8j6yRTcxJCvBwORDyv/uBtXFxBPEC6MDfzU2gKAaHeeJUWrzRv34qFSaYkYta8canK+PSInylQTjJK9VqmjQ';

        public $ckey    = 'eJUWrzRv34qFSaYk';
        public $secret  = 'Cyu5jcR4zyK6QEPn1hdIGXB5QIDAQABMA0GC';

        public $meta;
        
        // statics needed for logging in and the rest of the communication
        $meta['lang']           = 'en';
        $meta['appCode']        = 'i_eco_e';
        $meta['appVersion']     = '1.3.5';
        public $meta['channel']        = 'c_googleplay';
        public $meta['deviceType']     = '1';
        public $meta['authTimespan']   = round(microtime(true)*1000);
        public $meta['authTimeZone']   = 'GMT-8';
        public $meta['deviceId']       = md5(time()/5); 
        public $meta['resource']       = substr($meta['deviceId'], 0, 8);
        public $meta['authAppkey']     = $ckey;
        public $meta['realm']          = 'ecouser.net';
        
        public $function['login']			= 'user/login';
        public $function['getAuthCode']	= 'user/getAuthCode';
        public $function['loginByItToken']	= 'loginByItToken';
        
        protected function getAccountInfo() {
            $json = $this->ReadAttributeString("AccountInfo");
            if ($json=="false"){
                return false;
            } else {
                return json_decode($json,true);
            }
        }
        
        public function setAccountInfo($country, $httpServer, $xmppServer, $account, $password) {
            $md5pw  = md5($password); 
            $array  = array("httpsServer"=>$httpServer,"xmppServer"=>$xmppServer, "country"=>$country,"account"=>$account,"password"=>$md5pw);
            $json   = json_encode($array);
            $this->WriteAttributeString("AccountInfo", $json);
        }
        
        public function HTTPS_Login()
        {            
            global $function;
            
            $accountInfo = getAccountInfo();
            
            if($accountInfo=="false"){
                IPS_LogMessage("Ecovacs", 'Login Failed! No account info please enter your info in the configurator.');
                return false;
            }

            $this->meta['requestId']	= md5(round(microtime(true)*1000));	 // this have to be different every call you make to the HTTPS API
            //$this->meta['requestId']	= $this->meta['requestId'];
            $this->meta['country']      = $accountInfo['httpServer'];
            $this->meta['continent']    = $accountInfo['xmppServer'];
            $this->meta['account']      = encrypt($accountInfo['username']); //this is a md5 conerverted value
            $this->meta['password']     = encrypt($accountInfo['password']);

            $MAIN_URL_FORMAT = 'https://'.$this->meta['country']'/v1/private/'.$this->meta['country'].'/'.$this->meta['lang'].'/'.$this->meta['deviceId'].'/'.$this->meta['appCode'].'/'.$this->meta['appVersion'].'/'.$this->meta['channel'].'/'.$this->meta['deviceType'];

            $order 				= array('account','appCode','appVersion','authTimeZone','authTimespan','channel','country','deviceId','deviceType','lang','password','requestId');
            $info4Sign 			= orderArray($order, $meta);	
            $authSign 			= sign($info4Sign);
            $this->meta['authSign']	= md5($authSign);

            $order 		= array('account','password','requestId','authTimespan','authTimeZone','authAppkey','authSign');
            $info4Url 	= orderArray($order, $meta);	
            $query 		= "?".http_build_query($info4Url, '', '&');	
            $url	 	= $MAIN_URL_FORMAT.'/'.$function['login'].$query;

            $response = file_get_contents($url);

            if($response==false) {
                IPS_LogMessage("Ecovacs", 'Login Failed!  No connection or wrong URL'; //echo 'Login Failed, No connection';
                return false;
            } else {
                $return = json_decode($response,true);
                if($return['code']!='0000') { // 0000 = login succes
                    IPS_LogMessage("Ecovacs", 'Login Failed! '.showMsg($return['code'])); //echo 'Login Failed! '.showMsg($return['code']);
                    return false;
                } elseif($return['code']==1005){
                    IPS_LogMessage("Ecovacs", 'Login Failed! '.showMsg($return['code']));
                    $this->WriteAttributeString("AccountInfo", "false");
                } else {
                    unset($this->meta['requestId']);
                    $meta = array_merge($this->meta,$return['data']);
                    return $return;
                }
            }
        }
                                   
        public function HTTPS_getAuthCode(){
            global $function;

            public $meta['requestId']	= md5(round(microtime(true)*1000));  // this have to be different every call you make to the HTTPS API
            //$this->meta['requestId']	= $this->meta['requestId'];

            $MAIN_URL_FORMAT = 'https://eco-'.$meta['country'].'-api.ecovacs.com/v1/private/'.$meta['country'].'/'.$meta['lang'].'/'.$meta['deviceId'].'/'.$meta['appCode'].'/'.$meta['appVersion'].'/'.$meta['channel'].'/'.$meta['deviceType'];

            $order 				= array('accessToken','appCode','appVersion','authTimeZone','authTimespan','channel','country','deviceId','deviceType','lang','requestId','uid');
            $info4Sign			= orderArray($order, $meta);
            $authSign 			= sign($info4Sign);
            public $meta['authSign']	= md5($authSign);


            $order 		= array('uid','accessToken','requestId','authTimespan','authTimeZone','authAppkey','authSign');
            $info4Url 	= orderArray($order, $meta);
            $query 		= "?".http_build_query($info4Url, '', '&');	
            $url	 	= $MAIN_URL_FORMAT.'/'.$function['getAuthCode'].$query;

            $response = file_get_contents($url);

            if($response==false) {
                IPS_LogMessage("Ecovacs", 'GetAuthCode Failed! No connection or wrong URL'; //echo 'Error! no connection or URL is wrong.';
                return false;
            } else {
                $return = json_decode($response,true);
                if($return['code']!='0000') {
                    IPS_LogMessage("Ecovacs", 'GetAuthCode Failed! '.showMsg($return['code']));
                    return false;
                } else {
                    unset($meta['requestId']);
                    $meta = array_merge($meta,$return['data']);
                    return $return;
                }
            }
        }
        
        protected function encrypt($plaintext) {
            global $key;

            $key = "-----BEGIN CERTIFICATE-----\r\n" . chunk_split($key) . "\r\n-----END CERTIFICATE-----";

            $x509 = new File_X509();
            $x509->loadX509($key);
            $pkey = $x509->getPublicKey();

            openssl_public_encrypt( $plaintext , $result , $pkey );

            $result = base64_encode($result);
            return $result;	

        } // end of function encrypt

        protected function sign($meta) {
            global $ckey, $secret;

            ksort($meta);

            $string = '';

            foreach($meta as $key => $value) {
                $string = $string.$key.'='.$value;
            }

            return $ckey.$string.$secret;
        }

        protected function orderArray($order, $array) {
            foreach($order as $value) {
                $return[$value] = $array[$value];
            }
            return $return;
        }

        protected function renameKeysInArray($oldNames, $newNames, $array) {
            foreach($oldNames as $key => $value) {
                $return[$newNames[$key]] = $array[$value];
            }
            return $return;
        }
        
        protected function showMsg($nr) {
            $code['0000'] = 'login OK';
            $code['0001'] = 'operation failed';
            $code['0002'] = 'interface authentication failed';
            $code['0003'] = 'abnormal parameter';
            $code['1005'] = 'wrong username/password';
            $code['9001'] = 'Authorization code expired!';

            return $nr.': '.$code[$nr];
        }
    }
?>