<?php

$InstanceInfo   = IPS_GetInstance($this->InstanceID)
$SplitterID     = $InstanceInfo['ConnectionID']
$RobotsDataID   = IPS_GetObjectIDIPS_GetInstance($this->InstanceID)ByIdent("XMPP_Robots",$SplitterID);
$RobotsData     = GetValue($RobotsDataID);

if ((strlen($RobotsData) > 2)){
    $values = substr($RobotsData, 1, -1);
} else {
    $values = '';
}

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
                "name": "RobotSerialNr",
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
        { "type": "Button", "caption": "View", "onClick": "print_r(\$devices);" }
    ]
}
EOT;

?>