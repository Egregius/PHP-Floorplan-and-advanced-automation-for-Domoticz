<?php
mset('powermeter',time());
storemode('powermeter', 2000, basename(__FILE__).':'.__LINE__);
usleep(1000000);
sw('powermeter', 'On', basename(__FILE__).':'.__LINE__,true);
