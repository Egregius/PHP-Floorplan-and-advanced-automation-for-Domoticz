<?php
$start = microtime(true);
require 'secure/functions.php';
require '/var/www/authentication.php';
$d = fetchdata(0, basename(__FILE__) . ':' . __LINE__);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['device'])) {
	header('Content-Type: application/json');
	ob_start();
	try {
		if (isset($_POST['s'])) {
			storesm($_POST['device'], $_POST['s'],$_POST['m']);
		}
		ob_end_clean();
		echo json_encode(['status' => 'ok']);
	} catch (Throwable $e) {
		ob_end_clean();
		http_response_code(500);
		echo json_encode(['status' => 'error', 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
	}
	exit;
}

uasort($d, function ($a, $b) {
	if (!isset($a->t) && !isset($b->t)) return 0;
	if (!isset($a->t)) return 1;
	if (!isset($b->t)) return -1;
	return $b->t <=> $a->t;
});
?>
<!DOCTYPE html>
<html lang="nl">
<head>
	<meta charset="UTF-8"/>
	<title>Floorplan</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
	<meta name="theme-color" content="#121212">
	<link rel="icon" type="image/png" href="icon.png">
	<link rel="apple-touch-icon" href="icon.png">
	<script src="https://mynetpay.be/js/jquery-3.5.1.min.js"></script>
	<script src="https://mynetpay.be/js/jQuery.dataTables.min.js"></script>
	<style>
		:root {
			--bg-color: #121212;
			--card-bg: #1e1e1e;
			--text-main: #f0f0f0;
			--text-muted: #a0a0a0;
			--border-color: #2d2d2d;
			--accent: #cc5500;
			--accent-hover: #e65c00;
		}
		* { box-sizing: border-box; }
		body {
			margin: 0;
			padding: 10px 0;
			background-color: var(--bg-color);
			color: var(--text-main);
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
			font-size: 14px;
			width: 100%;
			overflow-x: hidden;
		}
		.header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 10px;
			padding: 0 10px;
		}
		.close-btn {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 36px;
			height: 36px;
			background: var(--card-bg);
			border: 1px solid var(--border-color);
			border-radius: 8px;
			color: var(--accent);
			text-decoration: none;
			font-weight: bold;
			font-size: 1.1rem;
		}
		.table-wrapper {
			background: var(--card-bg);
			border-top: 1px solid var(--border-color);
			border-bottom: 1px solid var(--border-color);
			width: 100%;
			overflow: hidden;
		}
		table.dataTable {
			width: 100% !important;
			border-collapse: collapse !important;
			margin: 0 !important;
			table-layout: fixed;
		}
		table.dataTable thead th {
			background: #181818;
			color: var(--accent);
			padding: 10px 6px;
			border-bottom: 1px solid var(--border-color) !important;
			font-weight: 600;
			text-transform: uppercase;
			font-size: 0.75rem;
		}
		table.dataTable thead th:nth-child(1) { width: 40%; }
		table.dataTable thead th:nth-child(2) { width: 30%; }
		table.dataTable thead th:nth-child(3) { width: 30%; }

		table.dataTable tbody tr {
			background: transparent;
			border-bottom: 1px solid var(--border-color);
			cursor: pointer;
			transition: background 0.2s;
		}
		table.dataTable tbody tr:hover {
			background: #282828;
		}
		table.dataTable tbody td {
			padding: 8px 6px;
			vertical-align: middle;
			word-wrap: break-word;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		.device-name {
			font-weight: 600;
			display: block;
			color: var(--text-main);
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		.device-time {
			font-size: 0.7rem;
			color: var(--text-muted);
			margin-top: 2px;
			display: block;
		}
		.badge {
			display: inline-block;
			padding: 3px 6px;
			border-radius: 4px;
			font-size: 0.8em;
			background: #2a2a2a;
			color: #ffffff;
			border: 1px solid var(--border-color);
			max-width: 100%;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		.modal-overlay {
			display: none;
			position: fixed;
			top:0; left:0; width:100%; height:100%;
			background: rgba(0,0,0,0.8);
			backdrop-filter: blur(4px);
			z-index: 999;
			align-items: center;
			justify-content: center;
		}
		.modal {
			background: var(--card-bg);
			border: 1px solid var(--accent);
			border-radius: 12px;
			width: 90%;
			max-width: 400px;
			padding: 20px;
			box-shadow: 0 20px 25px -5px rgba(0,0,0,0.7);
		}
		.modal-title {
			margin-top: 0;
			font-size: 1.2rem;
			color: var(--accent);
			border-bottom: 1px solid var(--border-color);
			padding-bottom: 10px;
		}
		.form-group {
			margin-bottom: 15px;
		}
		.form-group label {
			display: block;
			margin-bottom: 5px;
			color: var(--text-muted);
		}
		.form-group input {
			width: 100%;
			padding: 10px;
			border-radius: 6px;
			border: 1px solid var(--border-color);
			background: #121212;
			color: #fff;
			font-size: 1rem;
		}
		.form-group input:focus {
			outline: none;
			border-color: var(--accent);
		}
		.modal-actions {
			display: flex;
			gap: 10px;
			justify-content: flex-end;
			margin-top: 20px;
		}
		.btn {
			padding: 10px 16px;
			border-radius: 6px;
			border: none;
			cursor: pointer;
			font-weight: 600;
		}
		.btn-primary { background: var(--accent); color: #fff; }
		.btn-primary:hover { background: var(--accent-hover); }
		.btn-secondary { background: #333333; color: #fff; }
		
		.dataTables_wrapper { padding: 0 !important; }
		.dataTables_filter { padding: 8px 10px; color: var(--text-muted); text-align: left !important; }
		.dataTables_filter label { display: flex; align-items: center; gap: 8px; width: 100%; }
		.dataTables_filter input {
			background: #121212;
			border: 1px solid var(--border-color);
			color: #fff;
			padding: 6px 10px;
			border-radius: 6px;
			flex-grow: 1;
			margin-left: 0 !important;
		}
		.dataTables_filter input:focus {
			border-color: var(--accent);
			outline: none;
		}
	</style>
</head>
<body>
	<div class="header">
		<a href="floorplan.php" class="close-btn">✕</a>
		<h2>Device Status</h2>
		<div></div>
	</div>

	<div class="table-wrapper">
		<table id="table" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th>Name</th>
					<th>Status (S)</th>
					<th>Mode (M)</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$now = time();
			foreach ($d as $n => $row) {
				if(isset($row->t)) {
					$s_disp = $row->s ?? '';
					$m_disp = $row->m ?? '';
					$raw_s = $row->s ?? '';
					$raw_m = $row->m ?? '';
	
					if (str_ends_with($n, '_set')) {
						if ($row->s == 'D') $s_disp = 'Drogen';
						elseif ($row->s == 'Off') $s_disp = 'Off';
						else $s_disp = number_format((float)$row->s, 1, ',', '') . ' °C';
	
						if ($row->m == 0) $m_disp = 'Auto';
						else {
							if ($n === 'living_set' || $n === 'badkamer_set') {
								if ($row->m == 1) $m_disp = 'Pre-heating';
								elseif ($row->m == 2) $m_disp = 'Pre-heating ready';
							} else $m_disp = 'Manueel';
						}
					} elseif (str_ends_with($n, '_temp')) {
						$s_disp = number_format((float)$row->s, 1, ',', '') . ' °C';
						$m_disp = ($n == 'waskamer_temp' || $n == 'zolder_temp') ? '' : $row->m . ' %';
					} elseif ($row->d=='r') {
						if ($row->s == 0) $s_disp = 'Open';
						elseif ($row->s == 100) $s_disp = 'Gesloten';
						else $s_disp = $row->s . ' % Toe';
						$m_disp = '';
					} elseif (str_starts_with($n, '8')) {
						$s_disp = ''; $m_disp = '';
					} elseif ($n == 'luifel') {
						if ($row->s == 0) $s_disp = 'Gesloten';
						elseif ($row->s == 100) $s_disp = 'Open';
						else $s_disp = $row->s . ' % Open';
						$m_disp = ($row->m == 0) ? 'Auto' : 'Manueel';
					} elseif (in_array($n, array('eettafel', 'zithoek', 'kamer', 'waskamer', 'alex', 'lichtbadkamer'))) {
						$s_disp = ($row->s == 0) ? 'Off' : $row->s;
						if ($row->m == 0) $m_disp = '';
						elseif ($row->m == 1) $m_disp = 'Wake-up';
						elseif ($row->m == 2) $m_disp = 'Sleep';
					} elseif ($n == 'Weg') {
						if ($row->s == 0) $s_disp = 'Thuis';
						elseif ($row->s == 1) $s_disp = 'Slapen';
						elseif ($row->s == 2) $s_disp = 'Weg';
						$m_disp = 'Laatste: ' . date("d-m G:i:s", $row->m);
					} elseif ($n == 'auto') {
						$s_disp = ($row->s == 'Off') ? 'Lichten manueel' : (($row->s == 'On') ? 'Lichten automatisch' : $row->s);
						$m_disp = ($row->m == 0) ? 'Nacht' : (($row->m == 1) ? 'Dag' : '');
					} elseif ($n == 'heating') {
						$map = [0=>'0 Neutral', -2=>'-2 Active cooling', -1=>'-1 Passive cooling', 1=>'1 Airco heating', 2=>'2 Gas/Airco heating', 3=>'2 Gas heating'];
						$s_disp = $map[$row->s] ?? $row->s;
						$m_disp = '';
					} else {
						$s_disp = substr((string)($row->s ?? ''), 0, 20);
						$m_disp = substr((string)($row->m ?? ''), 0, 20);
					}
	
					$t_disp = '';
					if (isset($row->t)) {
						if ($row->t < $now - (86400 * 7 * 4)) $t_disp = date('d-m-Y', $row->t);
						elseif ($row->t < $now - 82800) $t_disp = date('d-m-Y G:i', $row->t);
						else $t_disp = date("G:i:s", $row->t);
					}
	
					echo '<tr data-device="'.htmlspecialchars($n).'" data-s="'.htmlspecialchars($raw_s).'" data-m="'.htmlspecialchars($raw_m).'">';
					echo '<td><span class="device-name">'.htmlspecialchars($n).'</span>'.($t_disp !== '' ? '<span class="device-time">'.$t_disp.'</span>' : '').'</td>';
					echo '<td>'.$s_disp.'</td>';
					echo '<td>'.$m_disp.'</td>';
					echo '</tr>';
				}
			}
			?>
			</tbody>
		</table>
	</div>

	<div class="modal-overlay" id="editModal">
		<div class="modal">
			<h3 class="modal-title" id="modalDeviceName">Device Aanpassen</h3>
			<form id="editForm">
				<input type="hidden" id="modalDevice" name="device">
				
				<div class="form-group">
					<label for="modalS">Status (s):</label>
					<input type="text" id="modalS" name="s">
				</div>
				
				<div class="form-group">
					<label for="modalM">Mode (m):</label>
					<input type="text" id="modalM" name="m">
				</div>
				
				<div class="modal-actions">
					<button type="button" class="btn btn-secondary" onclick="closeModal()">Annuleren</button>
					<button type="submit" class="btn btn-primary">Opslaan</button>
				</div>
			</form>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			var table = $('#table').DataTable({
				"bStateSave": true,
				"bPaginate": false,
				"ordering": false,
				"info": false,
				"fnInitComplete": function(){
					$("#table_filter input").focus();
				}
			});

			$('#table tbody').on('click', 'tr', function() {
				var device = $(this).data('device');
				var s = $(this).data('s');
				var m = $(this).data('m');

				if(!device) return;

				$('#modalDeviceName').text(device);
				$('#modalDevice').val(device);
				$('#modalS').val(s);
				$('#modalM').val(m);

				$('#editModal').css('display', 'flex');
			});

			$('#editForm').on('submit', function(e) {
				e.preventDefault();
				$.ajax({
					url: window.location.href,
					type: 'POST',
					dataType: 'json',
					data: $(this).serialize(),
					success: function(response) {
						if(response && response.status === 'ok') {
							closeModal();
							location.reload();
						} else {
							alert('Fout: ' + (response.message || 'Onbekende fout'));
						}
					},
					error: function(xhr) {
						var err = xhr.responseJSON ? xhr.responseJSON.message + ' (regel ' + xhr.responseJSON.line + ' in ' + xhr.responseJSON.file + ')' : xhr.responseText;
						alert('PHP Error 500:\n' + err);
					}
				});
			});
		});

		function closeModal() {
			$('#editModal').hide();
		}
	</script>
</body>
</html>