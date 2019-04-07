#!/bin/bash
MSG="$1"
curl -s --data-urlencode "text=$MSG" "https://home.egregius.be/secure/ios.php"
