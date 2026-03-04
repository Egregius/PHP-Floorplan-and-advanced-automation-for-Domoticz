<?php
session_start();
$configPath = __DIR__ . '/log_config.json';
$config	 = file_exists($configPath) ? json_decode(file_get_contents($configPath), true) : null;
if (!is_array($config)) $config = ['logs' => []];
if (!isset($_SESSION['domo_paths'])){
	$_SESSION['domo_paths'] = [];
	foreach ($config['logs'] ?? [] as $entry){
		if (!($entry['active'] ?? false)) continue;
		$files = ($entry['type'] === 'folder')
			? (glob($entry['path'] . $entry['pattern']) ?: [])
			: [$entry['path']];
		foreach ($files as $f){
			if ($entry['type'] === 'folder' && in_array(basename($f), $entry['exclude'] ?? [])) continue;
			$_SESSION['domo_paths'][$f] = ['limit' => (int)$entry['limit'], 'format' => $entry['format'] ?? 'domotica'];
	 }
 }
}
function parseTs(string $line): int{
	if (preg_match('/^(\d{1,2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $line, $m))
		return (int)mktime((int)$m[3],(int)$m[4],(int)$m[5],(int)$m[2],(int)$m[1],(int)date('Y'));
	if (preg_match('/(\d{4})\/(\d{2})\/(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $line, $m))
		return (int)(strtotime("{$m[1]}-{$m[2]}-{$m[3]}{$m[4]}:{$m[5]}:{$m[6]}") ?: 0);
	if (preg_match('/(\d{2})\/(\w{3})\/(\d{4}):(\d{2}):(\d{2}):(\d{2})/', $line, $m))
		return (int)(strtotime("{$m[1]}{$m[2]}{$m[3]}{$m[4]}:{$m[5]}:{$m[6]}") ?: 0);
	if (preg_match('/(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2})/', $line, $m))
		return (int)(strtotime($m[1]) ?: 0);
	return 0;
}
function fmtTs(int $ts): string{
	if (!$ts) return '';
	return date('Ymd') === date('Ymd', $ts) ? date('H:i:s', $ts) : date('d/m H:i', $ts);
}
if (isset($_GET['action'])){
	header('Content-Type: application/json');
	header('Cache-Control: no-store');
	if ($_GET['action'] === 'update_selection'){
		$d = json_decode(file_get_contents('php://input'), true);
		if ($d['checked'])
			$_SESSION['domo_paths'][$d['path']] = ['limit' => (int)$d['limit'], 'format' => $d['format']];
		else
			unset($_SESSION['domo_paths'][$d['path']]);
		echo json_encode(['ok' => true]);exit;
 }
	if ($_GET['action'] === 'group_selection'){
		$d = json_decode(file_get_contents('php://input'), true);
		foreach ($config['logs'] ?? [] as $entry){
			if ($entry['name'] !== $d['group']) continue;
			$files = ($entry['type'] === 'folder')
				? (glob($entry['path'] . $entry['pattern']) ?: [])
				: [$entry['path']];
			foreach ($files as $f){
				if ($entry['type'] === 'folder' && in_array(basename($f), $entry['exclude'] ?? [])) continue;
				if ($d['mode'] === 'all')
					$_SESSION['domo_paths'][$f] = ['limit' => (int)$entry['limit'], 'format' => $entry['format'] ?? 'domotica'];
				else
					unset($_SESSION['domo_paths'][$f]);
		 }
	 }
		echo json_encode(['ok' => true]);exit;
 }
	if ($_GET['action'] === 'list'){
		$tree = [];
		foreach ($config['logs'] ?? [] as $entry){
			$group = ['name' => $entry['name'], 'limit' => (int)$entry['limit'],
					 'format' => $entry['format'] ?? 'domotica', 'children' => []];
			$files = ($entry['type'] === 'folder')
				? (glob($entry['path'] . $entry['pattern']) ?: [])
				: [$entry['path']];
			foreach ($files as $f){
				if ($entry['type'] === 'folder' && in_array(basename($f), $entry['exclude'] ?? [])) continue;
				if (!file_exists($f)) continue;
				$mt = filemtime($f);
				$group['children'][] = [
					'name'	 => ($entry['type'] === 'folder') ? basename($f) : $entry['name'],
					'path'	 => $f,
					'limit'	=> (int)$entry['limit'],
					'format' => $entry['format'] ?? 'domotica',
					'active' => isset($_SESSION['domo_paths'][$f]),
					'mtime'	=> date('Ymd') == date('Ymd', $mt) ? date('H:i:s', $mt) : date('d/m H:i', $mt),
					'mtime_ts' => $mt,
				];
		 }
			$tree[] = $group;
	 }
		echo json_encode($tree);exit;
 }
	if ($_GET['action'] === 'read'){
		$since = max(0, (int)($_GET['since'] ?? 0));
		$isDelta = $since > 0;
		$grace = $since - 10;
		$out = [];
		foreach ($_SESSION['domo_paths'] ?? [] as $path => $meta){
			if (!file_exists($path)) continue;
			$limit = $isDelta ? 500 : max(1, min(200000, $meta['limit']));
			$raw = shell_exec('tail -n ' . (int)$limit . ' ' . escapeshellarg($path));
			if (!$raw) continue;
			foreach (explode("\n", trim($raw)) as $line){
				$line = trim($line);
				if ($line === '') continue;
				$ts = parseTs($line);
				if ($isDelta && $ts > 0 && $ts < $grace) continue;
				$out[] = ['c' => $line, 'f' => basename($path), 'p' => $path,
						 't' => $ts, 'd' => fmtTs($ts), 'x' => $meta['format']];
		 }
	 }
		usort($out, fn($a, $b) => $b['t'] <=> $a['t']);
		echo json_encode(['lines' => $out]);exit;
 }
}
?><!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Domo Logs</title>
<style>
:root{
	--bg:	 #020617;
	--surface: #070e1c;
	--border: #182236;
	--muted: #4a5e78;
	--dim:	 #7d94ab;
	--text:	#c5d5e5;
	--accent: #f5a623;
	--green: #2ddc8e;
	--red:	 #f06060;
	--blue:	#5ba3f5;
	--purple: #9e78f0;
	--orange: #fb923c;
	--mono:	'JetBrains Mono','Fira Code','Cascadia Code','Consolas',monospace;
	--row-h: 24px;
	--cols:	82px 1fr;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%;overflow:hidden}
body{background:var(--bg);color:var(--text);font-family:var(--mono);font-size:14px}
#app{display:flex;flex-direction:column;height:100vh}
#hdr{background:var(--surface);border-bottom:1px solid var(--border);padding:8px 14px;display:flex;align-items:center;gap:10px;flex-shrink:0;z-index:20}
#body{display:flex;flex:1;overflow:hidden}
#sb{width:230px;background:var(--surface);border-right:1px solid var(--border);overflow-y:auto;flex-shrink:0;padding:12px 10px}
#wrap{flex:1;overflow-y:auto;position:relative}
#colHdr{position:sticky;top:0;z-index:10;background:var(--bg);border-bottom:1px solid var(--border);display:grid;grid-template-columns:var(--cols);gap:8px;padding:3px 14px;font-size:8px;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:var(--muted)}
#colHdr > div{min-width:0;overflow:hidden;}
#vsOuter{position:relative}
#vsInner{position:absolute;left:0;right:0;top:0;will-change:transform}
.log-row{display:grid;grid-template-columns:var(--cols);gap:8px;padding:2px 14px;height:var(--row-h);border-bottom:1px solid rgba(24,34,54,.6);align-items:center;overflow:hidden;contain:layout style;border-left:2px solid transparent;transition:background .07s;cursor:pointer;}
.log-row > div{min-width:0;overflow:hidden;}
.ri-green{border-left-color:rgba(45,220,142,.55)}
.ri-green:hover{background:rgba(45,220,142,.04)}
.ri-red	{border-left-color:rgba(240,96,96,.65);background:rgba(240,96,96,.035)}
.ri-red:hover{background:rgba(240,96,96,.08)}
.ri-orange{border-left-color:rgba(251,146,60,.55);background:rgba(251,146,60,.025)}
.ri-orange:hover{background:rgba(251,146,60,.06)}
.ri-yellow{border-left-color:rgba(245,200,50,.45)}
.ri-yellow:hover{background:rgba(245,200,50,.04)}
.ri-blue{border-left-color:rgba(91,163,245,.45)}
.ri-blue:hover{background:rgba(91,163,245,.04)}
.ri-purple{border-left-color:rgba(158,120,240,.45)}
.ri-purple:hover{background:rgba(158,120,240,.04)}
.ri-dim	{border-left-color:rgba(74,94,120,.35)}
.ri-dim:hover	{background:rgba(255,255,255,.02)}
.is-new{animation:flashIn 4s ease-out forwards}
@keyframes flashIn{0%{background:rgba(245,166,35,.18)} 100%{background:transparent}}
.ts-now{color:#fff;font-weight:700}
.ts-fresh{color:#b8d8ff;font-weight:600}
.ts-warm{color:#7aa8cc}
.ts-cool{color:#5a7a95}
.ts-older{color:var(--muted)}
.mc{overflow:hidden;white-space:nowrap;text-overflow:ellipsis;color:var(--text);line-height:1.4}
.hl{background:rgba(245,166,35,.36);color:#fff;border-radius:2px;padding:0 1px}
h1{color:var(--accent);font-size:15px;font-weight:900;font-style:italic;letter-spacing:-.02em;text-transform:uppercase;white-space:nowrap}
#chip{font-size:8px;padding:2px 7px;border-radius:3px;border:1px solid var(--green);color:var(--green);font-weight:900;letter-spacing:.12em;white-space:nowrap}
#lc{color:var(--muted);font-size:9px;white-space:nowrap}
input[type=text]{background:var(--bg);border:1px solid var(--border);color:var(--text);font-family:var(--mono);font-size:11px;padding:5px 14px;border-radius:20px;width:200px;min-width:50%;outline:none;transition:border-color .15s}
input[type=text]:focus{border-color:var(--accent)}
select{background:var(--bg);border:1px solid var(--border);color:var(--accent);font-family:var(--mono);font-size:10px;font-weight:800;padding:5px 10px;border-radius:20px;outline:none;cursor:pointer}
.btn{background:transparent;border:1px solid var(--border);color:var(--dim);font-family:var(--mono);font-size:8px;font-weight:800;padding:5px 10px;border-radius:4px;cursor:pointer;text-transform:uppercase;letter-spacing:.06em;transition:border-color .2s,color .2s;white-space:nowrap}
.btn:hover{border-color:var(--accent);color:var(--accent)}
input[type=checkbox]{accent-color:var(--accent)}
.flabel{display:flex;align-items:center;justify-content:space-between;padding:3px 6px 3px 0;cursor:pointer;border-radius:3px;transition:background .1s;min-height:28px;}
.flabel:hover{background:rgba(255,255,255,.03)}
.fdot{width:6px;height:6px;border-radius:50%;display:inline-block;flex-shrink:0;margin-right:7px}
.tf-n{animation:tfN 3s ease-out forwards}
@keyframes tfN{0%{color:#fff!important;text-shadow:0 0 14px var(--accent)} 35%{color:var(--accent)!important} 100%{color:inherit!important;text-shadow:none}}
#jumpBtn{position:fixed;bottom:16px;right:16px;background:var(--accent);color:#000;border:none;font-family:var(--mono);font-weight:800;font-size:9px;padding:6px 14px;border-radius:20px;cursor:pointer;opacity:0;pointer-events:none;transition:opacity .22s,transform .22s;transform:translateY(6px);text-transform:uppercase;letter-spacing:.06em;box-shadow:0 4px 22px rgba(245,166,35,.45)}
#jumpBtn.vis{opacity:1;pointer-events:all;transform:translateY(0)}
::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
@media (max-width: 700px){
	:root{--row-h: 28px;--cols: 58px 1fr;}
	body{font-size:12px}
	#sb{position:fixed;left:0;right:0;bottom:0;width:100% !important;height:70vh;border-right:none;border-top:1px solid var(--border);border-radius:16px 16px 0 0;transform:translateY(100%);transition:transform .28s cubic-bezier(.4,0,.2,1);z-index:150;padding:0 14px calc(16px + env(safe-area-inset-bottom));overflow-y:auto;}
	#sb.open{transform:translateY(0)}
	#sb::before{content:'';display:block;width:40px;height:4px;background:var(--border);border-radius:2px;margin:12px auto 16px;flex-shrink:0;}
	.flabel{padding:7px 4px;}
	.flabel input[type=checkbox]{width:16px;height:16px;}
	.fdot{width:8px;height:8px;margin-right:9px;}
	#sbBd{display:none;position:fixed;inset:0;z-index:149;background:rgba(0,0,0,.5);}
	#sbBd.open{display:block}
	#sbBtn{display:flex}
	#hdr{padding:6px 10px;gap:7px;}
	#fi{width:130px;font-size:11px}
	#lc{display:none}
	.log-row{padding:3px 10px;font-size:12px}
	#colHdr{padding:3px 10px;font-size:7px}
	#jumpBtn{bottom:calc(16px + env(safe-area-inset-bottom))}
}
@media (min-width: 701px){
	#sbBtn{display:none}
	#sbBd{display:none !important}
}
#linePop{display:none;position:fixed;inset:0;z-index:200;align-items:flex-end;justify-content:stretch;background:rgba(0,0,0,.55);}
#linePop.open{display:flex}
#linePop .lp-sheet{background:var(--surface);border-top:1px solid var(--border);border-radius:16px 16px 0 0;width:100%;padding:0 16px calc(20px + env(safe-area-inset-bottom));max-height:80vh;overflow-y:auto;animation:sheetUp .2s ease-out;}
@keyframes sheetUp{from{transform:translateY(40px);opacity:.6} to{transform:translateY(0);opacity:1}}
#linePop .lp-handle{width:40px;height:4px;background:var(--border);border-radius:2px;margin:12px auto 14px;}
#linePop .lp-ts{font-size:9px;color:var(--muted);margin-bottom:8px;}
#linePop .lp-body{font-family:var(--mono);font-size:12px;line-height:1.7;color:var(--text);white-space:pre-wrap;word-break:break-all;}
</style>
</head>
<body>
<div id="app">
 <div id="hdr">
	<button id="sbBtn" onclick="toggleSb()" style="background:none;border:1px solid var(--border);color:var(--dim);font-size:14px;padding:4px 9px;border-radius:4px;cursor:pointer;line-height:1;flex-shrink:0;">☰</button>
	<a href="log.php"><h1>Logs</h1></a>
	<div id="chip">LIVE</div>
	<input type="text" id="fi" placeholder="Filter…" oninput="schedRender()">
	<span id="lc">0 lijnen</span>
	<div style="margin-left:auto;display:flex;gap:8px;align-items:center">
	 <select id="ivl" onchange="restartIvl()">
		<option value="0">PAUSED</option>
		<option value="2000" selected>2 s</option>
		<option value="5000">5 s</option>
		<option value="10000">10 s</option>
	 </select>
	 <button class="btn" onclick="hardReset()">Reset</button>
	</div>
 </div>
 <div id="body">
	<div id="sbBd" onclick="toggleSb()"></div>
	<div id="sb"></div>
	<div id="wrap" onscroll="onScroll()">
	 <div id="colHdr">
		<div>Tijd</div><div>Bericht</div>
	 </div>
	 <div id="vsOuter"><div id="vsInner"></div></div>
	</div>
 </div>
</div>
<button id="jumpBtn" onclick="jumpTop()">▲ Live top</button>
<div id="linePop" onclick="closeLinePop()">
 <div class="lp-sheet" onclick="event.stopPropagation()">
	<div class="lp-handle"></div>
	<div class="lp-ts" id="lpTs"></div>
	<div class="lp-body" id="lpBody"></div>
 </div>
</div>
<script>
'use strict';
const IMAP ={
	'🟢':['ri-green', '#2ddc8e'], '✅':['ri-green', '#2ddc8e'], '📈':['ri-green','#2ddc8e'],
	'🔴':['ri-red', '#f06060'], '❌':['ri-red', '#f06060'], '🔥':['ri-red', '#f06060'],
	'🟠':['ri-orange','#fb923c'], '⚡':['ri-orange','#fb923c'],
	'⚠️':['ri-orange','#fb923c'], '⚠': ['ri-orange','#fb923c'],
	'🟡':['ri-yellow','#f5c83a'], '💡':['ri-yellow','#f5c83a'],
	'🔵':['ri-blue', '#5ba3f5'], '💾':['ri-blue', '#5ba3f5'], '🌡':['ri-blue', '#5ba3f5'],
	'🟣':['ri-purple','#9e78f0'], '🌀':['ri-purple','#9e78f0'],
	'🕒':['ri-dim', '#7d94ab'], '⚪':['ri-dim', '#7d94ab'], '⚫':['ri-dim','#4a5e78'],
};
const IDEF = ['ri-dim','#7d94ab'];
function iStyle(ic){return IMAP[ic] || IDEF;}
const pArr = [], pMap = Object.create(null);
function intern(p){if (pMap[p] === undefined){pMap[p] = pArr.length;pArr.push(p);} return pMap[p];}
function djb2(s){let h=5381;for(let i=0;i<s.length;i++) h=(Math.imul(h,33)^s.charCodeAt(i))>>>0;return h;}
let timer, allLines = [], hashes = new Set();
let userScrolled = false, firstFetch = true, busy = false;
let highTs = 0, nowSec = 0, renderTmr = null;
let viewIdx = new Int32Array(0), viewCnt = 0, dirty = true;
const ROW_H = 24, OVER = 8;
let vsTop = 0, rafPend = false, hdrH = 0, vpH = 0, layoutDirty = true;
function parseLine(line){
	const raw = line.c;
	let icon = '';
	const firstTab = raw.indexOf('\t');
	if (firstTab !== -1){
		const secondTab = raw.indexOf('\t', firstTab + 1);
		const candidate = (secondTab !== -1 ? raw.slice(firstTab+1, secondTab) : raw.slice(firstTab+1)).trim();
		if (/^\p{Emoji_Presentation}$/u.test(candidate) || /^\p{Extended_Pictographic}$/u.test(candidate))
			icon = candidate;
 }
	if (!icon){
		const noTs = raw.replace(/^\d{1,2}-\d{2} \d{2}:\d{2}:\d{2}(?:\.\d+)?\s*/, '');
		const em = noTs.match(/^(\p{Emoji_Presentation}|\p{Extended_Pictographic})/u);
		if (em) icon = em[1];
 }
	line.parsed ={icon};
}
function tsC(t){
	if (!t) return 'ts-older';
	const a = nowSec - t;
	return a < 30 ? 'ts-now' : a < 120 ? 'ts-fresh' : a < 600 ? 'ts-warm' : a < 3600 ? 'ts-cool' : 'ts-older';
}
function schedRender(){clearTimeout(renderTmr);renderTmr = setTimeout(() =>{dirty=true;renderVs();}, 160);}
function getLayout(){
	if (layoutDirty){
		hdrH = document.getElementById('colHdr').offsetHeight;
		vpH = document.getElementById('wrap').clientHeight - hdrH;
		layoutDirty = false;
 }
	return{hdrH, vpH};
}
new ResizeObserver(() =>{layoutDirty = true;}).observe(document.getElementById('wrap'));
function onScroll(){
	vsTop = document.getElementById('wrap').scrollTop;
	const atTop = vsTop <= 5;
	document.getElementById('jumpBtn').classList.toggle('vis', !atTop);
	if (!atTop && !userScrolled){userScrolled = true; setChip(false);}
	if ( atTop && userScrolled){userScrolled = false;setChip(true); fetchLogs();}
	if (!rafPend){rafPend = true;requestAnimationFrame(() =>{rafPend = false;paintVs();});}
}
function rebuildIdx(){
	if (!dirty) return;
	const f = document.getElementById('fi').value.toLowerCase();
	const n = allLines.length;
	if (viewIdx.length < n) viewIdx = new Int32Array(n);
	viewCnt = 0;
	for (let i = 0;i < n;i++){
		if (f && !allLines[i].c.toLowerCase().includes(f)) continue;
		viewIdx[viewCnt++] = i;
 }
	dirty = false;
}
function renderVs(){
	rebuildIdx();
	document.getElementById('vsOuter').style.height = (viewCnt * ROW_H) + 'px';
	document.getElementById('lc').textContent = allLines.length + ' lijnen';
	paintVs();
}
function paintVs(){
	if (dirty) rebuildIdx();
	const{hdrH: hh, vpH: vh} = getLayout();
	const s0	 = Math.max(0, vsTop - hh);
	const first = Math.max(0, Math.floor(s0 / ROW_H) - OVER);
	const last = Math.min(viewCnt, first + Math.ceil(vh / ROW_H) + OVER * 2);
	const needed = last - first;

	const inner = document.getElementById('vsInner');
	const filt = document.getElementById('fi').value.toLowerCase();
	inner.style.top = (first * ROW_H) + 'px';

	while (inner.children.length > needed) inner.removeChild(inner.lastChild);
	for (let i = 0;i < needed;i++){
		const line = allLines[viewIdx[first + i]];
		if (i < inner.children.length) updRow(inner.children[i], line, filt, i);
		else inner.appendChild(mkRow(line, filt, i));
 }
}
const rCache = [];
function rc(i){if (!rCache[i]) rCache[i] ={};return rCache[i];}
function mkRow(line, filt, i){
	const row = document.createElement('div');
	row.appendChild(document.createElement('div'));
	row.appendChild(document.createElement('div'));
	row.children[0].style.cssText = 'white-space:nowrap;font-variant-numeric:tabular-nums;flex-shrink:0;';
	row.children[1].className = 'mc';
	row.addEventListener('click', () =>{
//		if (isMobile() || row.children[1].scrollWidth > row.children[1].clientWidth){
			openLinePop(line);
//	 }
 });
	rCache[i] ={};
	updRow(row, line, filt, i);
	return row;
}
function updRow(row, line, filt, ri){
	const p = line.parsed;
	const ch = row.children;
	const c = rc(ri);
	const [rCls] = iStyle(p.icon);
	const wantCls = 'log-row ' + rCls + (line.isNew ? ' is-new' : '');
	if (row.className !== wantCls) row.className = wantCls;
	const dispTs = isMobile() && line.d && line.d.length === 8 ? line.d.slice(0,5) : line.d;
	const tc = tsC(line.t);
	if (ch[0].className !== tc)	 ch[0].className = tc;
	if (ch[0].textContent !== dispTs) ch[0].textContent = dispTs;
	const raw	 = line.c;
	const lineStr = raw.replace(/^\d{1,2}-\d{2} \d{2}:\d{2}:\d{2}(?:\.\d+)?[\t ]/, '').trimStart();
	const lKey	= lineStr + filt;
	if (c.lk !== lKey){
		ch[1].innerHTML = filt ? hilite(lineStr, filt) : esc(lineStr);
		c.lk = lKey;
 }
}
async function fetchTree(hlPath){
	console.log('fetchtree')
	try{
		const data = await (await fetch('?action=list')).json();
		if (!Array.isArray(data)) return;
		const now = Math.floor(Date.now() / 1000);
		let html = '';
		data.forEach(g =>{
			html += `<div style="margin-bottom:18px">
			 <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;padding:0 2px">
				<span style="color:var(--muted);font-size:8px;font-weight:900;text-transform:uppercase;letter-spacing:.12em">${esc(g.name)}</span>
				<div style="display:flex;gap:8px">
				 <button onclick="grpAct('${esc(g.name)}','all')"
					style="background:none;border:none;color:var(--muted);font-family:var(--mono);font-size:8px;font-weight:800;text-transform:uppercase;cursor:pointer"
					onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--muted)'">All</button>
				 <button onclick="grpAct('${esc(g.name)}','none')"
					style="background:none;border:none;color:var(--muted);font-family:var(--mono);font-size:8px;font-weight:800;text-transform:uppercase;cursor:pointer"
					onmouseover="this.style.color='var(--red)'" onmouseout="this.style.color='var(--muted)'">None</button>
				</div>
			 </div>
			 <div style="border-left:1px solid var(--border);padding-left:8px">`;
			g.children.forEach(f =>{
				const col = '#5ba3f5';
				const hit = f.path === hlPath;
				const age = now - (f.mtime_ts || 0);
				const mtC = getAgeColor(age);
				html += `<label class="flabel">
				 <div style="display:flex;align-items:center;overflow:hidden;min-width:0;flex:1">
					<span class="fdot" style="background:${col}"></span>
					<input type="checkbox" onchange="selFile(this)"
						 value="${f.path}" data-limit="${f.limit}" data-format="${f.format}"
						 ${f.active?'checked':''} style="margin-right:6px;flex-shrink:0;cursor:pointer">
					<span class="${hit?'tf-n':''}" style="font-size:10px;color:${f.active?'var(--text)':'var(--muted)'}">${esc(f.name)}</span>
				 </div>
				 <span class="${hit?'tf-t':''}" style="font-size:16px;flex-shrink:0;margin-left:8px;color:${mtC}">${f.mtime}</span>
				</label>`;
		 });
			html += '</div></div>';
	 });
		document.getElementById('sb').innerHTML = html;
 } catch(e){console.error('fetchTree', e);}
}
const getAgeColor = (age) =>{
 const clamp = (val, min, max) => Math.max(min, Math.min(max, val));
 if (age <= 60){
	const ratio = clamp(age / 60, 0, 1);
	const saturation = 100 - (ratio * 100);
	const lightness = 50 + (ratio * 50);
	return `hsl(60, ${saturation}%, ${lightness}%)`;
 }
 else{
	const ratio = clamp((age - 60) / (900 - 60), 0, 1);
	const lightness = 100 - (ratio * 70);
	return `hsl(0, 0%, ${lightness}%)`;
 }
};
async function fetchLogs(){
	if (userScrolled || busy) return;
	busy = true;
	try{
		const data = await (await fetch(`?action=read&since=${highTs}&_cb=${Date.now()}`)).json();
		if (!data.lines) return;
		nowSec = Math.floor(Date.now() / 1000);
		let hasNew = false, tpath = null;
		const nw = [];
		for (const r of data.lines){
			const h = djb2(r.c + '|' + r.p);
			if (hashes.has(h)) continue;
			hashes.add(h);r.h = h;r.pi = intern(r.p);delete r.p;
			r.isNew = !firstFetch;
			if (r.isNew) tpath = pArr[r.pi];
			if (r.t > highTs) highTs = r.t;
			parseLine(r);nw.push(r);hasNew = true;
	 }
		if (hasNew){
			if (firstFetch || nw.length > 500){
				allLines = allLines.concat(nw);
				allLines.sort((a, b) => b.t - a.t);
		 } else{
				for (const nl of nw){
					let lo = 0, hi = allLines.length;
					while (lo < hi){const m=(lo+hi)>>>1;allLines[m].t >= nl.t ? lo=m+1 : hi=m;}
					allLines.splice(lo, 0, nl);
			 }
		 }
			if (allLines.length > 20000){allLines = allLines.slice(0,20000);hashes = new Set(allLines.map(l=>l.h));}
			dirty = true;renderVs();
			fetchTree(tpath);
	 } else if (firstFetch) fetchTree();
		firstFetch = false;
 } catch(e){console.error('fetchLogs', e);}
	finally{busy = false;}
}
function setChip(live){
	const c = document.getElementById('chip');
	c.textContent	 = live ? 'LIVE' : 'PAUSED';
	c.style.borderColor = live ? 'var(--green)' : 'var(--red)';
	c.style.color	 = live ? 'var(--green)' : 'var(--red)';
}
function jumpTop(){
	userScrolled = false;setChip(true);
	document.getElementById('jumpBtn').classList.remove('vis');
	const w = document.getElementById('wrap');w.scrollTop = 0;vsTop = 0;
	fetchLogs();
}
function restartIvl(){
	clearInterval(timer);
	const v = parseInt(document.getElementById('ivl').value);
	if (v > 0) timer = setInterval(fetchLogs, v);
}
async function grpAct(name, mode){
	await fetch('?action=group_selection',{method:'POST', body:JSON.stringify({group:name, mode})});
	if (isMobile()) closeSb();
	reset();
}
async function selFile(cb){
	await fetch('?action=update_selection',{method:'POST', body:JSON.stringify({
		path:cb.value, checked:cb.checked, limit:parseInt(cb.dataset.limit), format:cb.dataset.format
 })});
	if (isMobile()) closeSb();
	reset();
}
function reset(){
	clearInterval(timer);
	allLines=[];viewIdx=new Int32Array(0);viewCnt=0;hashes.clear();
	firstFetch=true;busy=false;highTs=0;userScrolled=false;
	dirty=true;vsTop=0;rafPend=false;rCache.length=0;
	setChip(true);
	document.getElementById('wrap').scrollTop = 0;
	document.getElementById('vsInner').innerHTML = '';
	document.getElementById('vsOuter').style.height = '0px';
	document.getElementById('jumpBtn').classList.remove('vis');
	fetchLogs();fetchTree();restartIvl();
}
function hardReset(){reset();}
function esc(s){
	if (s == null) return '';
	return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
function hilite(str, term){
	if (!term || str == null) return esc(str);
	const safe = esc(String(str));
	const re = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
	return safe.replace(new RegExp(re, 'gi'), m => `<span class="hl">${m}</span>`);
}
function openLinePop(line){
	document.getElementById('lpTs').textContent = line.d || '';
	document.getElementById('lpBody').textContent = line.c;
	document.getElementById('linePop').classList.add('open');
}
function closeLinePop(){
	document.getElementById('linePop').classList.remove('open');
}
document.addEventListener('keydown', e =>{if (e.key === 'Escape') closeLinePop();});
const isMobile = () => window.innerWidth <= 700;
function toggleSb(){
	document.getElementById('sb').classList.toggle('open');
	document.getElementById('sbBd').classList.toggle('open');
}
function closeSb(){
	document.getElementById('sb').classList.remove('open');
	document.getElementById('sbBd').classList.remove('open');
}
fetchLogs();
restartIvl();
</script>
</body>
</html>
