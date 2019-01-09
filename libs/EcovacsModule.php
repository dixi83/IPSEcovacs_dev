<?php

require_once __DIR__ . '/loadXMPP.php';
$autoloaderXMPP = new AutoloaderXMPP('xmpp'); // TODO clean up this library (take out the unused classes and functions)
$autoloaderXMPP->register();

$autoloaderPsr = new AutoloaderPsr('Psr'); // TODO replace this in future for IP-Symocn's LogMessage()
$autoloaderPsr->register();

$autoloaderMonolog = new AutoloaderMonolog('Monolog'); // TODO replace this in future for IP-Symocn's LogMessage()
$autoloaderMonolog->register();

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use xmpp\Options;
use xmpp\Client;
use xmpp\Protocol\Roster;
use xmpp\Protocol\Presence;
use xmpp\Protocol\Message;
use xmpp\Connection;

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
            IPS_LogMessage("Ecovacs", 'getAuthCode Failed! No connection or wrong URL');
            return false;
        } else {
            $return = json_decode($response,true);
            if($return['code']!='0000') {
                IPS_LogMessage("Ecovacs", 'getAuthCode Failed! '.$this->showMsg($return['code']));
                return false;
            } else {
                unset($this->meta['requestId']);
                $this->meta = array_merge($this->meta,$return['data']);
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
            IPS_LogMessage("Ecovacs", 'GetDeviceList Failed! no connection or URL is wrong.');
            return false;
        } else {
            $return = json_decode($result,true);
            if($return['result']!='ok') {
                IPS_LogMessage("Ecovacs", 'GetDeviceList Error! '.$return['error']);
                return false;
            } else {
                $EcovacsSplitter = new EcovacsSplitter($this->InstanceID);
                $oldRobotInfo = $EcovacsSplitter->GetValue("XMPP_Robots");
                if(($oldRobotInfo!="")) {
                    $oldRobotInfo = json_decode($oldRobotInfo,true);
                }
                
                $i = 0; 
                foreach($return['devices'] as $value){
                    if(is_array($oldRobotInfo)){
                        foreach($oldRobotInfo as $value){
                            if(($return['devices'][$i]['did']==$value['RobotSerialNr'])) {
                                $prevName   = $value['RobotName'];
                                $prevId     = $value['InstanceID'];
                            } else {
                                $prevName   = "";
                                $prevId     = 0;
                            }
                        }
                    } else {
                        $prevName   = "";
                        $prevId     = 0;
                    }
                    
                    $Robot[$i]['RobotNr']       = $i;
                    $Robot[$i]['RobotName']     = $prevName;
                    $Robot[$i]['InstanceID']    = $prevId;
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
    
    // defining command constants: for more info https://github.com/wpietri/sucks/blob/master/protocol.md
    const GET_CLEANSTATE                = 'get:cleanState';
    const GET_CHARGESTATE               = 'get:chargeState';
    const GET_BATTERYINFO               = 'get:batteryInfo';
    const GET_LIFESPAN_BRUSH            = 'get:lifespan:brush';
    const GET_LIFESPAN_SIDEBRUSH        = 'get:lifespan:sidebrush';
    const GET_LIFESPAN_DUSTCASEHEAP     = 'get:lifespan:dustcaseheap';
    const GET_ERROR                     = 'get:error';
    
    const SPEED_STANDARD                = 'standard';
    const SPEED_STRONG                  = 'strong';
    
    const SET_CLEAN_AUTO_STANDARD       = 'set:clean:auto:standard';
    const SET_CLEAN_BORDER_STANDARD     = 'set:clean:border:standard';
    const SET_CLEAN_SPOT_STANDARD       = 'set:clean:spot:standard';
    const SET_CLEAN_SINGLEROOM_STANDARD = 'set:clean:singleroom:standard';
    const SET_CLEAN_AUTO_STRONG         = 'set:clean:auto:strong';
    const SET_CLEAN_BORDER_STRONG       = 'set:clean:border:strong';
    const SET_CLEAN_SPOT_STRONG         = 'set:clean:spot:strong';
    const SET_CLEAN_SINGLEROOM_STRONG   = 'set:clean:singleroom:strong';
    const SET_STOP                      = 'set:clean:stop';
    const SET_CHARGE_GO                 = 'set:charge:go';
    const SET_PLAYSOUND                 = 'set:playsound';
    
    // Functions needed for EcoVacs Vac
    public function XMPPsetCommand($robotSerialNr,$command) { // just send message, <iq type="set"> will not get any responce from ecovacs servers
        //$EcovacsSplitter = new EcovacsSplitter($this->InstanceID);
        $SplitterID     = IPS_GetInstance($this->InstanceID)['ConnectionID'];
        $XMPPDataID     = IPS_GetObjectIDByIdent('XMPP_Info', $SplitterID);     
        $XMPP           = json_decode(GetValue($XMPPDataID), true);
        
        $RobotsDataID   = IPS_GetObjectIDByIdent('XMPP_Robots', $SplitterID);
        $robots         = json_decode(GetValue($RobotsDataID), true);
        
        $set['server'] 		= 'msg-'.$XMPP['continent'].'.ecouser.net'; 
        $set['port']		= 5223;
        $set['username']	= $XMPP['username'];//.'@'.$XMPP['domain'];	//sucks      DEBUG    username used to login: 201802265a9437ee73aa7
        $set['password']	= $XMPP['password'];			            //sucks      DEBUG    password used to login: 0/372d00ce/glcTBbzoppbndSRpTflNTpk1gDCAYLQv
        $set['resource']	= $XMPP['resource'];
        $set['domain']		= $XMPP['domain'];
        
        
        foreach($robots as $value){
            if(($robotSerialNr==$value['RobotSerialNr'])){
                $set['vacAddr']	= $value['XMPPaddress'];
                break;
            }
        }
        
        if (!isset($set['vacAddr'])){
            return false;
        }
        
        switch ($command) {
            case self::SET_CLEAN_AUTO_STANDARD:
                $SetMessage = '<query xmlns="com:ctl"><ctl td="Clean"><clean type="auto" speed="standard"/></ctl></query>';
                break;
            case self::SET_CLEAN_BORDER_STANDARD:
                $SetMessage = '<query xmlns="com:ctl"><ctl td="Clean"><clean type="border" speed="standard"/></ctl></query>';
                break;
            case self::SET_CLEAN_SPOT_STANDARD:
                $SetMessage = '<query xmlns="com:ctl"><ctl td="Clean"><clean type="spot" speed="standard"/></ctl></query>';
                break;
            case self::SET_CLEAN_SINGLEROOM_STANDARD:
                $SetMessage = '<query xmlns="com:ctl"><ctl td="Clean"><clean type="singleroom" speed="standard"/></ctl></query>';
                break;
            case self::SET_CLEAN_AUTO_STRONG:
                $SetMessage = '<query xmlns="com:ctl"><ctl td="Clean"><clean type="auto" speed="strong"/></ctl></query>';
                break;
            case self::SET_CLEAN_BORDER_STRONG:
                $SetMessage = '<query xmlns="com:ctl"><ctl td="Clean"><clean type="border" speed="strong"/></ctl></query>';
                break;
            case self::SET_CLEAN_SPOT_STRONG:
                $SetMessage = '<query xmlns="com:ctl"><ctl td="Clean"><clean type="spot" speed="strong"/></ctl></query>';
                break;
            case self::SET_CLEAN_SINGLEROOM_STRONG:
                $SetMessage = '<query xmlns="com:ctl"><ctl td="Clean"><clean type="singleroom" speed="strong"/></ctl></query>';
                break;
            case self::SET_STOP:
                $SetMessage = '<query xmlns="com:ctl"><ctl td="Clean"><clean type="stop" speed="standard"/></ctl></query>';
                break;
            case self::SET_CHARGE_GO:
                $SetMessage = '<query xmlns="com:ctl"><ctl td="Charge"><charge type="go"/></ctl></query>';
                break;
            case self::SET_PLAYSOUND:
                $SetMessages = '<query xmlns="com:ctl"><ctl sid="0" td="PlaySound" /></ctl></query>';
                break;
            default:
                IPS_LogMessage("Ecovacs", 'Unknown Set command!');
                return false;
        }
        
        $logger = new Logger('xmpp');
        $logger->pushHandler(new StreamHandler(__DIR__.'/XMPP_Set.log', Logger::DEBUG));
        
        $message = new Message;
        $message->setMessage($SetMessage)
            ->setTo($set['vacAddr'])
            ->setFrom($set['username'].'/'.md5($set['resource']))
            ->setType(Message::TYPE_EV_SET);

        $options = new Options($set['server'].':'.$set['port']);

        $options->setLogger($logger)
            ->setUsername($set['username'])
            ->setPassword($set['password'])
            ->setTo($set['domain']);

        //$options->setSocksProxyAddress('localhost:8080');
        $client = new Client($options);
        $client->connect();
        $client->send($message);
        $client->disconnect();
    }
    
    public function XMPPgetCommand($robotSerialNr,$command) { // just send message, <iq type="set"> will not get any responce from ecovacs servers
        $SplitterID     = IPS_GetInstance($this->InstanceID)['ConnectionID'];
        $XMPPDataID     = IPS_GetObjectIDByIdent('XMPP_Info', $SplitterID);     
        $XMPP           = json_decode(GetValue($XMPPDataID), true);
        
        $RobotsDataID   = IPS_GetObjectIDByIdent('XMPP_Robots', $SplitterID);
        $robots         = json_decode(GetValue($RobotsDataID), true);
        
        $set['server'] 		= 'msg-'.$XMPP['continent'].'.ecouser.net'; 
        $set['port']		= 5223;
        $set['username']	= $XMPP['username'];//.'@'.$XMPP['domain'];
        $set['password']	= $XMPP['password'];
        $set['resource']	= $XMPP['resource'];
        $set['domain']		= $XMPP['domain'];
        
        
        foreach($robots as $value){
            if(($robotSerialNr==$value['RobotSerialNr'])){
                $set['vacAddr']	= $value['XMPPaddress'];
            }
        }
        
        if (!isset($set['vacAddr'])){
            return false;
        }
        
        switch ($command) {
            case self::GET_CLEANSTATE:
                $GetMessage = '<query xmlns="com:ctl"><ctl td="GetCleanState" /></query>';
                break;
            case self::GET_CHARGESTATE:
                $GetMessage = '<query xmlns="com:ctl"><ctl td="GetChargeState" /></query>';
                break;
            case self::GET_BATTERYINFO:
                $GetMessage = '<query xmlns="com:ctl"><ctl td="GetBatteryInfo" /></query>';
                break;
            case self::GET_LIFESPAN_BRUSH:
                $GetMessage = '<query xmlns="com:ctl"><ctl td="GetLifeSpan" type="Brush" /></query>';
                break;
            case self::GET_LIFESPAN_SIDEBRUSH:
                $GetMessage = '<query xmlns="com:ctl"><ctl td="GetLifeSpan" type="SideBrush" /></query>';
                break;
            case self::GET_LIFESPAN_DUSTCASEHEAP:
                $GetMessage = '<query xmlns="com:ctl"><ctl td="GetLifeSpan" type="DustCaseHeap" /></query>';
                break;
            case self::GET_ERROR:
                $GetMessage = '<query xmlns="com:ctl"><ctl td="GetError" /></query>';
            default:
                IPS_LogMessage("Ecovacs", 'Unknown Get command!');
                return false;
        }
        
        $logger = new Logger('xmpp');
        $logger->pushHandler(new StreamHandler(__DIR__.'/XMPP_Get.log', Logger::DEBUG));
        
        $message = new Message;
        $message->setMessage($GetMessage)
            ->setTo($set['vacAddr'])
            ->setFrom($set['username'].'@'.$set['domain'].'/'.md5($set['resource'])) //self.user + '@' + self.domain + '/' + self.resource
            ->setType(Message::TYPE_EV_SET);

        $options = new Options($set['server'].':'.$set['port']);

         $options->setLogger($logger)
            ->setUsername($set['username'])
            ->setPassword($set['password'])
            ->setTo($set['domain']);

        $client = new Client($options);
        $client->connect();
        $client->send($message);
        
        $startTime = time();
        $i = 0;
        
        while(true) { // wait for messages
        	$messages = $client->getConnection()->receive();
        	if(strlen($messages) > 1) {
                $xml   = simplexml_load_string($messages);
                $array = json_decode(json_encode((array) $xml), true);
                $XmppReply[$i] = array($xml->getName() => $array);
        		//$XmppReply[$i] = simplexml_load_string($messages);
                $i++;
        	}
            if(((time()-$startTime) > 5) or ($i>2)) {
                break;
            }
        }
        
        //print_r($XmppReply);
        
        if (!isset($XmppReply)) { // check if there was a reply
            IPS_LogMessage("Ecovacs Deebot", "XMPP: No responce from robot within 5 seconds after connection");
            return false;
        }
        if (!is_array($XmppReply)) { // check if the reply is a array()
            $reply = print_r(htmlspecialchars($XmppReply),true);
            IPS_LogMessage("Ecovacs Deebot", "XMPP: Invailid reply from robot. Received reply: ".$reply);
            return false;
        }
        if (!(count($XmppReply) > 1)) { // if it is correctly answered there should be 2 messages
            $reply = print_r(htmlspecialchars($XmppReply),true);
            IPS_LogMessage("Ecovacs Deebot", "XMPP: Invailid reply from robot. Received reply: ".$reply);
            return false;
        }
        
        //print_r($XmppReply);
        
        if ($XmppReply[0]['iq']['@attributes']['type'] == 'result') {
            $return = $XmppReply[1]['iq']['query']['ctl'];
        }
        
        print_r($return);
        
        $client->disconnect();
        
        return $return;
    }
}

?>