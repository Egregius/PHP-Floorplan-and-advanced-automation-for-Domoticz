<?php
if ($status=='Off'&&$d['regenputvol']->s=='On') alert('regenput', 'Regenput niet meer vol.', 3600, false, true);
elseif ($status=='On'&&$d['regenputvol']->s=='Off') alert('regenput', 'Regenput vol.', 3600, false, true);
