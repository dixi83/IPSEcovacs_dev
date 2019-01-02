<?php

$form = <<<EOT
{
    "type": "List",
    "name": "Devices",
    "caption": "Devices",
    "rowCount": 5,
    "add": true,
    "delete": true,
    "sort": {
        "column": "Name",
        "direction": "ascending"
    },
    "columns": [{
        "caption": "InstanceID",
        "name": "InstanceID", 
        "width": "75px",
        "add": 0,
        "edit": {
            "type": "SelectInstance"
        }
    }, {
        "caption": "Name",
        "name": "Name",
        "width": "auto",
        "add": ""
    }, {
        "caption": "State",
        "name": "State",
        "width": "40px",
        "add": "New!"
    }, {
        "caption": "Temperature",
        "name": "Temperature",
        "width": "75px",
        "add": 20.0,
        "edit": {
            "type": "NumberSpinner",
            "digits": 2
        }
    }],
    "values": [{
        "InstanceID": 12435,
        "Name": "ABCD",
        "State": "OK!",
        "Temperature": 23.31,
        "rowColor": "#ff0000" //rot
    }]
}
EOT;

?>