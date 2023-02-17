#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
echo 'Healing Zwave network'.PHP_EOL;

$nodes=json_decode(
    file_get_contents(
        $domoticzurl.'/json.htm?type=openzwavenodes&idx='.$zwaveidx
    ),
    true
);
if (!empty($nodes['result'])) {
    foreach ($nodes['result'] as $node) {
        $idx=$node['NodeID'];$name=$node['Name'];$state=$node['State'];
        if ($state=='Dead') {
            zwavecancelaction();
            sleep(4);
            echo 'Reviving node '.$idx.' '.$name.' '.
                zwaveHasnodefailed($idx).PHP_EOL;
            sleep(60);
        } else {
            zwavecancelaction();
            sleep(4);
            echo 'Node Neighbour Update '.$idx.' '.$name.' '.
                zwaveNodeNeighbourUpdate($idx).PHP_EOL;
            sleep(60);
            /*zwavecancelaction();sleep(4);
            echo 'Refresh Node Information'.$idx.' '.$name.' '.
                zwaveRefreshNode($idx).PHP_EOL;
            sleep(60);*/
        }
    }
}
