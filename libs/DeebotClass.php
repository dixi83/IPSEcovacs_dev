<?php

        public $meta;
        
        $meta['country']	  = $country;           //TODO: these 4 should come from a login Variable
        $meta['continent']	  = $continent;         //TODO
        $meta['account']	  = encrypt($username); //TODO (this is a md5 conerverted value)
        $meta['password']	  = encrypt($password); //TODO
        $meta['lang']		  = 'en';
        $meta['appCode']	  = 'i_eco_e';
        $meta['appVersion']   = '1.3.5';
        $meta['channel']	  = 'c_googleplay';
        $meta['deviceType']	  = '1';
        $meta['authTimespan'] = round(microtime(true)*1000);
        $meta['authTimeZone'] = 'GMT-8';
        $meta['deviceId']	  = md5(time()/5); 
        $meta['resource']	  = substr($meta['deviceId'], 0, 8);
        $meta['authAppkey']	  = $ckey;
        $meta['realm']		  = 'ecouser.net';
        
        public function HTTPS_Login()
        {
            function EcoVacsHTTPS_Login(&$meta){
                global $function;

                $this->meta'requestId']	= md5(round(microtime(true)*1000));	
                $this->meta'requestId']	= $this->meta'requestId'];

                $MAIN_URL_FORMAT = 'https://eco-'.$this->meta'country'].'-api.ecovacs.com/v1/private/'.$this->meta'country'].'/'.$this->meta'lang'].'/'.$this->meta'deviceId'].'/'.$this->meta'appCode'].'/'.$this->meta'appVersion'].'/'.$this->meta'channel'].'/'.$this->meta'deviceType'];

                $order 				= array('account','appCode','appVersion','authTimeZone','authTimespan','channel','country','deviceId','deviceType','lang','password','requestId');
                $info4Sign 			= orderArray($order, $meta);	
                $authSign 			= sign($info4Sign);
                $this->meta'authSign']	= md5($authSign);

                $order 		= array('account','password','requestId','authTimespan','authTimeZone','authAppkey','authSign');
                $info4Url 	= orderArray($order, $meta);	
                $query 		= "?".http_build_query($info4Url, '', '&');	
                $url	 	= $MAIN_URL_FORMAT.'/'.$function['login'].$query;

                $response = file_get_contents($url);

                if($response==false) {
                    IPS_LogMessage("Ecovacs", 'Login Failed!  No connection'; //echo 'Login Failed, No connection';
                    return false;
                } else {
                    $return = json_decode($response,true);
                    if($return['code']!='0000') {
                        IPS_LogMessage("Ecovacs", 'Login Failed! '.showMsg($return['code'])); //echo 'Login Failed! '.showMsg($return['code']);
                        return false;
                    } else {
                        unset($this->meta'requestId']);
                        $meta = array_merge($this->meta,$return['data']);
                        return $return;
                    }
                }
            }
        }
        
        function encrypt($plaintext) {
            global $key;

            $key = "-----BEGIN CERTIFICATE-----\r\n" . chunk_split($key) . "\r\n-----END CERTIFICATE-----";

            $x509 = new File_X509();
            $x509->loadX509($key);
            $pkey = $x509->getPublicKey();

            openssl_public_encrypt( $plaintext , $result , $pkey );

            $result = base64_encode($result);
            return $result;	

        } // end of function encrypt

        function sign($meta) {
            global $ckey, $secret;

            ksort($meta);

            $string = '';

            foreach($meta as $key => $value) {
                $string = $string.$key.'='.$value;
            }

            return $ckey.$string.$secret;
        }

        function orderArray($order, $array) {
            foreach($order as $value) {
                $return[$value] = $array[$value];
            }
            return $return;
        }

        function renameKeysInArray($oldNames, $newNames, $array) {
            foreach($oldNames as $key => $value) {
                $return[$newNames[$key]] = $array[$value];
            }
            return $return;
        }
        
        function getLoginInfo() {
            $id   = IPS_GetInstanceListByModuleID("");              //TODO
            $json = IPS_GetObjectIDByIdent("EVDB_LoginInfo", $id);
            return json_decode($json,true);
        }
                                   
        function showMsg($nr) {
            $code['0000'] = 'login OK';
            $code['0001'] = 'operation failed';
            $code['0002'] = 'interface authentication failed';
            $code['0003'] = 'abnormal parameter';
            $code['1005'] = 'wrong username/password';
            $code['9001'] = 'Authorization code expired!';

            return $nr.': '.$code[$nr];
        }

?>