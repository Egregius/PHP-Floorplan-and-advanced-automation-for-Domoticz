<?
if (!isset($wasmachine_power)) $wasmachine_power=10000;
if ($status<$wasmachine_power) {
	$wasmachine_power=$status;
	telegram ('Laagste vermogen wasmachine = '.$status.'W');
}