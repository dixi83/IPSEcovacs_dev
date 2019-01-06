<?php

$values = EVCF GetRobotInfo();

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
                "width": "20px",
                "visible": false
            }, {
                "caption": "InstanceID",
                "name": "InstanceID", 
                "width": "75px",
                "visible": true
            }, {
                "caption": "Name",
                "name": "RobotName",
                "width": "auto",
                "visible": true,
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
        { "type": "Button", "caption": "View Array (debug)", "onClick": "print_r(\$devices);" },
        { "type": "Button", "caption": "Save Robot Name", "onClick": "SetRobotInfo(json_encode(\$devices));" },
        { "type": "Button", "caption": "Create Instance", "onClick": "CreateRobotInstance(json_encode(\$devices));" }
    ]
}
EOT;

?>