<?php
mset('powermeter',time());
storemode('powermeter', 2400, basename(__FILE__).':'.__LINE__);
usleep(1000000);
if (mget('avg')<1000) sw('powermeter', 'On', basename(__FILE__).':'.__LINE__,true);
