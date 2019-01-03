<?php

$data[0] = $this->GetValue("SplitterID");
$data[1] = IPS_GetObjectIDByIdent("XMPP_Robots",$data[0]);
$data[2] = GetValueString($data[1]);

print_r()

if ((strlen($data[2]) > 2)){
    $values = substr($data[2], 1, -1);
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