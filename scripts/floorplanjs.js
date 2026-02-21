var isSubmitting=!1,euroelec=.2698,eurogas=.092,eurowater=4.8166;const ucfirst=e=>e[0].toUpperCase()+e.slice(1);
let avgTimeOffset=0,ftime=0,htime=0,otime=0,lastAvgValue=null,forceTimes=true,fetchajax=true,d={time:0},view=null,deviceElems={},lastValues={},lastLog={},lastState={},newTime=Math.floor(Date.now() / 1000),prevnet=null,prevzon=-1,prevtotal=null,prevavg=-1
keys = ['n','c','b','a','z'];
for (const k of keys) {
    d[k] = 0;
}
keys = ['gas','gasavg','elec','elecavg','verbruik','zon','zonavg','zonref','alwayson'];
for (const k of keys) {
    const v = sessionStorage.getItem(k);
    d[k] = v !== null ? Number(v) : 0;
}
window.addEventListener('error', (e) => {
    log('JS ERROR '+ e.message);
});
window.addEventListener('unhandledrejection', (e) => {
    log('PROMISE REJECT '+ e.reason);
});
let updateQueue = new Map();
let rafScheduled = false;
function getElem(id) {
    if (!(id in deviceElems)) {
        deviceElems[id] = document.getElementById(id) || null;
    }
    return deviceElems[id];
}
function getUpdateEntry(id) {
    if (!updateQueue.has(id)) {
        updateQueue.set(id, { styles: {}, attrs: {}, classes: {} });
    }
    return updateQueue.get(id);
}
function requestTick() {
    if (!rafScheduled) {
        rafScheduled = true;
        requestAnimationFrame(processQueue);
    }
}
function processQueue() {
    updateQueue.forEach((u, id) => {
        const el = getElem(id);
        if (!el) return;
        if (u.text !== undefined) el.textContent = u.text;
        else if (u.html !== undefined) el.innerHTML = u.html;
        if (u.logs && u.logs.length > 0) {
            el.textContent += u.logs.join('\n') + '\n';
            el.scrollTop = el.scrollHeight;
        }
        for (const [prop, val] of Object.entries(u.styles)) {
            if (val === null) el.style.removeProperty(prop);
            else el.style[prop] = val;
        }
        for (const [attr, val] of Object.entries(u.attrs)) {
            el.setAttribute(attr, val);
        }
        for (const [cls, action] of Object.entries(u.classes)) {
            if (action === 'add') el.classList.add(cls);
            else el.classList.remove(cls);
        }
    });
    updateQueue.clear();
    rafScheduled = false;
}
function setText(id, text) {
    if (lastValues[id] === text) return false;
    lastValues[id] = text;
    let entry = getUpdateEntry(id);
    entry.text = text;
    delete entry.html;
    requestTick();
    return true;
}
function setHTML(id, html) {
    const key = id + ':html';
    if (lastValues[key] === html) return false;
    lastValues[key] = html;
    let entry = getUpdateEntry(id);
    entry.html = html;
    delete entry.text;
    requestTick();
    return true;
}

function setStyle(id, prop, value) {
    const key = id + ':style:' + prop;
    if (value === null) {
        if (!(key in lastValues)) return;
        delete lastValues[key];
    } else {
        if (lastValues[key] === value) return;
        lastValues[key] = value;
    }
    getUpdateEntry(id).styles[prop] = value;
    requestTick();
}

function setAttr(id, attr, value) {
    const key = id + ':attr:' + attr;
    if (lastValues[key] === value) return;
    lastValues[key] = value;
    getUpdateEntry(id).attrs[attr] = value;
    requestTick();
}

function addClass(id, cls) {
    const key = id + ':cls:' + cls;
    if (lastValues['a' + key] === cls) return;
    lastValues['a' + key] = cls;
    delete lastValues['r' + key];

    getUpdateEntry(id).classes[cls] = 'add';
    requestTick();
}

function removeClass(id, cls) {
    const key = id + ':cls:' + cls;
    if (lastValues['r' + key] === cls) return;
    lastValues['r' + key] = cls;
    delete lastValues['a' + key];

    getUpdateEntry(id).classes[cls] = 'remove';
    requestTick();
}

function setTime(){
    const date = new Date(Date.now());
	const hours = date.getHours();
	const minutes = ("0" + date.getMinutes()).slice(-2);
	const seconds = ("0" + date.getSeconds()).slice(-2);
	if(setText('time',`${hours}:${minutes}:${seconds}`)) {
		newTime=date/1000
		const rawSeconds = updateSecondsInQuarter(newTime);
		const avgTimeOffsetRaw = localStorage.getItem('avgTimeOffset');
		avgTimeOffset = Number(avgTimeOffsetRaw);
		if (avgTimeOffsetRaw === null || avgTimeOffsetRaw === "null" || !Number.isFinite(avgTimeOffset)) {
			avgTimeOffset = -10;
		}
		if (lastAvgValue !== null && lastAvgValue > 0 && d.avg < lastAvgValue) {
			const expectedResetTime = avgTimeOffset < 0 ? 900 + avgTimeOffset : avgTimeOffset;
			const minResetTime = (expectedResetTime - 10 + 900) % 900;
			const maxResetTime = (expectedResetTime + 10) % 900;
			let inWindow = false;
			if (minResetTime < maxResetTime) {
				inWindow = rawSeconds >= minResetTime && rawSeconds <= maxResetTime;
			} else {
				inWindow = rawSeconds >= minResetTime || rawSeconds <= maxResetTime;
			}
			if (inWindow) {
				const proposedOffset = rawSeconds <= 20 ? rawSeconds : rawSeconds - 900;
				const offsetDifference = proposedOffset - avgTimeOffset;
				if (Math.abs(offsetDifference) <= 3) {
					avgTimeOffset = proposedOffset;
					log(`Offset aangepast naar ${avgTimeOffset}s`);
				} else {
					avgTimeOffset += Math.sign(offsetDifference) * 3;
					log(`Offset stapsgewijs aangepast naar ${avgTimeOffset}s (doel: ${proposedOffset}s)`);
				}
				localStorage.setItem('avgTimeOffset', avgTimeOffset);
			}
		}
		lastAvgValue = d.a;
		const correctedSeconds = (rawSeconds - avgTimeOffset + 900) % 900;
		if (correctedSeconds % 5 == 0 || forceTimes) {
			drawCircle('avgtimecircle', correctedSeconds, 900, 82, 'gray');
		}

		if (d.timestamps === undefined || (newTime >= d.timestamps + 5 && newTime % 10 == 0) || forceTimes === true) {
			const items = [
				...['living_set','badkamer_set','kamer_set','alex_set','brander','luifel'],
				...['deurgarage','deurinkom','achterdeur','deurvoordeur','deurbadkamer','deurkamer','deurwaskamer','deuralex','deurwc','raamliving','raamkeuken','raamkamer','raamwaskamer','raamalex'],
				...['pirliving','pirinkom','pirhall','pirkeuken','pirgarage']
			];
			updateAllDeviceTimes(items);
			d.timestamps = newTime;
			forceTimes = false;
		}
	}
}
async function ajaxJSON(url){
    const controller=new AbortController()
    try {
        const r=await fetch(url,{cache:'no-store',signal:controller.signal})
        if (!r.ok){
			console.warn('Fetch returned status',r.status,'for',url)
			return {}
		}
        const data=await r.json()
        return data
    } catch (err){
        if (err.name==='AbortError'){
            return { aborted: true }
        }
        return { aborted: true }
    }
}
function setView(newView){
    view=newView;
    ['floorplan','floorplanothers','floorplanheating','floorplantemp'].forEach(v=>{
        const el=getElem(v);
        if(el) el.classList.remove('active');
    })
    const newEl=getElem(newView)
    if(newEl) newEl.classList.add('active')
}
function formatDate(t){return date=new Date(1e3*t),date.getDate()+"/"+(date.getMonth()+1)}
// ── Heating render helpers ────────────────────────────────────────────────────
const HEATING_ICONS = {
    '-2': { img: 'Cooling',      alt: 'Cooling',  label: 'Airco<br>cooling'   },
    '-1': { img: 'Cooling_grey', alt: 'Cooling',  label: 'Passive<br>cooling' },
     '0': { img: 'close',        alt: 'Neutral',  label: 'Neutral'            },
     '1': { img: 'Cooling_red',  alt: 'Elec',     label: 'Airco<br>heating'   },
     '2': { img: 'GasAirco',     alt: 'AircoGas', label: 'Gas-Airco<br>heating' },
     '3': { img: 'Gas',          alt: 'Gas',      label: 'Gas heating'        },
     '4': { img: 'Gas',          alt: 'Gas',      label: 'Gas heating'        },
};

function renderHeatingButton(s) {
    const cfg = HEATING_ICONS[String(s)];
    let html = '<img src="/images/arrowdown.png" class="i60" alt="Open">';
    if (cfg && s !== 0) html += `<img src="/images/${cfg.img}.png" class="i40" alt="${cfg.alt}">`;
    setHTML('heatingbutton', html);
    setHTML('oheatingbutton', html);
}

function renderHeatingRow(s) {
    const cfg = HEATING_ICONS[String(s)];
    if (!cfg) return;
    const h = s === 0 ? '40' : '60';
    const w = s === 0 ? '40' : '80';
    const html = `<img src="images/${cfg.img}.png" onclick="heating();"></td>`
               + `<td align="left" height="${h}" width="${w}px" style="line-height:18px" onclick="heating()">${cfg.label}</td>`;
    setHTML('trheating', html);
}

function renderHeatingFloorplan(s, branderState) {
    const cfg = HEATING_ICONS[String(s)];
    let html = '<img src="/images/arrowdown.png" class="i60" alt="Open">';
    if (s === 4) {
        html += branderState === 1
            ? '<img src="/images/fire_On.png" class="i40" id="branderfloorplan" alt="Gas">'
            : '<img src="/images/fire_Off.png" class="i40" alt="Gas">';
    } else if (cfg && s !== 0) {
        html += `<img src="/images/${cfg.img}.png" class="i40" alt="${cfg.alt}">`;
    }
    setHTML('heating', html);
}

// ── Switch/dimmer render helpers ──────────────────────────────────────────────
const SVG_CIRCLE_PATH = 'M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831';

function renderDimmerCircleSVG(dasharray) {
    return `<svg viewBox="0 0 36 36">`
         + `<path class="circle-bg" d="${SVG_CIRCLE_PATH}" />`
         + `<path class="circle" stroke-dasharray="${dasharray},100" d="${SVG_CIRCLE_PATH}" />`
         + `</svg>`;
}

function renderSwitchHTML(device, v, opts = {}) {
    const isOn    = (v.s > 0);
    const icon    = v.i || 'l';
    const state   = isOn ? 'On' : 'Off';

    const CONFIRM  = ['ipaddcok','mac','daikin','grohered','kookplaat','media','boseliving','bosekeuken','poort'];
    const DIRECT   = ['regenpomp','nas'];
    const SPECIAL  = ['water','regenpomp','steenterras','tuintafel','terras','tuin','auto','media','nas','zetel','grohered','kookplaat','boseliving','bosekeuken','ipaddock','mac','poort'];
    const TIMESTMP = ['water','regenpomp','auto','media','nas','mac','ipaddock','boseliving','bosekeuken','grohered','kookplaat','zetel','poort'];

    let onclick;
    if (CONFIRM.includes(device)) {
        onclick = `confirmSwitch('${device}')`;
    } else if (DIRECT.includes(device)) {
        onclick = `ajaxcontrol('${device}','sw','${isOn ? 'Off' : 'On'}');setView('floorplan');`;
    } else {
        onclick = `ajaxcontrol('${device}','sw','${isOn ? 'Off' : 'On'}')`;
    }

    let html;
    if (isOn && icon === 'l') {
        html = `<img src="/images/l_On.png" id="${device}" class="img100" />`
             + `<div class="dimmercircle" onclick="${onclick}">`
             + renderDimmerCircleSVG(100)
             + `</div>`;
    } else {
        html = `<img src="/images/${icon}_${state}.png" id="${device}" onclick="${onclick}" />`;
    }

    if (SPECIAL.includes(device)) {
        const naam = device === 'steenterras' ? 'steen' : device === 'tuintafel' ? 'hout' : device;
        html += `<br>${naam}`;
        if (TIMESTMP.includes(device)) {
            if (v.t > (newTime - 82800)) {
                const dt = new Date(v.t * 1000);
                html += `<br>${dt.getHours()}:${String(dt.getMinutes()).padStart(2,'0')}`;
                setStyle(device, 'color', '#CCC');
            } else {
                html += `<br>${formatDate(v.t)}`;
                setStyle(device, 'color', '#777');
            }
        }
    }
    return html;
}

function renderDimmerHTML(device, v) {
    const level = parseInt(v.s) || 0;
    let html;
    if (level === 0) {
        html = '<img src="/images/l_Off.png" class="img100">';
    } else {
        html = '<img src="/images/l_On.png" class="img100">'
             + `<div class="dimmercircle">`
             + renderDimmerCircleSVG(level)
             + `<div class="dimmer-percentage">${level}%</div>`
             + `</div>`;
    }
    if (device === 'terras') html += 'terras';
    return html;
}

function renderBoseHTML(device, v) {
    if (v.m === 0) return '';
    const img = (device === 'bose101' || device === 'bose106') ? 'ST30' : 'ST10';
    const state = v.s === 1 ? 'On' : 'Off';
    return `<a href="javascript:navigator_Go('floorplan.bose.php?ip=${device}');">`
         + `<img src="images/${img}_${state}.png" id="${device}" alt="bose"></a>`;
}

// ─────────────────────────────────────────────────────────────────────────────
function handleResponse(device,v){
	d[device]=v
	switch(device) {
		case 't':
			setTime()
			return
		case 'i':
			setText('info',v)
			return
		case 'd':
			setText('Tstart',v.Ts)
			setText('Srise',v.Sr)
			setText('Sset',v.Ss)
			setText('Tend',v.Te)
			setText('playlist',v.pl)
			{
				const mintemp=(Math.round(v.b.m * 100) / 100).toFixed(1);
				const maxtemp=(Math.round(v.b.x * 100) / 100).toFixed(1);
				if(setText('mint',mintemp.toString().replace(/[.]/,","))) {
					const mincolor = mintemp>0 ? berekenKleurRood(mintemp,30,10) : berekenKleurBlauw(mintemp,-5,10);
					setStyle('mint','color',mincolor)
				}
				if(setText('maxt',maxtemp.toString().replace(/[.]/,","))) {
					const maxcolor = maxtemp>0 ? berekenKleurRood(maxtemp,35,15) : berekenKleurBlauw(maxtemp,5,10);
					setStyle('maxt','color',maxcolor)
				}
			}
			if(d.buiten_temp?.s!==undefined) renderThermometer('buiten_temp',d.buiten_temp)
			return
		case 'dailyen':
			d.zonavg=v.zonavg
			d.zonref=v.zonref
			d.elecavg=v.elecavg
			d.gasavg=v.gasavg
			sessionStorage.setItem('zonavg', v.zonavg)
			sessionStorage.setItem('zonref', v.zonref)
			sessionStorage.setItem('elecavg', v.elecavg)
			sessionStorage.setItem('gasavg', v.gasavg)
			return
		case 'w':
			if(setHTML('wind',v.w.toString().replace(/[.]/,","))===true) setStyle('wind','color',berekenKleurRood(v.w,50,20))
			setAttr('icon','src',"/images/" + v.i + ".png")
			{
				const mintemp=(Math.round(v.mint * 100) / 100).toFixed(1);
				if(setText('mintemp',mintemp.toString().replace(/[.]/,","))) {
					const mincolor = mintemp>0 ? berekenKleurRood(mintemp,30,10) : berekenKleurBlauw(mintemp,-5,10);
					setStyle('mintemp','color',mincolor)
				}
				const maxtemp=(Math.round(v.maxt * 100) / 100).toFixed(1);
				if(setText('maxtemp',maxtemp.toString().replace(/[.]/,","))) {
					const maxcolor = maxtemp>0 ? berekenKleurRood(maxtemp,35,15) : berekenKleurBlauw(maxtemp,5,10);
					setStyle('maxtemp','color',maxcolor)
				}
			}
			if(setText('uv',v.uv.toString().replace(/[.]/,","))===true) setStyle('uv','color',berekenKleurRood(v.uv,11,2))
			if(setText('uvm',v.uvm.toString().replace(/[.]/,","))===true) setStyle('uv','color',berekenKleurRood(v.uvm,11,2))
			if(setText('buien',v.b)===true) setStyle('buien','color',berekenKleurBlauw(v.b,100))
			return
		case 'a':
			if(setText('avgvalue',v)===true) {
				if (shouldRedraw(prevavg,v,2500,5)||v===0) {
					setStyle('avgvalue', 'color', berekenKleurRood(v, 5000));
					drawCircle('avgcircle', v, 2500, 90, 'purple');
				}
			}
			return
		case 'elec': {
			const val = parseFloat(v);
			const euro = val * euroelec;
			const html = euro.toFixed(2).toString().replace(/[.]/, ",");
			if (d.net > 0) setStyle('elecvalue', 'color', berekenKleurRood(v, d.elecavg * 2));
			else setStyle('elecvalue', 'color', berekenKleurGroen(-v, d.elecavg * 2));
			drawCircle('eleccircle', val, d.elecavg, 90, 'purple');
			const valStr = val.toFixed(2).toString().replace(/[.]/, ",");
			setHTML('elecvalue', html + `<br><span class="units">${valStr}</span>`);
			sessionStorage.setItem('elec', v)
			return
		}
		case 'alwayson':
			setText('alwayson', v + "W");
			return
		case 'zon': {
			const val = parseFloat(v);
			const euro = val * (euroelec + 0.45);
			const html = euro.toFixed(2).toString().replace(/[.]/, ",");
			setStyle('zonvvalue', 'color', berekenKleurGroen(-val, d.zonref / 10));
			drawCircle('zonvcircle', val, d.zonref, 90, 'green');
			const valStr = val.toFixed(2).toString().replace(/[.]/, ",");
			setHTML('zonvvalue', html + `<br><span class="units">${valStr}</span>`);
			return
		}
		case 'gas': {
			const euro = v * 11.5 * eurogas;
			const html = euro.toFixed(2).toString().replace(/[.]/, ",");
			setStyle('gasvalue', 'color', berekenKleurRood(v, d.gasavg * 2000));
			drawCircle('gascircle', v, d.gasavg, 90, 'red');
			const valStr = v.toFixed(2).toString().replace(/[.]/, ",");
			setHTML('gasvalue', html + `<br><span class="units">${valStr}</span>`);
			return
		}
		case 'verlof':
			if(v.s==0)html='Normaal'
			else if(v.s==1)html='Geen school'
			else if(v.s==2)html='Verlof'
			setText('verlof',html)
			return
		case 'weg':
			if(v.s==0)setAttr('wegimg','src',"/images/Thuis.png")
			else if(v.s==1)setAttr('wegimg','src',"/images/Slapen.png")
			else if(v.s==2)setAttr('wegimg','src',"/images/weg.png")
			else if(v.s==3)setAttr('wegimg','src',"/images/Vacation.png")
			if(v.s==0){
				removeClass('zlivinga','secured')
				removeClass('zlivingb','secured')
				removeClass('zkeuken','secured')
				removeClass('zinkom','secured')
				removeClass('zgarage','secured')
				removeClass('zhalla','secured')
				removeClass('zhallb','secured')
			}else if(v.s==1){
				addClass('zlivinga','secured')
				addClass('zlivingb','secured')
				addClass('zkeuken','secured')
				addClass('zinkom','secured')
				addClass('zgarage','secured')
				removeClass('zhalla','secured')
				removeClass('zhallb','secured')
			}else if(v.s>=2){
				addClass('zlivinga','secured')
				addClass('zlivingb','secured')
				addClass('zkeuken','secured')
				addClass('zinkom','secured')
				addClass('zgarage','secured')
				addClass('zhalla','secured')
				addClass('zhallb','secured')
			}
			if(v.s==0) setStyle('poort','display','block')
			else setStyle('poort','display','none')
			return
		case 'alexslaapt': {
			let txt;
			if(v.s==1) {
				addClass('zalex','securedalex')
				txt='Alex slaapt '
			} else {
				removeClass('zalex','securedalex')
				txt='Alex wakker '
			}
			let timeHtml = '';
			if(v.t>(newTime-82800)){
				const dt=new Date(v.t*1000)
				timeHtml=dt.getHours()+':'+String(dt.getMinutes()).padStart(2,'0')
				txt+=timeHtml
			}
			setText("t"+device, timeHtml)
			return
		}
		case 'dag': {
			let s=v.s
			if (s < -10 || s > 10) s=Math.round(s)
			setText('dag',s)
			return
		}
		case 'heating':
			renderHeatingButton(v.s)
			renderHeatingRow(v.s)
			return
		case 'sirene':
			if(v.s==1)html='<img src="images/alarm_On.png" width="500px" height="auto" alt="Sirene" onclick="ajaxcontrol(\'sirene\',\'sw\',\'Off\')"><br>'+device
			else html=""
			setHTML('sirene',html)
			return
		case 'brander':
			const heatingmode=d.heating?.s ?? 0;
			const branderOn = (v.s === 1 || v.s === 'On') ? 1 : 0;
			const branderImg = branderOn ? 'fire_On' : 'fire_Off';
			const branderToggle = branderOn ? 'Off' : 'On';
			setHTML('brander', `<img src="images/${branderImg}.png" onclick="ajaxcontrol('brander','sw','${branderToggle}')">`)
			updateDeviceTime(device)
			renderHeatingFloorplan(heatingmode, branderOn)
			if (heatingmode >= 1) {
				setAttr('branderfloorplan', 'src', `/images/${branderImg}.png`)
			} else {
				setAttr('branderfloorplan', 'src', '')
			}
			return
		case 'luifel':
			if(v.s==0)html='<img src="/images/arrowgreenup.png" class="i60">'
			else if(v.s==100)html='<img src="/images/arrowgreendown.png" class="i60">'
			else html='<img src="/images/arrowdown.png" class="i60"><div class="fix center dimmerlevel" style="position:absolute;top:10px;left:-2px;width:70px;letter-spacing:4;"><font size="5" color="#CCC">'+v.s+'</font> </div>'
			if(v.m==1)html+='<div class="abs" style="top:2px;left:2px;z-index:-100;background:#fff7d8;width:56px;height:56px;border-radius:45px;"></div>'
			html+='<br>luifel<br>'
			setHTML(device,html)
			updateDeviceTime(device)
			return
		case 'l':
			log('💬 '+v)
			return
		default:
			setTime()
			switch(v?.d) {
				case 's':
				case 'sc': {
					if (device === 'daikin') {
						const isOn = (v.s > 0);
						if (!isOn) setText('daikin_kwh', '');
						else {
							setText('daikin_kwh', v.p + ' W');
							setStyle('daikin_kwh', 'color', berekenKleurRood(v.p, 2000, 400));
						}
					}
					setHTML(device, renderSwitchHTML(device, v));
					if (device === 'poort' && d.weg?.s > 0) setStyle('poort', 'display', 'none');
					else if (device === 'weg' && d.weg?.s === 0) setStyle('poort', 'display', 'block');
					return
				}
				case 'd':
				case 'hd':
					setHTML(device, renderDimmerHTML(device, v));
					return
				case 'b':
					setHTML(device, renderBoseHTML(device, v));
					return
				case 'r': {
					const status = 100 - v.s;
					const perc = (status < 100) ? (status / 100) * 0.7 : 1;
					if (device !== 'zoldertrap') {
						const opts = v.i.split(',');
						let rollerTop = parseInt(opts[0]);
						let indicatorSize = 0;
						if (status > 0) {
							indicatorSize = Math.min((parseFloat(opts[2]) * perc) + 8, parseFloat(opts[2]));
							rollerTop = parseInt(opts[0]) + parseInt(opts[2]) - indicatorSize;
							addClass(device, 'yellow');
						}
						if (opts[3] === 'P') {
							setStyle(device, 'top',    rollerTop + 'px');
							setStyle(device, 'left',   opts[1] + 'px');
							setStyle(device, 'width',  '8px');
							setStyle(device, 'height', indicatorSize + 'px');
						} else if (opts[3] === 'L') {
							setStyle(device, 'top',    opts[0] + 'px');
							setStyle(device, 'left',   opts[1] + 'px');
							setStyle(device, 'width',  indicatorSize + 'px');
							setStyle(device, 'height', '9px');
						}
					}
					let html;
					if (v.s === 100)     html = '<img src="/images/arrowgreendown.png" class="i48">';
					else if (v.s === 0)  html = '<img src="/images/arrowgreenup.png" class="i48">';
					else                 html = `<img src="/images/circlegrey.png" class="i48">`
					                          + `<div class="fix center dimmerlevel" style="position:absolute;top:19px;left:1px;width:46px;letter-spacing:6px;">`
					                          + `<font size="5" color="#CCC">${v.s}</font></div>`;
					html += '</div>';
					if (v.t > (newTime - 82800)) {
						const dt = new Date(v.t * 1000);
						html += `<br><div id="t${device}">${dt.getHours()}:${String(dt.getMinutes()).padStart(2,'0')}</div>`;
					} else {
						html += `<br><div id="t${device}">${formatDate(v.t)}</div>`;
					}
					setHTML('R' + device, html);
					return
				}
				case 'p': {
					d[device] = v;
					const pirZone = device.replace('pir', '');
					updateDeviceTime(device);
					if (pirZone === 'hall' || pirZone === 'living') {
						if (v.s === 1) { addClass('z'+pirZone+'a','motion'); addClass('z'+pirZone+'b','motion'); }
						else           { removeClass('z'+pirZone+'a','motion'); removeClass('z'+pirZone+'b','motion'); }
					} else {
						if (v.s === 1) addClass('z'+pirZone, 'motion');
						else           removeClass('z'+pirZone, 'motion');
					}
					return
				}
				case 'c':
					if (device !== 'raamhall') {
						d[device] = v;
						updateDeviceTime(device);
						if (v.s === 1) addClass(device, 'red');
						else           removeClass(device, 'red');
					}
					return
				case 't':
					if (device === 'buiten_temp') {
						if (d.w?.mint !== undefined && d.d?.b?.m != undefined) renderThermometer(device, v);
					} else {
						renderThermometer(device, v);
					}
					return
				case 'th': {
					const thTemp = d[device.replace('_set','_temp')]?.s ?? 0;
					const dif = thTemp - v.s;
					const circle = dif > 0.3 ? 'hot' : dif < -0.3 ? 'cold' : 'grey';
					const center = v.s >= 20 ? 'red' : v.s > 19 ? 'orange' : v.s > 14 ? 'grey' : 'blue';
					const valStr = v.s.toString().replace('.', ',');
					let html = `<img src="/images/thermo${circle}${center}.png" class="i48" alt="">`
					         + `<div class="abs center" style="top:35px;left:11px;width:26px;">`;
					if (v.m === 1) {
						html += `<font size="2" color="#222">${valStr}</font></div>`
						      + `<div class="abs" style="top:2px;left:2px;z-index:-100;background:#b08000;width:44px;height:44px;border-radius:45px;"></div>`;
					} else {
						html += `<font size="2" color="#CCC">${valStr}</font></div>`;
					}
					if (v.t > (newTime - 82800)) {
						const dt = new Date(v.t * 1000);
						html += `<br><div id="t${device}">${dt.getHours()}:${String(dt.getMinutes()).padStart(2,'0')}</div>`;
					} else {
						html += `<br><div id="t${device}">${formatDate(v.t)}</div>`;
					}
					setHTML(device, html);
					return
				}
			}
	}
	if (device==='n') {
		if(setText('netvalue', v)){
			if (shouldRedraw(prevnet,v,2500,5)||v===0) {
				prevnet=v
				const kleur = v < 0 ? berekenKleurGroen(-v, 5000) : berekenKleurRood(v, 5000);
				setStyle('net', 'color', kleur);
				setStyle('nettitle', 'color', kleur);
				drawCircle('netcircle', v, 2500, 90, 'purple')
			}
		}
	}
	if (device==='c') {
		if(setText('batcharge',v+' %')) {
			drawCircle('chargecircle', v, 100, 82, 'gray');
			setStyle('batcharge', 'color', berekenKleurGroen(v, 100));
		}
		return
	}
	if (device==='z') {
		if(setText('zonvalue', v)){
			if (shouldRedraw(prevzon,v,90)||v===0) {
				prevzon=v
				setStyle('zonvalue', 'color', berekenKleurGroen(v, d.zonavg * 2));
				drawCircle('zoncircle', -v, d.zonavg, 90, 'green');
			}
		}
	}
	if (device==='b') {
		if (v < -800) v = -800;
		else if (v > 800) v = 800;
		if(setText('batvalue', v)) {
			if (v > 0) setStyle('bat', 'color', berekenKleurRood(v, 2000));
			else setStyle('bat', 'color', berekenKleurGroen(-v, 1000));
			drawCircle('batcircle', v, 800, 90, 'purple');
		}
		return
	}
	const total = d.n + d.z - d.b;
	if (device==='n'||device==='z'||device==='b'){
		if(setText('totalvalue', total)){
			if (shouldRedraw(prevtotal,total,2500)||v===0) {
				prevtotal=total
				const kleur=berekenKleurRood(d.n, 5000)
				setStyle('totaltitle', 'color', kleur);
				setStyle('totalvalue', 'color', kleur);
				drawCircle('totalcircle', total, 2500 + d.z, 90, 'purple');
			}
		}
		return
	}
}
async function ajaxbose(ip){
	cleanup('bose')
    if (isSubmitting) return;
    isSubmitting=true;
    try {
        const data=await ajaxJSON('/ajax.php?bose=' + ip)
        if (data.time){
            const date=new Date(data.time * 1000);
            const hours=date.getHours();
            const minutes=('0' + date.getMinutes()).slice(-2);
            const seconds=('0' + date.getSeconds()).slice(-2);
            setText('time',hours + ':' + minutes + ':' + seconds);
        }
        let html='';
        if (data.source!=="STANDBY"){
            if (data.volume!==undefined){
                const volume=parseInt(data.volume,10);
                const levels=[-10,-7,-4,-2,-1,0,1,2,4,7,10];
                html="<br>";
                levels.forEach(level=>{
                    const newlevel=volume + level;
                    if (newlevel >= 0&&newlevel <= 80){
                        const cls=(level===0) ? 'btn volume btna' : 'btn volume hover';
                        html += `<button class="${cls}" id="vol${level}" onclick="ajaxcontrolbose('${ip}','volume','${newlevel}')">${newlevel}</button>`;
                    }
                });
                setHTML('volume',html);
            }
            if (data.source==="SPOTIFY"){
                setText('artist',data.artist)
                setText('track',data.track)
            } else if (data.source==="BLUETOOTH"){
                setText('artist',"Bluetooth")
                setText('track',data.track)
            } else if (data.source==="TUNEIN"){
                setText('artist',data.artist)
                setText('track',data.track)
                setText('source',data.source)
            } else {
                try { setText('artist',data.source); } catch {}
            }
            let img='None';
            try { img=data.art?.toString().replace("http://","https://"); } catch {}
            if (data.source==="BLUETOOTH") html='<img src="/images/bluetooth.png" height="160px" width="auto" alt="bluetooth">';
            else if (img==='None') html='';
            else if (img.startsWith('http')) html=`<img src="${img}" class="spotify" alt="Art">`;
            else html='';
            setHTML('art',html);
            html='';
            if (data.source==="SPOTIFY"){
                html += `<button class="btn b2" onclick="ajaxcontrolbose('${ip}','skip','prev')">Prev</button>`;
                html += `<button class="btn b2" onclick="ajaxcontrolbose('${ip}','skip','next')">Next</button>`;
            }
                const presets=[
                    ['EDM - Part 1','1'],
                    ['EDM - Part 2','2'],
                    ['EDM - Part 3','3'],
                    ['Mix - Part 1','4'],
                    ['Mix - Part 2','5'],
                    ['Mix - Part 3','6'],
                ];
                presets.forEach(([playlistName,presetId])=>{
                    const cls=(data.playlist===playlistName) ? 'btn btna b3' : 'btn b3';
                    html += `<button class="${cls}" onclick="ajaxcontrolbose('${ip}','preset','${presetId}')">${playlistName.split(' ')[0]} - ${playlistName.split(' ')[3]}</button>`;
                });
        } else {
            setText('artist','');
            setText('track','');
            setHTML('art','');
            setHTML('volume','');
            setHTML('bass','');
            html=`<button class="btn b1" onclick="ajaxcontrolbose('${ip}','power','On')">Power On</button>`;
        }
        setHTML('power',html);
        setHTML('playlist',data.playlisttoday ?? '');
    } catch(err){
        console.warn('ajaxbose error',err);
    } finally {
        isSubmitting=false;
    }
}
function setpoint(device){
	const level=d[device+'_set'].s;
	const heatingset=d.heating.s
	const temp=d[device+'_temp'].s
	const $mode=d[device+'_set'].m
	let min, avg, max;
	if (device==='living'){
		min=16; avg=20; max=23;
	} else if (device==='badkamer'){
		min=12; avg=20; max=22;
	} else {
		min=10; avg=16; max=22;
		if (d.heating&&d.heating.s != null){
			avg -= Number(d.heating.s);
		}
	}
	const currentTxtColor=getTemperatureColorTxt(parseFloat(temp),min,avg,max);
	const targetTxtColor=getTemperatureColorTxt(parseFloat(level),min,avg,max);
	let html='<div class="dimmer"><div class="dimmer-container">';
	html += '<div class="dimmer-header">';
	html += '<h2 class="dimmer-title">'+ucfirst(device)+'</h2>';
	html += '</div>';
	html += '<div class="temp-displaytop">';
	html += '<div class="temp-box current">';
	html += '<div class="temp-label">Temperatuur</div>';
	html += '<div class="temp-value" style="color:'+currentTxtColor+';">'+temp+'°C</div>';
	html += '</div>';
	html += '<div class="temp-box target">';
	html += '<div class="temp-label">Setpoint</div>';
	html += '<div class="temp-value" id="targetTemp" style="color:'+targetTxtColor+';">'+level+'°C</div>';
	html += '</div>';
	html += '<button class="mode-btn '+($mode==0||!$mode?'active':'')+'" onclick="ajaxcontrol(\''+device+'_set\',\'storemode\',\'0\');setView(\'floorplanheating\')">Auto</button>';
	html += '</div>';
	let temps=[];
	if(device=='badkamer'){
		temps=[11,12,13,14,15,16,16.2,16.4,16.6,16.8,17,17.2,17.4,17.6,17.8,18,18.2,18.4,18.6,18.8,19,19.2,19.4,19.6,19.8,20,20.2,20.4,20.6,20.8,21,21.2,21.4,21.6,21.8];
	}else{
		if(heatingset>0){
			if(device=='living') temps=[10,13,14,15,16,17,17.2,17.4,17.6,17.8,18,18.2,18.4,18.6,18.8,19,19.2,19.4,19.6,19.8,20,20.2,20.4,20.6,20.8,21,21.2,21.4,21.6,21.8,22,22.2,22.4,22.6,22.8];
			else temps=[4,10,11,12,12.5,13,13.2,13.4,13.6,13.8,14,14.2,14.4,14.6,14.8,15,15.2,15.4,15.6,15.8,16,16.2,16.4,16.6,16.8,17,17.2,17.4,17.6,17.8,18,18.2,18.4,18.6,18.8];
		}else if(heatingset==-2){
			temps=[1,2,3,4,5,15,16,17,18,18.5,19,19.5,20,20.5,21,21.5,22,22.5,23,23.5,24,24.5,25,25.5,33,'D'];
		}else{
			temps=['D'];
		}
	}
	const cols=5;
	const total=temps.length;
	const rows=Math.ceil(total / cols);
	let tempsSorted=temps.slice().sort((a,b)=>{
		if(a==='D') return 1;
		if(b==='D') return -1;
		return a-b;
	});
	let grid=[];
	for(let r=0;r<rows;r++){
		grid[r]=[];
	}
	for(let i=0;i<tempsSorted.length;i++){
		let row=rows - 1 - Math.floor(i/cols);
		let col=i % cols;
		grid[row][col]=tempsSorted[i];
	}
	let orderedTemps=[];
	for(let r=0;r<rows;r++){
		for(let c=0;c<cols;c++){
			if(grid[r][c]!==undefined) orderedTemps.push(grid[r][c]);
		}
	}
	html += '<div class="quick-levels quick-levels-setpoint">';
	orderedTemps.forEach(function(t){
		let btnClass='level-btn';
		let levelNum=parseFloat(level);
		let tempNum=(t==='D') ? t : parseFloat(t);

		if(level == t || (t==='D'&&level==='D')){
			btnClass += ' active';
		} else if(t!=='D'&&level!=='D'&&levelNum > tempNum){
			btnClass += ' below';
		}
		let displayTemp=(t==='D') ? 'D' : t;
		html += '<button class="'+btnClass+'" data-temp="'+t+'" onclick="setThermostatTemp(\''+device+'\',\''+t+'\')">'+displayTemp+'</button>';
	});
	html += '</div>';
	html += '<button class="close-btn" onclick="setView(\'floorplanheating\')">✕</button>';
	html += '</div></div>';
	setHTML('floorplantemp',html);
	setView('floorplantemp')
}
window.dimmerLocked=window.dimmerLocked || {};
function dimmer(device,floorplan='floorplan'){
	if(window.dimmerLocked[device])return;
	const current=d[device].s
	let html='<div class="dimmer"><div class="dimmer-container">';
	html += '<div class="dimmer-header">';
	html += '<h2 class="dimmer-title">'+ucfirst(device)+'</h2>';
	if(current==0) html += '<p class="dimmer-value off" id="dimmerValue">Uit</p>';
	else html += '<p class="dimmer-value" id="dimmerValue">'+current+'%</p>';
	html += '</div>';
	html += '<div class="slider-container">';
	html += '<div class="slider-track" id="sliderTrack">';
	const sliderPos=dimmerToSlider(parseInt(current));
	html += '<div class="slider-fill" id="sliderFill" style="width:'+sliderPos+'%"></div>';
	html += '<div class="slider-thumb" id="sliderThumb" style="left:'+sliderPos+'%"></div>';
	html += '</div></div>';
	let levels=[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,22,24,26,28,30,32,34,36,38,40,45,50,55,60,65,70,75,80,85,90];
	let currentInt=parseInt(current);
	if (!levels.includes(currentInt)&&currentInt > 0&&currentInt < 100){
		let closestIndex=0;
		let closestDiff=Math.abs(levels[0] - currentInt);
		for (let i=1; i < levels.length; i++){
			let diff=Math.abs(levels[i] - currentInt);
			if (diff < closestDiff){
				closestDiff=diff;
				closestIndex=i;
			}
		}
		levels[closestIndex]=currentInt
	}
	levels.sort((a,b)=>a - b)
	const cols=5
	const rows=8
	let grid=[]
	for (let r=0; r < rows; r++){
		grid[r]=[]
	}
	for (let i=0; i < levels.length; i++){
		let row=rows - 1 - Math.floor(i / cols)
		let col=cols - 1 - (i % cols)
		grid[row][col]=levels[i]
	}
	let orderedLevels=[];
	for (let r=0; r < rows; r++){
		for (let c=0; c < cols; c++){
			if(grid[r][c]!==undefined){
				orderedLevels.push(grid[r][c]);
			}
		}
	}
	html += '<div class="quick-levels quick-levelsd">';
	orderedLevels.forEach(function(level){
		let btnClass='level-btn level-btnd';
		if(currentInt == level) btnClass += ' active';
		else if(currentInt > level) btnClass += ' below';
		html += '<button class="'+btnClass+'" data-level="'+level+'" onclick="setDimmerLevel(\''+device+'\','+level+')">'+level+'</button>';
	});
	html += '</div>';
	html += '<div class="control-buttons">';
	html += '<button class="ctrl-btn off" onclick="ajaxcontrol(\''+device+'\',\'dimmer\',\'0\');setView(\'floorplan\');">';
	html += '<span class="ctrl-btn-icon">⏻</span><span>Uit</span></button>';
	html += '<button class="ctrl-btn on" onclick="ajaxcontrol(\''+device+'\',\'dimmer\',\'100\');setView(\'floorplan\');">';
	html += '<span class="ctrl-btn-icon">💡</span><span>100%</span></button>';
	html += '</div>';
	html += '<button class="close-btn" onclick="setView(\'floorplan\')">✕</button>';
	html += '</div></div>';
	setHTML('floorplantemp',html);
	setView('floorplantemp')
	setTimeout(function(){initDimmerSlider(device);},10);
}
function roller(device,floorplan='floorplanheating'){
	const current=d[device].s
	const currentInt=parseInt(current);
	let html='<div class="dimmer"><div class="dimmer-container">';
	html += '<div class="dimmer-header">';
	html += '<h2 class="dimmer-title">'+ucfirst(device)+'</h2>';
	let status='';
	if(currentInt == 0) status=device == 'luifel' ? 'Dicht' : 'Open';
	else if(currentInt == 100) status=device == 'luifel' ? 'Open' : 'Dicht';
	if(status) html += '<p class="dimmer-value" id="rollerValue">'+status+'</p>';
	else html += '<p class="dimmer-value" id="rollerValue">'+currentInt+' %</p>';
	html += '</div>';
	if(device == 'luifel'){
		let mode=d[device].m
		html += '<div class="mode-toggle">';
		html += '<button class="mode-btn '+(mode==1?'active':'')+'" onclick="ajaxcontrol(\'luifel\',\'mode\',\'1\');setView(\'floorplanheating\');">Manueel</button>';
		html += '<button class="mode-btn '+(mode==0||!mode?'active':'')+'" onclick="ajaxcontrol(\'luifel\',\'mode\',\'0\');setView(\'floorplanheating\');">Auto</button>';
		html += '</div>';
	}
	let levels=[5,10,15,20,25,30,32,34,36,38,40,42,44,46,48,50,52,54,56,58,60,62,64,66,68,70,72,74,76,78,80,82,85,90,95];
	if (!levels.includes(currentInt)&&currentInt > 0&&currentInt < 100){
		let closestIndex=0;
		let closestDiff=Math.abs(levels[0] - currentInt);
		for (let i=1; i < levels.length; i++){
			let diff=Math.abs(levels[i] - currentInt);
			if (diff < closestDiff){
				closestDiff=diff;
				closestIndex=i;
			}
		}
		levels[closestIndex]=currentInt;
	}
	levels.sort((a,b)=>b - a);
	const cols=5;
	const total=levels.length;
	const rows=Math.ceil(total / cols);
	let grid=[];
	for(let r=0; r<rows; r++){
		grid[r]=[];
	}
	for(let i=0; i<levels.length; i++){
		let row=rows - 1 - Math.floor(i / cols);
		let col=i % cols;
		grid[row][col]=levels[i];
	}
	let orderedLevels=[];
	for(let r=0;r<rows;r++){
		for(let c=0;c<cols;c++){
			if(grid[r][c]!==undefined) orderedLevels.push(grid[r][c]);
		}
	}
	html += '<div class="quick-levels">';
	orderedLevels.forEach(function(level){
		let btnClass='level-btn';
		if(currentInt == level) btnClass += ' active';
		else if(currentInt < level) btnClass += ' below';
		html += '<button class="'+btnClass+'" data-level="'+level+'" onclick="setRollerLevel(\''+device+'\','+level+')">'+level+'</button>';
	});
	html += '</div>';
	html += '<div class="roller-direction-buttons">';
	if(device == 'luifel'){
		html += '<button class="direction-btn down '+(currentInt==100?'active':'')+'" onclick="setRollerLevel(\''+device+'\',100)">';
		html += '<span class="direction-btn-icon">▼</span><span>Uitrollen</span></button>';
		html += '<button class="direction-btn up '+(currentInt==0?'active':'')+'" onclick="setRollerLevel(\''+device+'\',0)">';
		html += '<span class="direction-btn-icon">▲</span><span>Oprollen</span></button>';
	} else {
		html += '<button class="direction-btn down '+(currentInt==100?'active':'')+'" onclick="setRollerLevel(\''+device+'\',100)">';
		html += '<span class="direction-btn-icon">▼</span><span>Omlaag</span></button>';
		html += '<button class="direction-btn up '+(currentInt==0?'active':'')+'" onclick="setRollerLevel(\''+device+'\',0)">';
		html += '<span class="direction-btn-icon">▲</span><span>Omhoog</span></button>';
	}
	html += '</div>';
	html += '<button class="close-btn" onclick="setView(\'floorplanheating\');">✕</button>';
	html += '</div></div>';
	setHTML('floorplantemp',html);
	setView('floorplantemp')
}
function verlof(){
	let html='<div class="dimmer" ><div style="min-height:220px">'
	html+='<div id="message" class="dimmer">'
	if (d.verlof.s==2)html+='<button class="btn btna huge3" style="display:inline-block;" onclick="ajaxcontrol(\'verlof\',\'verlof\',\'2\');setView(\'floorplanothers\');">Verlof</button>'
	else html+='<button class="btn huge3" style="display:inline-block;" onclick="ajaxcontrol(\'verlof\',\'verlof\',\'2\');setView(\'floorplanothers\');">Verlof</button>'
	if (d.verlof.s==1)html+='<button class="btn btna huge3" style="display:inline-block;" onclick="ajaxcontrol(\'verlof\',\'verlof\',\'1\');setView(\'floorplanothers\');">Geen school</button>'
	else html+='<button class="btn huge3" style="display:inline-block;" onclick="ajaxcontrol(\'verlof\',\'verlof\',\'1\');setView(\'floorplanothers\');">Geen school</button>'
	if (d.verlof.s==0)html+='<button class="btn btna huge3" style="display:inline-block;" onclick="ajaxcontrol(\'verlof\',\'verlof\',\'0\');setView(\'floorplanothers\');">Normaal</button>'
	else html+='<button class="btn huge3" style="display:inline-block;" onclick="ajaxcontrol(\'verlof\',\'verlof\',\'0\');setView(\'floorplanothers\');">Normaal</button>'
	html+='</div>'
	html+='</div>'
	html += '<button class="close-btn" onclick="setView(\'floorplanothers\');">✕</button>';
	setHTML('floorplantemp',html);
	setView('floorplantemp')
}
function weg(){
	let html=''
	html += '<div id="message" class="dimmer">'
	const warnings=[]
	if(d.achterdeur.s==='Open') warnings.push('Achterdeur OPEN')
	if(d.raamliving.s==='Open') warnings.push('Raam Living OPEN')
	if(d.raamhall.s==='Open') warnings.push('Raam Hall OPEN')
	if(d.raamkeuken.s==='Open') warnings.push('Raam Keuken OPEN')
	if(d.bose103.s===1) warnings.push('Bose kamer aan')
	if(d.bose104.s===1) warnings.push('Bose garage aan')
	if(d.bose105.s===1) warnings.push('Bose keuken aan')
	if(d.bose106.s===1) warnings.push('Bose Buiten20 aan')
	if(d.bose107.s===1) warnings.push('Bose Buiten10 aan')
	if(warnings.length > 0){
		html += '<h1 style="font-size:4em">OPGELET!<br>'
		html += warnings.join('<br>')
		html += '</h1>'
	}
	let buttonHeight=Math.max(44 - warnings.length * 10,6) + 'vh'
	if(d.weg.s == 0){
		html += `<button class="btn huge2" style="height:${buttonHeight};display:inline-block;background-image:url(images/weg.png);background-repeat:no-repeat;background-position:center left 58px;background-size:25%;" onclick="ajaxcontrol('weg','weg','2');setView(\'floorplan\');">Weg</button>`
		html += `<button class="btn huge2" style="height:${buttonHeight};display:inline-block;background-image:url(images/Slapen.png);background-repeat:no-repeat;background-position:center left 58px;background-size:25%;" onclick="ajaxcontrol('weg','weg','1');setView(\'floorplan\');">Slapen</button>`
	} else if(d.weg.s == 1){
		setView('floorplan')
		ajaxcontrol('weg','weg',0)
		return
	} else if(d.weg.s == 2){
		html += `<button class="btn huge2" style="height:${buttonHeight};display:inline-block;background-image:url(images/Thuis.png);background-repeat:no-repeat;background-position:center left 58px;background-size:25%;" onclick="ajaxcontrol('weg','weg','0');setView(\'floorplan\');">Thuis</button>`
		html += `<button class="btn huge2" style="height:${buttonHeight};display:inline-block;background-image:url(images/Vacation.png);background-repeat:no-repeat;background-position:center left 58px;background-size:25%;" onclick="ajaxcontrol('weg','weg','3');setView(\'floorplan\');">Vakantie</button>`
	}
	html += '</div></div>'
	html += '<button class="close-btn" onclick="setView(\'floorplan\');">✕</button>';
	setHTML('floorplantemp',html);
	setView('floorplantemp')
}
function heating(){
	let html='<div class="dimmer" ><div style="min-height:220px"><div id="message" class="dimmer">'
	if(d.heating.s==3) html+='<button class="btn btna huge7" style="display:inline-block;background-image:url(images/Gas.png);background-repeat:no-repeat;background-position:center left 80px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'3\');setView(\'floorplanheating\');">Gas heating</button>'
	else html+='<button class="btn huge7" style="display:inline-block;background-image:url(images/Gas.png);background-repeat:no-repeat;background-position:center left 80px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'3\');setView(\'floorplanheating\');">Gas heating</button>'
	if(d.heating.s==2) html+='<button class="btn btna huge7" style="display:inline-block;background-image:url(images/GasAirco.png);background-repeat:no-repeat;background-position:center left 35px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'2\');setView(\'floorplanheating\');">Gas-Airco heating</button>'
	else html+='<button class="btn huge7" style="display:inline-block;background-image:url(images/GasAirco.png);background-repeat:no-repeat;background-position:center left 35px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'2\');setView(\'floorplanheating\');">Gas-Airco heating</button>'
	if(d.heating.s==1) html+='<button class="btn btna huge7" style="display:inline-block;background-image:url(images/Cooling_red.png);background-repeat:no-repeat;background-position:center left 50px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'1\');setView(\'floorplanheating\');">Airco heating</button>'
	else html+='<button class="btn huge7" style="display:inline-block;background-image:url(images/Cooling_red.png);background-repeat:no-repeat;background-position:center left 50px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'1\');setView(\'floorplanheating\');">Airco heating</button>'
	if(d.heating.s==0) html+='<button class="btn btna huge7" style="display:inline-block;background-image:url(images/close.png);background-repeat:no-repeat;background-position:center left 35px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'0\');setView(\'floorplanheating\');">Neutral</button>'
	else html+='<button class="btn huge7" style="display:inline-block;background-image:url(images/close.png);background-repeat:no-repeat;background-position:center left 35px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'0\');setView(\'floorplanheating\');">Neutral</button>'
	if(d.heating.s==-1) html+='<button class="btn btna huge7" style="display:inline-block;background-image:url(images/Cooling_grey.png);background-repeat:no-repeat;background-position:center left 50px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'-1\');setView(\'floorplanheating\');">Passive cooling</button>'
	else html+='<button class="btn huge7" style="display:inline-block;background-image:url(images/Cooling_grey.png);background-repeat:no-repeat;background-position:center left 50px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'-1\');setView(\'floorplanheating\');">Passive cooling</button>'
	if(d.heating.s==-2) html+='<button class="btn btna huge7" style="display:inline-block;background-image:url(images/Cooling.png);background-repeat:no-repeat;background-position:center left 50px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'-2\');setView(\'floorplanheating\');">Airco cooling</button>'
	else html+='<button class="btn huge7" style="display:inline-block;background-image:url(images/Cooling.png);background-repeat:no-repeat;background-position:center left 50px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'-2\');setView(\'floorplanheating\');">Airco cooling</button>'
	html+='</div>'
	html+='</div>'
	html+='<button class="close-btn" onclick="setView(\'floorplanheating\');">✕</button>';
	setHTML('floorplantemp',html);
	setView('floorplantemp')
}
function confirmSwitch(device){
	let html='<div class="dimmer" ><div style="min-height:220px"><div id="message" class="dimmer"><br><h1>'+device+'</h1><br>'
	if (d[device].s==1){
		html+='<button class="btn btna huge3" onclick="ajaxcontrol(\''+device+'\',\'sw\',\'On\');setView(\''+view+'\');">On</button>'
		html+='<button class="btn huge3" onclick="ajaxcontrol(\''+device+'\',\'sw\',\'Off\');setView(\''+view+'\');">Off</button>'
	} else {
		html+='<button class="btn huge3" onclick="ajaxcontrol(\''+device+'\',\'sw\',\'On\');setView(\''+view+'\');">On</button>'
		html+='<button class="btn btna huge3" onclick="ajaxcontrol(\''+device+'\',\'sw\',\'Off\');setView(\''+view+'\');">Off</button>'
	}
	html+='</div>'
	html+='</div>'
	html += '<button class="close-btn" onclick="setView(\''+view+'\');">✕</button>';
	setHTML('floorplantemp',html);
	setView('floorplantemp')
}
const circleStates={};
function navigator_Go(n){
	if (client) {
        client.end(true);
        client = null;
    }
	window.location.assign(n)
}
function ajaxcontrol(a,o,n){fetch(`https://home.egregius.be/ajax.php?device=${a}&command=${o}&action=${n}`,{cache:'no-store'}).catch(err=>console.warn('ajaxcontrol error',err));}
function ajaxcontrolbose(a,o,n){fetch(`https://home.egregius.be/ajax.php?boseip=${a}&command=${o}&action=${n}`,{cache:'no-store'}).catch(err=>console.warn('ajaxcontrolbose error',err));}
function floorplanbose(){ajaxbose($ip)(),myAjaxmedia=$.setInterval((function(){ajaxbose($ip)}),1e3)}
function pad(e,n){return len=n-(""+e).length,(len>0?new Array(++len).join("0"):"")+e}
function fix(){var e=this,n=e.parentNode,i=e.nextSibling;n.removeChild(e),setTimeout((function(){n.insertBefore(e,i)}),0)}
function sliderToDimmer(r){return r<=50?r/50*25:25+(r-50)/50*75}
function dimmerToSlider(r){return r<=25?r/25*50:50+(r-25)/75*50}
function setThermostatTemp(t,o){ajaxcontrol(t,"setpoint",o),setView('floorplanheating')}
function setRollerLevel(e,l){ajaxcontrol(e,"roller",l),setView('floorplanheating')}
function setDimmerLevel(e,i){ajaxcontrol(e,"dimmer",i),setView('floorplan')}
function initDimmerSlider(e){
/*	const t = getElem("sliderTrack"),
	      n = getElem("sliderThumb"),
	      o = getElem("sliderFill"),
	      s = getElem("dimmerValue");
*/	const t = document.getElementById("sliderTrack");
	const n = document.getElementById("sliderThumb");
	const o = document.getElementById("sliderFill");
	const s = document.getElementById("dimmerValue");
	if (!t || !n || !o) return;
	let i = false;
	let d = parseInt(sessionStorage.getItem(e)) || 0;
	let c = null;
	function getClientX(ev){
		if (ev.touches && ev.touches.length) return ev.touches[0].clientX;
		if (ev.changedTouches && ev.changedTouches.length) return ev.changedTouches[0].clientX;
		return ev.clientX ?? null;
	}
	function r(val){
		const t = Math.round(val);
		if (s){
			if (t === 0){
				s.textContent = "Uit";
				s.classList.add("off");
			} else {
				s.textContent = t + "%";
				s.classList.remove("off");
			}
		}
		document.querySelectorAll(".level-btn").forEach(btn=>{
			const n = parseInt(btn.dataset.level);
			btn.classList.remove("active","below");
			if (n === t) btn.classList.add("active");
			else if (n < t) btn.classList.add("below");
		});
		d = t;
		return t;
	}

	function l(val){
		window.dimmerLocked[e] = true;
		ajaxcontrol(e,"dimmer",val);
		sessionStorage.setItem(e,val);
	}

	function a(ev){
		if (ev.cancelable) ev.preventDefault();

		const rect = t.getBoundingClientRect();
		const x = getClientX(ev);
		if (x === null) return;

		const pos = x - rect.left;
		const pct = Math.max(0, Math.min(100, pos / rect.width * 100));

		let m;
		const u = (m = pct) <= 50
			? m / 50 * 25
			: 25 + (m - 50) / 50 * 75;

		const v = r(Math.round(u));

		o.style.width = pct + "%";
		n.style.left  = pct + "%";

		if (i){
			if (c) clearTimeout(c);
			c = setTimeout(()=>l(v),250);
		}
		return v;
	}

	function start(ev){
		if (ev.cancelable) ev.preventDefault();
		i = true;
		window.dimmerLocked[e] = true;
		n.style.cursor = "grabbing";
		a(ev); // CRUCIAAL voor iOS
	}

	function end(){
		if (!i) return;
		i = false;
		n.style.cursor = "grab";
		if (c) clearTimeout(c);
		l(d);
		setTimeout(()=>{ window.dimmerLocked[e] = false; },1000);
	}

	// mouse
	n.addEventListener("mousedown", start);
	document.addEventListener("mousemove", ev=>{ if (i) a(ev); });
	document.addEventListener("mouseup", end);

	// touch (iOS!)
	n.addEventListener("touchstart", start, { passive:false });
	document.addEventListener("touchmove", ev=>{ if (i) a(ev); }, { passive:false });
	document.addEventListener("touchend", end, { passive:false });
	document.addEventListener("touchcancel", end, { passive:false });

	// click on track
	t.addEventListener("click", ev=>{
		if (!i) l(a(ev));
	});
}
function interp(c1, c2, f) {
    return Math.round(c1 + (c2 - c1) * f);
}
function clamp(v, min, max) {
    return Math.max(min, Math.min(max, v));
}
function norm(t, start, end) {
    if (start === end) return 0;
    const f = (t - start) / (end - start);
    return Math.max(0, Math.min(1, f));
}
function berekenKleurBlauw(t, end, start = 0) {
    const f = norm(t, start, end);
    if (f <= 0) return '#CCCCCC';
    if (f >= 1) return '#3399FF';
    const r = interp(204,  51, f);
    const g = interp(204, 153, f);
    const b = interp(204, 255, f);
    return `#${r.toString(16).padStart(2,'0')}${g.toString(16).padStart(2,'0')}${b.toString(16).padStart(2,'0')}`;
}
function berekenKleurGroen(t, end, start = 0) {
    const f = norm(t, start, end);
    if (f <= 0) return '#CCCCCC';
    if (f >= 1) return '#33FF66';
    const r = interp(204,  51, f);
    const g = interp(204, 255, f);
    const b = interp(204, 102, f);
    return `#${r.toString(16).padStart(2,'0')}${g.toString(16).padStart(2,'0')}${b.toString(16).padStart(2,'0')}`;
}
function berekenKleurRood(t, end, start = 0) {
    const f = norm(t, start, end);

    if (f <= 0) return '#CCCCCC';
    if (f >= 1) return '#FF0000';

    let r, g, b, k;

    if (f < 0.33) {
        // grijs -> geel
        k = f / 0.33;
        r = interp(204, 255, k);
        g = interp(204, 255, k);
        b = interp(204, 102, k);

    } else if (f < 0.66) {
        // geel -> oranje
        k = (f - 0.33) / 0.33;
        r = 255;
        g = interp(255, 153, k);
        b = interp(102, 0, k);

    } else {
        // oranje -> rood
        k = (f - 0.66) / 0.34;
        r = 255;
        g = interp(153, 0, k);
        b = 0;
    }

    return `#${r.toString(16).padStart(2,'0')}${g.toString(16).padStart(2,'0')}${b.toString(16).padStart(2,'0')}`;
}
function drawCircle(id,value,color,radius,angle) {
    const canvas = getElem(id)
    if (!canvas) return
    const ctx = canvas.getContext("2d")
    const cx = canvas.width / 2
    const cy = canvas.height / 2
    const r = radius / 2
    if (!circleStates[id]) circleStates[id] = { currentValue: value,targetValue: value }
    const state = circleStates[id]
    state.currentValue = value
    state.targetValue = value
    drawCircleFrame(ctx,cx,cy,r,value,color,angle,id)
}
function drawCircleFrame(e,t,r,l,f,a,s,o){
	const k=Math.abs(f)/a;
	let y;y=k>2?k-2:k>1?k-1:k;
	const S=2*Math.PI,c=y*S;
	e.clearRect(0,0,e.canvas.width,e.canvas.height),e.beginPath(),e.arc(t,r,l,-Math.PI/2,-Math.PI/2+S),e.lineWidth=10,k>2?f>=0?"purple"==s?e.strokeStyle="#b54dff":"green"==s?e.strokeStyle="#66ff66":"red"==s?e.strokeStyle="#ffb833":"blue"==s&&(e.strokeStyle="#3333ff"):"purple"==s?e.strokeStyle="#0f3d0f":"green"==s?e.strokeStyle="#66ff66":"red"==s?e.strokeStyle="#ffb833":"blue"==s&&(e.strokeStyle="#000080"):k>1?f>=0?"purple"==s?e.strokeStyle="#b54dff":"green"==s?e.strokeStyle="#33cc33":"red"==s?e.strokeStyle="#ffb833":"blue"==s&&(e.strokeStyle="#3333ff"):"purple"==s?e.strokeStyle="#0f3d0f":"green"==s?e.strokeStyle="#33cc33":"red"==s?e.strokeStyle="#ffb833":"blue"==s&&(e.strokeStyle="#000080"):f>=0?"purple"==s?e.strokeStyle="#3b0066":"green"==s?e.strokeStyle="#0f3d0f":"red"==s?e.strokeStyle="#805300":"blue"==s?e.strokeStyle="#000080":"gray"==s&&(e.strokeStyle="#000000"):"purple"==s||"green"==s?e.strokeStyle="#0f3d0f":"red"==s?e.strokeStyle="#805300":"blue"==s&&(e.strokeStyle="#000080"),e.stroke(),e.beginPath(),k>2?f>=0?("green"==s?e.arc(t,r,l,-Math.PI/2,-Math.PI/2-c,!0):e.arc(t,r,l,-Math.PI/2,-Math.PI/2+c),"purple"==s?e.strokeStyle="#ff0000":"green"==s?e.strokeStyle="#f0ff05":("red"==s||"blue"==s)&&(e.strokeStyle="#ff0000")):(e.arc(t,r,l,-Math.PI/2,-Math.PI/2-c,!0),"purple"==s?e.strokeStyle="#33cc33":"green"==s?e.strokeStyle="#e6ff00":"red"==s?e.strokeStyle="#ff1a1a":"blue"==s&&(e.strokeStyle="#ff0000")):k>1?f>=0?("green"==s?e.arc(t,r,l,-Math.PI/2,-Math.PI/2-c,!0):e.arc(t,r,l,-Math.PI/2,-Math.PI/2+c),"purple"==s?e.strokeStyle="#ff0000":"green"==s?e.strokeStyle="#66ff66":("red"==s||"blue"==s)&&(e.strokeStyle="#ff0000")):(e.arc(t,r,l,-Math.PI/2,-Math.PI/2-c,!0),"purple"==s?e.strokeStyle="#33cc33":"green"==s?e.strokeStyle="#e6ff00":"red"==s?e.strokeStyle="#ff1a1a":"blue"==s&&(e.strokeStyle="#ff0000")):f>=0?("green"==s?e.arc(t,r,l,-Math.PI/2,-Math.PI/2-c,!0):e.arc(t,r,l,-Math.PI/2,-Math.PI/2+c),"purple"==s?e.strokeStyle="#b54dff":"green"==s?e.strokeStyle="#33cc33":"red"==s?e.strokeStyle="#ffb833":"blue"==s?e.strokeStyle="#3333ff":"gray"==s&&(e.strokeStyle="#aaaaaa")):(e.arc(t,r,l,-Math.PI/2,-Math.PI/2-c,!0),"purple"==s||"green"==s?e.strokeStyle="#33cc33":"red"==s?e.strokeStyle="#ffb833":"blue"==s&&(e.strokeStyle="#3333ff")),e.lineWidth=10,e.stroke()}
function updateSecondsInQuarter(e){let t=new Date(1e3*e);return Math.floor(t.getTime()/1e3)%900}
function renderThermometer(id,data) {
    const container = getElem(id)
    if (!container) return
    const { s: rawValue,i: trendIcon,m: label,t: time } = data
    const value = parseFloat(rawValue)
    if (isNaN(value)) return
    const trend = parseFloat(trendIcon) ?? 0
    const showLabel = !['zolder_temp','waskamer_temp'].includes(id)
    let min,avg,max
    if (id === 'buiten_temp') {
        min = d.d?.b?.m ?? (value - 5)
        avg = d.d?.b?.a ?? value
        max = d.d?.b?.x ?? (value + 5)
    } else if (id === 'living_temp') { min = 16; avg = 20; max = 23; }
    else if (id === 'badkamer_temp') { min = 12; avg = 20; max = 22; }
    else {
        min = 10; avg = 16; max = 22
        if (d.heating && d.heating.s != null) avg -= Number(d.heating.s)
    }
    const PIXEL_BOTTOM = 20,PIXEL_RANGE = 66,PIXEL_TOP = 94,PIXEL_MID = 54
    const span = Math.max(avg - min,max - avg)
    const scaleMin = avg - span,scaleMax = avg + span
    const mercuryHeight = Math.max(PIXEL_BOTTOM,Math.min(PIXEL_BOTTOM + PIXEL_RANGE,((value - scaleMin) / (scaleMax - scaleMin)) * PIXEL_RANGE + PIXEL_BOTTOM))
    const mercuryTop = PIXEL_TOP - mercuryHeight
    const avgTop = PIXEL_TOP - PIXEL_MID
    const color = getTemperatureColor(value,min,avg,max)
    setStyle(id + '_mercury','top',mercuryTop + 'px')
    setStyle(id + '_mercury','height',mercuryHeight + 'px')
    setStyle(id + '_mercury','background',color)
    setStyle(id + '_avg','top',avgTop + 'px')
    setHTML(id + '_display',`${value.toFixed(1).replace('.',',')}${showLabel ? `<br>${label}%` : ''}`)
    setHTML(id + '_trend',getTrendArrow(trend) || '')
    setStyle(id,'opacity','1')
}
function getTemperatureColor(r,min,avg,max){
	r=Math.max(min,Math.min(max,r))
	let color
	if(r <= avg){
		if(r < min + (avg-min)/3) color="#3366FF"
		else if(r < min + 2*(avg-min)/3) color="#6699FF"
		else color="#FFD700"
	} else {
		if(r < avg + (max-avg)/2) color="#FFC000"
		else color="#FF3300"
	}
	return `linear-gradient(180deg,${color} 0%,${color} 100%)`
}
function getTemperatureColorTxt(r,min,avg,max){
	r=Math.max(min,Math.min(max,r))
	if(r <= avg){
		if(r < min + (avg-min)/3) return "#3366FF"
		else if(r < min + 2*(avg-min)/3) return "#6699FF"
		else return "#FFD700"
	} else {
		if(r < avg + (max-avg)/2) return "#FFC000"
		else return "#FF3300"
	}
}
function getTrendArrow(t) {
    if (t >= 0.1) {
    	const n = Math.max(0, Math.min(Math.abs(t), 1));
	    const r = Math.round(6 + 40 * n);
        return `<div class="abs trend-arrow"><img src="/images/trendup.png" height="${r}px" width="15px" style="filter: drop-shadow(0 0 3px rgba(255,100,0,0.8));"></div>`;
    } else if (t <= -0.1) {
    	const n = Math.max(0, Math.min(Math.abs(t), 1));
	    const r = Math.round(6 + 40 * n);
        return `<div class="abs trend-arrow"><img src="/images/trenddown.png" height="${r}px" width="15px" style="filter: drop-shadow(0 0 3px rgba(100,150,255,0.8));"></div>`;
    }

    return "";
}
function updateDeviceTime(id) {
    const tijd = d[id]?.t ?? 0
    const status = d[id]?.s
    const delta = newTime - tijd
    if (delta>=0) {
		let kleur
		if (delta < 300) {
			const ratio = delta / 300
			const green = Math.round(255 * ratio)
			kleur = `rgb(255,${green},0)`
		} else if (delta < 1200) {
			const ratio = (delta - 400) / (1200 - 400)
			const blue = Math.round(255 * ratio)
			kleur = `rgb(255,255,${blue})`
		} else if (delta < 82800) {
			const ratio = delta / 10000
			const val = Math.round(255 - (255 - 119) * Math.min(ratio,1))
			kleur = `rgb(${val},${val},${val})`
		} else {
			kleur = "#666"
		}
		if (status !== undefined){
			if (status > 0){
				kleur = "#FFF"
			}
		}
		if (lastState[id] === tijd+kleur) return
		if (delta >= 82800) {
			setText('t' + id,formatDate(tijd))
		} else {
			const dTime = new Date(tijd * 1000)
			const hh = dTime.getHours()
			const mm = ("0" + dTime.getMinutes()).slice(-2)
			setText('t' + id,`${hh}:${mm}`)
		}
		setStyle('t' + id,'color',kleur)
		lastState[id] = tijd+kleur
	}
}
function updateAllDeviceTimes(deviceList){deviceList.forEach(id=>updateDeviceTime(id))}
function log(msg) {
    const id = 'log';
    let entry = getUpdateEntry(id);
    if (!entry.logs) entry.logs = [];

    const d = new Date();
    const hh = d.getHours().toString().padStart(2, '0');
    const mm = d.getMinutes().toString().padStart(2, '0');
    const ss = d.getSeconds().toString().padStart(2, '0');

    // Voeg de nieuwe regel toe aan de wachtrij voor dit frame
    entry.logs.push(`${hh}:${mm}:${ss} ${msg}`);

    requestTick();
}

function shouldRedraw(prev,curr,circleMax,degrees=2){
//	if (forceRedraw) return true;
	if (prev===undefined) return true;
	if ((prev <= 0&&curr > 0) || (prev >= 0&&curr < 0)) return true;
	const threshold=circleMax * (degrees / 360);
	return Math.abs(curr - prev) >= threshold;
}
let client = null;
let monitorTimer = null;
let lastMessageReceived = Date.now();
let lastWakeUp = Date.now();
let initialConnectDone = false
let isReconnecting = false;
let messageBatch = {};
let batchTimeout = null;
const DEAD_TIMEOUT = 2900;
const MONITOR_INTERVAL = 1000;
let totalHandleCalls = 0;
let currentVersion = null;
let offlineTimeout = null;
function connect() {
    if (client && client.connected) return;
    if (!navigator.onLine) {
        setTimeout(connect, 1000);
        return;
    }
    client = mqtt.connect('wss://' + window.location.hostname + ':443/mqtt', {
        clientId: getClientId(),
        clean: true,
        connectTimeout: 1000,
        reconnectPeriod: 500,
        keepalive: 30
    });
    client.on('connect', function () {
        const duration = Date.now() - lastWakeUp;
    	if (offlineTimeout) {
			clearTimeout(offlineTimeout);
			offlineTimeout = null;
		}
		log(`✅ MQTT Verbonden in ${duration}ms`);
        initialConnectDone = true;
        isReconnecting = false;
        client.subscribe("d/#");
        removeClass('clock','offline')
        addClass('clock','online')
        startMonitor();
    });
    client.on('message', (topic, payload) => onMessage(String(topic), payload));
    client.on('reconnect', () => {
		lastWakeUp = Date.now();
		log("🔄 MQTT herverbinden...");
	});
	client.on('close', () => {
		log("ℹ️ MQTT Verbinding gesloten (Close)");
		setTimeout(() => {
			if (!client || !client.connected) stopMonitor();
		}, 350);
	});
	client.on('offline', () => {
		log("⚠️ MQTT Client is offline");
		setTimeout(() => {
			if (!client || !client.connected) stopMonitor();
		}, 350);
	});
	client.on('error', (err) => {
		log("❌ MQTT Fout: " + err.message);
		stopMonitor();
	});
}
function onMessage(topic, payload) {
    lastMessageReceived = Date.now();
    if (topic === "d/floorplan_version") {
		const serverVersion = parseInt(payload);
		const s = serverVersion.toString();
		const readable = `${s.substring(6,8)}/${s.substring(4,6)} ${s.substring(8,10)}:${s.substring(10,12)}:${s.substring(12,14)}`;
		if (currentVersion === null) {
			currentVersion = serverVersion;
			log(`📌 App Versie: ${readable}`);
		} else if (serverVersion > currentVersion) {
			log(`🚀 Update gevonden (${readable}), herladen...`);
			forceAppUpdate();
		}
		return;
	}
    const device = topic.split("/").pop();
    if (payload && typeof payload === "object") {
        if (payload.type === "Buffer" && Array.isArray(payload.data)) {
            payload = String.fromCharCode(...payload.data);
        } else if (payload instanceof Uint8Array) {
            payload = new TextDecoder().decode(payload);
        }
    }
    if (typeof payload === "string" && (payload.startsWith("{") || payload.startsWith("["))) {
        try {
            const parsed = JSON.parse(payload);
            payload = parsed.val || parsed.value || parsed.svalue || parsed;
        } catch {
            log(`⚠️ Invalid JSON: ${topic} ${payload}`);
            return;
        }
    }
    if (typeof payload === "string" || typeof payload === "number") {
        const n = Number(payload);
        if (!Number.isNaN(n)) {
            payload = n;
        }
    }
    messageBatch[device] = payload;
    if (!batchTimeout) {
		batchTimeout = setTimeout(() => {
            for (const [deviceId, data] of Object.entries(messageBatch)) {
				requestAnimationFrame(() => {
					handleResponse(deviceId, data);
				});
			}
			messageBatch = {};
			batchTimeout = null;
		}, 150);
	}
}
function isIPad() {
    return (
        navigator.platform === "MacIntel" &&
        navigator.maxTouchPoints > 1
    );
}
function cleanup(reason = "") {
    stopMonitor();
    if (client) {
        client.end(true);
        client = null;
    }
}
function hardReconnect(reason = "") {
	log('hardReconnect '+reason)
	if (offlineTimeout) {
        clearTimeout(offlineTimeout);
        offlineTimeout = null;
    }
    isReconnecting = true;
    cleanup(reason);
    connect();
}
function getClientId() {
    let id = localStorage.getItem("mqttClientId")
    if (!id) {
        id = "web_" + Math.random().toString(16).slice(2, 10)
        localStorage.setItem("mqttClientId", id)
    }
    return id
}
function startMonitor() {
    stopMonitor();
    monitorTimer = setInterval(() => {
        if (document.hidden || !client || !client.connected) return;
        const silence = Date.now() - lastMessageReceived;
        if (silence > DEAD_TIMEOUT) {
            console.warn(`⚠️ MQTT Stilte: ${Math.round(silence / 1000)}s - Reconnecting...`);
            hardReconnect(`⚠️ MQTT Stilte: ${Math.round(silence / 1000)}s - Reconnecting...`);
        }
    }, MONITOR_INTERVAL);
}
function stopMonitor() {
    if (monitorTimer) {
        clearInterval(monitorTimer);
        monitorTimer = null;
    }
    if (!offlineTimeout) {
        offlineTimeout = setTimeout(() => {
            if (!isReconnecting && (!client || !client.connected)) {
                removeClass('clock', 'online');
                addClass('clock', 'offline');
                log("🔴 Status: Echt offline (Grace period verlopen)");
            }
            offlineTimeout = null;
        }, 300);
    }
}
document.addEventListener("DOMContentLoaded", () => {
    lastMessageReceived = Date.now()
    connect()
})
window.addEventListener("pageshow", e => {
	lastWakeUp = Date.now();
	if (client && client.connected) {
        addClass('clock', 'online');
        return;
    } else {
    	removeClass('clock', 'online');
		removeClass('clock', 'offline');
	}
    if (!initialConnectDone && isIPad()) {
        hardReconnect("pageshow")
        return
    }
    if (e.persisted) {
        hardReconnect("bfcache")
    } else {
        const ua = navigator.userAgent || navigator.vendor || window.opera
        const isiOS = /iPad|iPhone|iPod/.test(ua) && !window.MSStream
        if (isiOS) {
            hardReconnect("pageshow")
        }
    }
})
window.addEventListener("offline", () => {
    log("🌐 Offline")
    cleanup("offline")
	removeClass('clock','online')
	addClass('clock','offline')
    fetchajax=true
})

window.addEventListener("online", () => {
    log("🌐 Online")
    hardReconnect("online")
})

window.addEventListener("pagehide", () => {
    log("📄 Pagehide")
    cleanup("pagehide")
    fetchajax=true
})

function initPlaceholders() {
	drawCircle('avgtimecircle', 0, 0, 82, 'gray')
	drawCircle('avgcircle', 0, 0, 90, 'purple')
	drawCircle('eleccircle', 0, 0, 90, 'purple')
	drawCircle('zonvcircle', 0, 0, 90, 'green')
	drawCircle('gascircle', 0, 0, 90, 'red')
	drawCircle('netcircle', 0, 0, 90, 'purple')
	drawCircle('chargecircle', 0, 100, 82, 'gray')
	drawCircle('zoncircle', 0, 0, 90, 'green')
	drawCircle('batcircle', 0, 0, 90, 'purple')
	drawCircle('totalcircle', 0, 0, 90, 'purple')
}
window.onload = initPlaceholders;
if('serviceWorker' in navigator){navigator.serviceWorker.register('sw.js?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . "/sw.js") ?>').then(r=>{r.onupdatefound=()=>{const n=r.installing;n.onstatechange=()=>{n.state==='installed'&&navigator.serviceWorker.controller&&(sessionStorage.setItem('pwa_upd','1'),location.reload())}}});navigator.serviceWorker.oncontrollerchange=()=>{sessionStorage.getItem('pwa_upd')||(sessionStorage.setItem('pwa_upd','1'),location.reload())}}
async function forceReset() {
	const storageKey = 'app_version_' + window.location.hostname;
	const localVersion = localStorage.getItem(storageKey);
	try {
		const registrations = await navigator.serviceWorker.getRegistrations();
		for (let reg of registrations) { await reg.unregister(); }
		if ('caches' in window) {
			const keys = await caches.keys();
			await Promise.all(keys.map(key => caches.delete(key)));
		}
		await db.delete();
		localStorage.clear();
		if (localVersion !== null) {
			localStorage.setItem(storageKey, localVersion);
		}
		window.location.href = window.location.pathname + '?clear=' + Date.now();
	} catch (err) {
		alert("Reset mislukt: " + err);
	}
}
