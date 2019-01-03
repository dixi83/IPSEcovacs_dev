<?php

$SplitterID = $this->GetValue("SplitterID");
$RobotData = GetValueString(IPS_GetObjectIDByIdent("XMPP_Robots",$SplitterID));

if ((strlen($RobotData) > 2)){
    $values = substr($RobotData, 1, -1);
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