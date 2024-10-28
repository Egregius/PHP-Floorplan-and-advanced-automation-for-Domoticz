<?php
mset('powermeter',time());
usleep(1000000);
sw('powermeter', 'On', basename(__FILE__).':'.__LINE__,true);
