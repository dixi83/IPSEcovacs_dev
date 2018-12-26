<?php

    require_once(__DIR__ . "/../libs/EcoVacsModule.php");

    class Configurator extends EcoVacs 
    {
        public function TestAndSaveLogin($country,$httpServer,$xmppserver,$username,$password) {
            EVDB_setAccountInfo($country, $httpServer, $xmppServer, $account, $password);
            if(EVDB_HTTPS_Login()) {
                echo "Login succesful and saved";
            } else {
                echo "Login failed, please check your entered account information";
            }
        }
    }

?>