<?php

$values = $this->GetRobotInfo();

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
        { "type": "Button", "caption": "Identify (play sound)", "onClick": "CMD_PlaySound(\$id,\$devices[\"RobotSerialNr\"]);" },
        { "type": "Button", "caption": "Save Robot Name", "onClick": "if(EVCF_SetRobotInfo(\$id,json_encode(\$devices))) { echo \"Saved succesfull\";} else { echo \"Saving Failed! No device selected\nIf selecting a device doesn't work, then reload the devicelist in the Splitter Module\"; }" },
        { "type": "Button", "caption": "Create Instance", "onClick": "EVCF_SetRobotInstance(\$id,json_encode(\$devices));" }
    ]
}
EOT;

?>