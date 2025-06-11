<?php
$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJmMTQ1ZThmNjYyNTk0Mjk5OWM2ZTUyMWNhZWY3MTUxYSIsImlhdCI6MTc0ODQwMDM0OCwiZXhwIjoyMDYzNzYwMzQ4fQ.SDUxztRFwr9p7w29LQ-_fDa5l4KB1cOTrz_riHQCFlY";
$boundary = "PIderman";
header("Content-type: multipart/x-mixed-replace; boundary=$boundary");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

set_time_limit(0);
ob_implicit_flush(true);

$url = "http://192.168.2.26:8123/api/camera_proxy/camera.eufy";

$opts = [
    "http" => [
        "method" => "GET",
        "header" => "Authorization: Bearer $token\r\n",
        "timeout" => 8
    ]
];
$ctx = stream_context_create($opts);

while (true) {
    $image = @file_get_contents($url, false, $ctx);
    if ($image === false) {
        usleep(500000); // 0.5 seconde wachten bij mislukte poging
        continue;
    }

    echo "--$boundary\r\n";
    echo "Content-Type: image/jpeg\r\n";
    echo "Content-Length: " . strlen($image) . "\r\n";
    echo "\r\n";
    echo $image . "\r\n";
    @ob_flush();
    flush();

    usleep(500000); // 0.2 seconde wachten → ca. 5 FPS
}
?>
