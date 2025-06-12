#!/usr/bin/php
<?php
$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJmMTQ1ZThmNjYyNTk0Mjk5OWM2ZTUyMWNhZWY3MTUxYSIsImlhdCI6MTc0ODQwMDM0OCwiZXhwIjoyMDYzNzYwMzQ4fQ.SDUxztRFwr9p7w29LQ-_fDa5l4KB1cOTrz_riHQCFlY";
$ha_url = "http://192.168.2.26:8123/api/services/button/press";
$data = [
    "entity_id" => "button.start_p2p_stream"
];

$options = [
    "http" => [
        "method"  => "POST",
        "header"  => "Authorization: Bearer $token\r\nContent-Type: application/json\r\n",
        "content" => json_encode($data),
        "timeout" => 30
    ]
];
$context = stream_context_create($options);
$response = @file_get_contents($ha_url, false, $context);