<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
error_reporting(E_ALL);
ini_set("display_errors", "on");
require 'secure/functions.php';
echo 'Playkodi';
$version=116;
print_r($_REQUEST);
telegram('Play kodi executed');
echo 'ok';
telegram(print_r($_REQUEST,true));
if (isset($_REQUEST['imdbid'])) {
    if (strlen($_REQUEST['imdbid'])>5) {
        if ($d['playkodi']['s']!=true) {
            echo $_REQUEST['imdbid'];
            $data=grabfile($_REQUEST['imdbid']);
            print_r($data);
            telegram('grabfile data '.print_r($data,true));
            if (isset($data['id'])) {
                store('playkodi', true);
                shell_exec('python3 secure/lgtv.py -c send-message -a "Starting '.str_replace('nfs://'.$nasip.'/volume1/files/', '', $data['file']).'" '.$lgtvip.' > /dev/null 2>&1 &');
                kodiplay($data['profile'], $data['mediatype'], $data['id'], $data['file']);
                store('playkodi', false);
            }
        }
    }
}
sleep(5);
store('playkodi', false);
function grabfile($id)
{
    global $version;
    $data=array();
    $databases=array('FilmsKodi'.$version,'FilmsTobi'.$version,'FilmsAlex'.$version);
    foreach ($databases as $database) {
        $db=new mysqli('egregius.be', 'home', 'H0m€', $database);
        if ($db->connect_errno>0) {
            die('Unable to connect to movie database ['.$db->connect_error.']');
        }
        $query="SELECT idMovie,c22 FROM movie_view WHERE `uniqueid_value` = '$id'";
        if (!$result=$db->query($query)) {
            die('There was an error running the query ['.$query.' - '.$db->error.']');
        }
        if ($result->num_rows>0) {
            while ($row=$result->fetch_assoc()) {
                $data['id']=$row['idMovie'];
                $data['file']=$row['c22'];
            }
            $result->free();
            $data['mediatype']='movie';
            $data['profile']=str_replace('Films', '', str_replace($version, '', $database));
            return $data;
            break;
        }
    }
    $db=new mysqli('egregius.be', 'home', 'H0m€', 'FilmsKodi'.$version);
    if ($db->connect_errno>0) {
        die('Unable to connect to movie database ['.$db->connect_error.']');
    }
    $query="SELECT idEpisode,c18 FROM episode_view WHERE `uniqueid_value` = '$id'";
    if (!$result=$db->query($query)) {
        die('There was an error running the query ['.$query.' - '.$db->error.']');
    }
    if ($result->num_rows>0) {
        while ($row=$result->fetch_assoc()) {
            $data['id']=$row['idEpisode'];
            $data['file']=$row['c18'];
        }
        $result->free();
        $data['mediatype']='episode';
        $data['profile']='Wij';
        return $data;
    }
}
function kodiplay($profile,$mediatype,$kodiid,$file)
{
    global $d;
    $ctx=stream_context_create(array('http'=>array('timeout'=>2)));
    $kodireply="";
    for ($k=1;$k<=1000;$k++) {
        if ($d['nas']['s']!='On') {
            shell_exec('secure/wakenas.sh > /dev/null 2>&1 &');
            sleep(2);
        } else {
            break;
        }
    }
    if ($profile=='Kodi') {
        $profile='Wij';
    }
    if ($d['tv']['s']!='On') {
        sw('tv', 'On');
    }
    if ($d['denon']['s']!='On') {
        sw('denon', 'On');
    }
    if ($d['keuken']['s']!='Off') {
        sw('keuken', 'Off');
    }
    if ($d['nvidia']['s']!='On') {
        sw('nvidia', 'On');
    }

    if ($d['lgtv']['s']!='On') {
        shell_exec('python3 secure/lgtv.py -c on -a '.$lgtvmac.' '.$lgtvip.' > /dev/null 2>&1 &');
        sw('lgtv', 'On');
        sleep(2);
    }
    for ($k=1;$k<=1000;$k++) {
        if ($profile!='None') {
            $loadedprofile=@json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","method":"Profiles.GetCurrentProfile","id":1}', false, $ctx), true);
            if ($profile!==$loadedprofile['result']['label']) {
                $profilereply=@kodi('{"jsonrpc":"2.0","method":"Profiles.LoadProfile","params":{"profile":"'.$profile.'"},"id":1}');
                sleep(2);
            } else {
                $startreply=@kodi('{"jsonrpc":"2.0","id":"1","method":"Player.Open","params":{"item":{"file":"'.$file.'"}}}');
                telegram('Startreply '.$startreply);
                $info = @json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","method":"VideoLibrary.Get'.$mediatype.'Details","id":1,"params":['.$kodiid.',["resume"]]}', false, $ctx), true);
                if (!empty($info['result'][$mediatype.'details']['resume']['position'])) {
                    $position=floor((($info['result'][$mediatype.'details']['resume']['position']-90)/$info['result'][$mediatype.'details']['resume']['total'])*100);
                    if ($position>0) {
                        @file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","method":"Player.Seek","id":1,"params":[1,'.$position.']}', false, $ctx);
                    }
                }
                break;
            }
        } else {
            sleep(2);
        }
    }
}