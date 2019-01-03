<?php

//$EcovacsSplitter = new EcovacsSplitter($this->InstanceID);
//$values = $EcovacsSplitter->GetValue("XMPP_Robots");

//if ((strlen($values) > 2)){
//    $values = substr($values, 1, -1);
//}

$values = ',
            "values": [' .$values. ']';

$form = <<<EOT
{
    "actions": [
        {
            "type": "List",
            "name": "devices",
            "caption": "Devices",
            "rowCount": 5,
            "add": false,
            "delete": false,
            "sort": {
                "column": "RobotNr",
                "direction": "ascending"
            },
            "columns": [
            {
                "caption": "#",
                "name": "RobotNr", 
                "width": "20px"
            }, {
                "caption": "InstanceID",
                "name": "InstanceID", 
                "width": "75px"
            }, {
                "caption": "Name",
                "name": "RobotName",
                "width": "auto",
                "edit": {
                    "type": "ValidationTextBox"
                }
            }, {
                "caption": "Device Serial (identifier)",
                "name": "DeviceSerialNr",
                "width": "200px",
                "visible": true
            }, {
                "caption": "XMPPaddress",
                "name": "XMPPaddress",
                "width": "0px",
                "visible": false
            }
            ]$values
            
        },
        { "type": "Button", "caption": "Ausgabe", "onClick": "print_r(\$devices);" }
    ]
}
EOT;

?>