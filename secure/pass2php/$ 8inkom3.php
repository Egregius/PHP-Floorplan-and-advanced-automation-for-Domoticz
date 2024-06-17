<?php
mset('powermeter',time());
sleep(1);
sw('powermeter', 'On', basename(__FILE__).':'.__LINE__,true);
