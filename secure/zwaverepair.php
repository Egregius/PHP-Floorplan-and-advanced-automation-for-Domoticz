#!/usr/bin/php
<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require '/var/www/config.php';
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
            zwavecancelaction();sleep(4);
            echo 'Refresh Node Information'.$idx.' '.$name.' '.
                zwaveRefreshNode($idx).PHP_EOL;
            sleep(60);
        }
    }
}
/**
 * Function zwaveNodeNeighbourUpdate
 * Updates the neighbours of a node
 *
 * @param int $node idx of the node
 *
 * @return string result
 */
function zwaveNodeNeighbourUpdate($node)
{
    global $domoticzurl;
    for ($k=1;$k<=5;$k++) {
        sleep(1);
        $result=file_get_contents(
            $domoticzurl.'/ozwcp/admpost.html',
            false,
            stream_context_create(
                array(
                    'http'=>array(
                        'header'=>'Content-Type: application/x-www-form-urlencoded\r\n',
                        'method'=>'POST',
                        'content'=>http_build_query(
                            array(
                                'fun'=>'reqnnu',
                                'node'=>'node'.$node
                            )
                        ),
                    ),
                )
            )
        );
        if ($result=='OK') {
            break;
        }
        sleep(1);
    }
    return $result;
}
/**
 * Function zwaveRefreshNode
 * Refreshes the config of a node
 *
 * @param int $node idx of the node
 *
 * @return string result
 */
function zwaveRefreshNode($node)
{
    global $domoticzurl;
    for ($k=1;$k<=5;$k++) {
        sleep(1);
        $result=file_get_contents(
            $domoticzurl.'/ozwcp/admpost.html',
            false,
            stream_context_create(
                array(
                    'http'=>array(
                        'header'=>'Content-Type: application/x-www-form-urlencoded\r\n',
                        'method'=>'POST',
                        'content'=>http_build_query(
                            array(
                                'fun'=>'refreshnode',
                                'node'=>'node'.$node
                            )
                        ),
                    ),
                )
            )
        );
        if ($result=='OK') {
            break;
        }
        sleep(1);
    }
    return $result;
}
/**
 * Function zwavecancelaction
 * Cancels the current action on the controller
 *
 * @return null
 */
function zwavecancelaction()
{
    global $domoticzurl;
    file_get_contents(
        $domoticzurl.'/ozwcp/admpost.html',
        false,
        stream_context_create(
            array(
                'http'=>array(
                    'header'=>'Content-Type: application/x-www-form-urlencoded\r\n',
                    'method'=>'POST',
                    'content'=>http_build_query(
                        array(
                            'fun'=>'cancel'
                        )
                    ),
                ),
            )
        )
    );
}
/**
 * Function zwaveHasnodefailed
 * Tries to revive a dead node
 *
 * @param int $node idx of the node
 *
 * @return null
 */
function zwaveHasnodefailed($node)
{
    global $domoticzurl;
    for ($k=1;$k<=5;$k++) {
        sleep(1);
        $result=file_get_contents(
            $domoticzurl.'/ozwcp/admpost.html',
            false,
            stream_context_create(
                array(
                    'http'=>array(
                        'header'=>'Content-Type: application/x-www-form-urlencoded\r\n',
                        'method'=>'POST',
                        'content'=>http_build_query(
                            array(
                                'fun'=>'hnf',
                                'node'=>'node'.$node
                            )
                        ),
                    ),
                )
            )
        );
        if ($result=='OK') {
            break;
        }
        sleep(1);
    }
}