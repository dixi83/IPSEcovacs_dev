<?php

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
                "width": "150px",
                "visible": true
            }, {
                "caption": "XMPPaddress",
                "name": "XMPPaddress",
                "visible": false
            }
            ],
            "values": [{
                "InstanceID": 0,
                "RobotNr": 0,
                "RobotName": "ABCD",
                "DeviceSerialNr": "KOJSDAS78IJ89H3E98",
                "XMPPaddress": "ahsdkashjd@sadkjhsajdh.com/atom",
                "rowColor": "#ff0000" //rot
            }]
        }
    ]
}
EOT;

?>