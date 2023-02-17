<?php
if ($status=='Open'&&$d['auto']['s']=='On') {
    fgarage();
    fkeuken();
    fliving();
}
if ($status=='Open') sirene('Deur garage open');
else sirene('Deur garage dicht');