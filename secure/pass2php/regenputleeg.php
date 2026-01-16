<?php
if ($status=='Off'&&$d['regenputleeg']->s=='On') alert('regenput', 'Regenput leeg, zet alle water op stadswater.', 3600, false, true);
elseif ($status=='On'&&$d['regenputleeg']->s=='Off')  alert('regenput', 'Regenput niet meer leeg, schakel stadswater uit.', 3600, false, true);