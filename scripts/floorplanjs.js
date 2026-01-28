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
function getElem(id){
    let vid=id
    if (!(vid in deviceElems)){
        deviceElems[vid]=document.getElementById(vid) || null
    }
    return deviceElems[vid]
}
let rafQueue=[]
let rafScheduled=false
function schedule(fn){
    rafQueue.push(fn)
    if (!rafScheduled){
        rafScheduled=true
        requestAnimationFrame(()=>{
            for (const f of rafQueue) f()
            rafQueue=[]
            rafScheduled=false
        });
    }
}
function setTime(){
    const date = new Date(Date.now());
	const hours = date.getHours();
	const minutes = ("0" + date.getMinutes()).slice(-2);
	const seconds = ("0" + date.getSeconds()).slice(-2);
	if(setText('time',`${hours}:${minutes}:${seconds}`)) {
		newTime=date/1000
		rawSeconds = updateSecondsInQuarter(newTime);
		avgTimeOffsetRaw = localStorage.getItem('avgTimeOffset');
		avgTimeOffset = Number(avgTimeOffsetRaw);
		if (avgTimeOffsetRaw === null || avgTimeOffsetRaw === "null" || !Number.isFinite(avgTimeOffset)) {
			avgTimeOffset = -10;
		}
		if (lastAvgValue !== null && lastAvgValue > 0 && d.avg < lastAvgValue) {
			expectedResetTime = avgTimeOffset < 0 ? 900 + avgTimeOffset : avgTimeOffset;
			minResetTime = (expectedResetTime - 10 + 900) % 900;
			maxResetTime = (expectedResetTime + 10) % 900;
			inWindow = false;
			if (minResetTime < maxResetTime) {
				inWindow = rawSeconds >= minResetTime && rawSeconds <= maxResetTime;
			} else {
				inWindow = rawSeconds >= minResetTime || rawSeconds <= maxResetTime;
			}
			if (inWindow) {
				proposedOffset = rawSeconds <= 20 ? rawSeconds : rawSeconds - 900;
				offsetDifference = proposedOffset - avgTimeOffset;
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
		correctedSeconds = (rawSeconds - avgTimeOffset + 900) % 900;
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
function setText(id,text){
    if (lastValues[id]===text) return false
    lastValues[id]=text
    schedule(()=>{
        const el=getElem(id)
        if (el) {
        	el.textContent=text
        }
    });
    return true
}
function setHTML(id,html){
    const key=id + ':html'
    if (lastValues[key]===html) return false
    lastValues[key]=html
    schedule(()=>{
        const el=getElem(id)
        if (el) el.innerHTML=html
    });
    return true
}
function setStyle(id,prop,value){
    const key=id + ':style:' + prop
    if (value == null){
        delete lastValues[key]
        schedule(()=>{
            const el=getElem(id)
            if (el) el.style.removeProperty(prop)
        })
        return
    }
    if (lastValues[key]===value) return
    lastValues[key]=value
    schedule(()=>{
        const el=getElem(id)
        if (el) el.style[prop]=value
    })
}
function setAttr(id,attr,value){
    const key=id + ':attr:' + attr
    if (lastValues[key]===value) return
    lastValues[key]=value
    schedule(()=>{
        const el=getElem(id)
        if (el) el.setAttribute(attr,value)
    })
}
function addClass(id,cls){
    const key=id + ':cls:' + cls
    if (lastValues['a'+key]===cls) return
    lastValues['a'+key]=cls
    delete lastValues['r'+key]
    schedule(()=>{
        const el=getElem(id)
        if (el&&!el.classList.contains(cls)){
            el.classList.add(cls)
        }
    })
}
function removeClass(id,cls){
	const key=id + ':cls:' + cls
    if (lastValues['r'+key]===cls) return
    lastValues['r'+key]=cls
    delete lastValues['a'+key]
    schedule(()=>{
        const el=getElem(id)
        if (el&&el.classList.contains(cls)){
            el.classList.remove(cls)
        }
    })
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
			mintemp=(Math.round(v.b.m * 100) / 100).toFixed(1);
			maxtemp=(Math.round(v.b.x * 100) / 100).toFixed(1);
			if(setText('mint',mintemp.toString().replace(/[.]/,","))===true) {
				if (mintemp>0) mincolor=berekenKleurRood(mintemp,30,10)
				else mincolor=berekenKleurBlauw(mintemp,-5,10)
				setStyle('mint','color',mincolor)
			}
			if(setText('maxt',maxtemp.toString().replace(/[.]/,","))===true) {
				if (maxtemp>0) maxcolor=berekenKleurRood(maxtemp,35,15)
				else maxcolor=berekenKleurBlauw(maxtemp,5,10)
				setStyle('maxt','color',maxcolor)
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
			el=getElem("wind")
			if(setHTML('wind',v.w.toString().replace(/[.]/,","))===true) setStyle('wind','color',berekenKleurRood(v.w,50,20))
			setAttr('icon','src',"/images/" + v.i + ".png")
			mintemp=(Math.round(v.mint * 100) / 100).toFixed(1);
			if(setText('mintemp',mintemp.toString().replace(/[.]/,","))===true) {
				if (mintemp>0) mincolor=berekenKleurRood(mintemp,30,10)
				else mincolor=berekenKleurBlauw(mintemp,-5,10)
				setStyle('mintemp','color',mincolor)
			}
			maxtemp=(Math.round(v.maxt * 100) / 100).toFixed(1);
			if(setText('maxtemp',maxtemp.toString().replace(/[.]/,","))) {
				if (maxtemp>0) maxcolor=berekenKleurRood(maxtemp,35,15)
				else maxcolor=berekenKleurBlauw(maxtemp,5,10)
				setStyle('maxtemp','color',maxcolor)
			}
			if(setText('uv',v.uv.toString().replace(/[.]/,","))===true) setStyle('uv','color',berekenKleurRood(v.uv,11,2))
			if(setText('uvm',v.uvm.toString().replace(/[.]/,","))===true) setStyle('uv','color',berekenKleurRood(v.uvm,11,2))
			if(setText('buien',v.b)===true) setStyle('buien','color',berekenKleurBlauw(v.b,100))
			return
		case 'a':
			if(setText('avgvalue',v)===true) {
				if (shouldRedraw(prevavg,v,2500,5)) {
					setStyle('avgvalue', 'color', berekenKleurRood(v, 5000));
					drawCircle('avgcircle', v, 2500, 90, 'purple');
				}
			}
			return
		case 'elec':
			val = parseFloat(v);
			avg = parseFloat(d.elecavg);
			euro = val * euroelec;
			html = euro.toFixed(2).toString().replace(/[.]/, ",");
			if (d.net > 0) setStyle('elecvalue', 'color', berekenKleurRood(v, d.elecavg * 2));
			else setStyle('elecvalue', 'color', berekenKleurGroen(-v, d.elecavg * 2));
			drawCircle('eleccircle', val, avg, 90, 'purple');
			valStr = val.toFixed(2).toString().replace(/[.]/, ",");
			setHTML('elecvalue', html + `<br><span class="units">${valStr}</span>`);
			sessionStorage.setItem('elec', v)
			return
		case 'alwayson':
			setText('alwayson', v + "W");
			return
		case 'zon':
			val = parseFloat(v);
			euro = val * (euroelec + 0.45);
			html = euro.toFixed(2).toString().replace(/[.]/, ",");
			setStyle('zonvvalue', 'color', berekenKleurGroen(-val, d.zonref / 10));
			drawCircle('zonvcircle', val, d.zonref, 90, 'green');
			valStr = val.toFixed(2).toString().replace(/[.]/, ",");
			setHTML('zonvvalue', html + `<br><span class="units">${valStr}</span>`);
			return
		case 'gas':
			euro = v * 11.5 * eurogas;
			html = euro.toFixed(2).toString().replace(/[.]/, ",");
			setStyle('gasvalue', 'color', berekenKleurRood(v, d.gasavg * 2000));
			drawCircle('gascircle', v, d.gasavg, 90, 'red');
			valStr = v.toFixed(2).toString().replace(/[.]/, ",");
			setHTML('gasvalue', html + `<br><span class="units">${valStr}</span>`);
			return
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
			return
		case 'alexslaapt':
			if(v.s==1) {
				addClass('zalex','securedalex')
				txt='Alex slaapt '
			} else {
				removeClass('zalex','securedalex')
				txt='Alex wakker '
			}
			if(v.t>(newTime-82800)){
				date=new Date(v.t*1000)
				hours=date.getHours()
				minutes="0"+date.getMinutes()
				html=hours+':'+minutes.substr(-2)
				txt+=html
			}else html=""
			setText("t"+device,html)
			return
		case 'dag':
			s=v.s
			if (s < -10 || s > 10) s=Math.round(s)
			setText('dag',s)
			return
		case 'heating':
			html='<img src="/images/arrowdown.png" class="i60" alt="Open">'
			if(v.s==0)html+=''
			else if(v.s==-2)html+='<img src="/images/Cooling.png" class="i40" alt="Cooling">'
			else if(v.s==-1)html+='<img src="/images/Cooling_grey.png" class="i40" alt="Cooling">'
			else if(v.s==1)html+='<img src="/images/Cooling_red.png" class="i40" alt="Elec">'
			else if(v.s==2)html+='<img src="/images/AircoGas.png" class="i40" alt="AircoGas">'
			else if(v.s==3)html+='<img src="/images/GasAirco.png" class="i40" alt="GasAirco">'
			else if(v.s==4)html+='<img src="/images/Gas.png" class="i40" alt="Gas">'
			setHTML('heatingbutton',html)
			setHTML('oheatingbutton',html)
			if(v.s==0)html='<img src="images/close.png" height="40" width="40px" onclick="heating();"></td><td align="left" height="40" width="40px" style="line-height:18px" onclick="heating()">Neutral</td>'
			else if(v.s==-2)html='<img src="images/Cooling.png" onclick="heating();"></td><td align="left" height="60" width="80px" style="line-height:18px" onclick="heating()">Airco<br>cooling</td>'
			else if(v.s==-1)html='<img src="images/Cooling_grey.png" onclick="heating();"></td><td align="left" height="60" width="80px" style="line-height:18px" onclick="heating()">Passive<br>cooling</td>'
			else if(v.s==1)html='<img src="images/Cooling_red.png" onclick="heating();"></td><td align="left" height="60" width="80px" style="line-height:18px" onclick="heating()">Airco<br>heating</td>'
			else if(v.s==2)html='<img src="images/GasAirco.png" onclick="heating();"></td><td align="left" height="60" width="80px" style="line-height:18px" onclick="heating()">Gas-Airco<br>heating</td>'
			else if(v.s==3)html='<img src="images/Gas.png" onclick="heating();"></td><td align="left" height="60" width="80px" style="line-height:18px" onclick="heating()">Gas heating</td>'
			setHTML('trheating',html)
			return
		case 'sirene':
			if(v.s==1)html='<img src="images/alarm_On.png" width="500px" height="auto" alt="Sirene" onclick="ajaxcontrol(\'sirene\',\'sw\',\'Off\')"><br>'+device
			else html=""
			setHTML('sirene',html)
			return
		case 'brander':
			const heatingmode=d.heating?.s ?? 0;
			if(v.s==0)html='<img src="images/fire_Off.png" onclick="ajaxcontrol(\'brander\',\'sw\',\'On\')">'
			else html='<img src="images/fire_On.png" onclick="ajaxcontrol(\'brander\',\'sw\',\'Off\')">'
			setHTML('brander',html)
			updateDeviceTime(device)
			html='<img src="/images/arrowdown.png" class="i60" alt="Open">'
			if(heatingmode==0)html+=''
			else if(heatingmode==-2)html+='<img src="/images/Cooling.png" class="i40" alt="Cooling">'
			else if(heatingmode==-1)html+='<img src="/images/Cooling_grey.png" class="i40" alt="Cooling">'
			else if(heatingmode==1)html+='<img src="/images/Cooling_red.png" class="i40" alt="Elec">'
			else if(heatingmode==4){
				if(v.s==1)html+='<img src="/images/fire_On.png" class="i40" id="branderfloorplan" alt="Gas">'
				else html+='<img src="/images/fire_Off.png" class="i40" alt="Gas">'
			}
			setHTML('heating',html)
			if(heatingmode>=1){
				if(v.s==0) setAttr('branderfloorplan','src',"/images/fire_Off.png")
				else setAttr('branderfloorplan','src',"/images/fire_On.png")
			} else if(heatingmode>0){
				if(v.s==0)setAttr('branderfloorplan','src',"/images/gaselec_Off.png")
				else setAttr('branderfloorplan','src',"/images/gaselec_Off.png")
			} else setAttr('branderfloorplan','src',"")
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
	            case 'sc':
	            	html='';
					const isOn=(v.s > 0);
					const iconName=v.i || 'l';
					const specialDevices=["water","regenpomp","steenterras","tuintafel","terras","tuin","auto","media","nas","zetel","grohered","kookplaat","boseliving","bosekeuken","ipaddock","mac","poort"];
					const confirmDevices=["ipaddcok","mac","daikin","grohered","kookplaat","media","boseliving","bosekeuken","poort"];
					const directControlDevices=["regenpomp","nas"];
					if (device == "daikin"){
						if (!isOn){
							setText('daikin_kwh','')
						} else {
							setText('daikin_kwh',v.p+" W")
							setStyle('daikin_kwh','color',berekenKleurRood(v.p,2000,400))
						}
					}
			//			if (device == "water"){
			//				prev=localStorage.getItem('water');
			//				if (prev!==v.s) localStorage.setItem('water',v.s);
			//				prev=localStorage.getItem('watermode');
			//				if (prev!==v.m) localStorage.setItem('watermode',v.m);
			//			}
					onclickHandler='';
					if (confirmDevices.includes(device)){
						onclickHandler=`confirmSwitch('${device}')`;
					} else if (directControlDevices.includes(device)){
						const newState=isOn ? 'Off' : 'On';
						onclickHandler=`ajaxcontrol('${device}','sw','${newState}');setView('floorplan');`;
					} else {
						const newState=isOn ? 'Off' : 'On';
						onclickHandler=`ajaxcontrol('${device}','sw','${newState}')`;
					}
					if (isOn){
						if (iconName == 'l'){
							html='<img src="/images/l_On.png" id="' + device + '" class="img100" />';
							html += '<div class="dimmercircle" onclick="' + onclickHandler + '">';
							html += '<svg viewBox="0 0 36 36">';
							html += '<path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />';
							html += '<path class="circle" stroke-dasharray="100,100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />';
							html += '</svg>';
							html += '</div>';
						} else {
							html='<img src="/images/' + iconName + '_On.png" id="' + device + '" onclick="' + onclickHandler + '" />';
						}
					} else {
						html='<img src="/images/' + iconName + '_Off.png" id="' + device + '" onclick="' + onclickHandler + '" />';
					}
					if (specialDevices.includes(device)){
						if(device=='steenterras') naam='steen'
						else if(device=='tuintafel') naam='hout'
						else naam=device
						html += '<br>' + naam;
						if (["water","regenpomp","auto","media","nas","mac","ipaddock","boseliving","bosekeuken","grohered","kookplaat","zetel","poort"].includes(device)){
							if (v.t > (newTime - 82800)){
								const date=new Date(v.t * 1000);
								const hours=date.getHours();
								const minutes=("0" + date.getMinutes()).substr(-2);
								html += '<br>' + hours + ':' + minutes;
								setStyle(device,'color','#CCC')
							} else {
								html += '<br>' + formatDate(v.t);
								setStyle(device,'color','#777')
							}
						}
					}
					setHTML(device,html);
					if (device=="poort"&&d.weg?.s>0) setStyle('poort','display','none')
					else if (device=="weg"&&d.weg.s==0) setStyle('poort','display','block')
					return
				case 'd':
	            case 'hd':
	            	html='';
					level=parseInt(v.s) || 0;
					if (level == 0){
						html='<img src="/images/l_Off.png" class="img100">';
					} else {
						html='<img src="/images/l_On.png" class="img100">';
						html += '<div class="dimmercircle">';
						html += '<svg viewBox="0 0 36 36">';
						html += '<path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />';
						html += '<path class="circle" stroke-dasharray="' + level + ',100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />';
						html += '</svg>';
						html += '<div class="dimmer-percentage">' + level + '%</div>';
						html += '</div>';
					}
					if (device == "terras"){
						html += 'terras';
					}
					setHTML(device,html);
					return
				case 'b':
					if(device==="bose101"||device==="bose106"){
						if(v.m==0)html=''
						else{
							if(v.s==1)html="<a href='javascript:navigator_Go(\"floorplan.bose.php?ip="+device+"\");'><img src=\"images/ST30_On.png\" id=\""+device+"\" alt=\"bose\"></a>"
							else html="<a href='javascript:navigator_Go(\"floorplan.bose.php?ip="+device+"\");'><img src=\"images/ST30_Off.png\" id=\""+device+"\" alt=\"bose\"></a>"
						}
					}else{
						if(v.m==0)html=''
						else{
							if(v.s==1)html="<a href='javascript:navigator_Go(\"floorplan.bose.php?ip="+device+"\");'><img src=\"images/ST10_On.png\" id=\""+device+"\" alt=\"bose\"></a>"
							else html="<a href='javascript:navigator_Go(\"floorplan.bose.php?ip="+device+"\");'><img src=\"images/ST10_Off.png\" id=\""+device+"\" alt=\"bose\"></a>"
						}
					}
					setHTML(device,html)
					return
				case 'r':
					status=100 - v.s;
					perc=(status < 100) ? (status / 100) * 0.7 : 1;
					if(device!='zoldertrap') {
						const opts=v.i.split(",");
						rollerTop=parseInt(opts[0]);
						indicatorSize=0;
						if (status == 0){
							indicatorSize=0;
						} else if (status > 0){
							indicatorSize=(parseFloat(opts[2]) * perc) + 8;
							if (indicatorSize > parseFloat(opts[2])){
								indicatorSize=parseFloat(opts[2]);
							}
							rollerTop=parseInt(opts[0]) + parseInt(opts[2]) - indicatorSize;
							addClass(device,'yellow')
						} else {
							indicatorSize=parseFloat(opts[2]);
						}
						if (opts[3] == "P"){
							setStyle(device,'top',rollerTop + 'px')
							setStyle(device,'left',opts[1] + 'px')
							setStyle(device,'width','8px')
							setStyle(device,'height',indicatorSize + 'px')
						} else if (opts[3] == "L"){
							setStyle(device,'top',opts[0] + 'px')
							setStyle(device,'left',opts[1] + 'px')
							setStyle(device,'width',indicatorSize + 'px')
							setStyle(device,'height','9px')
						}
					}
					html='';
					if (v.s == 100){
						html='<img src="/images/arrowgreendown.png" class="i48">';
					} else if (v.s == 0){
						html='<img src="/images/arrowgreenup.png" class="i48">';
					} else {
						html='<img src="/images/circlegrey.png" class="i48">';
						html += '<div class="fix center dimmerlevel" style="position:absolute;top:19px;left:1px;width:46px;letter-spacing:6px;">';
						html += '<font size="5" color="#CCC">' + v.s + '</font>';
						html += '</div>';
					}
					html += '</div>';
					if (v.t > (newTime - 82800)){
						const date=new Date(v.t * 1000);
						const hours=date.getHours();
						const minutes=("0" + date.getMinutes()).substr(-2);
						html += '<br><div id="t' + device + '">' + hours + ':' + minutes + '</div>';
					} else {
						html += '<br><div id="t' + device + '">' + formatDate(v.t) + '</div>';
					}
					setHTML('R'+device,html)
				case 'p':
					d[device]=v
					temp=device.toString().replace("pir","")
					updateDeviceTime(device)
					if(temp=="hall"||temp=="living"){
						if(v.s==1){
							addClass('z'+temp+'a','motion')
							addClass('z'+temp+'b','motion')
						}else{
							removeClass('z'+temp+'a','motion')
							removeClass('z'+temp+'b','motion')
						}
					}else{
						if(v.s==1) addClass('z'+temp,'motion')
						else removeClass('z'+temp,'motion')
					}
					return
				case 'c':
					if(device!=='raamhall') {
						d[device]=v
						updateDeviceTime(device)
						if(v.s==1) addClass(device,'red')
						else removeClass(device,'red')
					}
					return
				case 't':
					if(device==='buiten_temp') {
					 if(d.w?.mint!==undefined&&d.d?.b?.m!=undefined) renderThermometer(device,v)
					}else renderThermometer(device,v)
					return
				case 'th':
					temp=d[device.toString().replace("_set","_temp")]?.s ?? 0
					dif=temp-v.s
					if(dif>0.3)circle="hot"
					else if(dif<-0.3)circle="cold"
					else circle="grey"
					if(v.s>=20)center="red"
					else if(v.s>19)center="orange"
					else if(v.s>14)center="grey"
					else center="blue"
					html='<img src="/images/thermo'+circle+center+'.png" class="i48" alt="">'
					html+='<div class="abs center" style="top:35px;left:11px;width:26px;">'
					if(v.m==1){
						html+='<font size="2" color="#222">'+v.s.toString().replace(/[.]/,",")+'</font></div>'
						html+='<div class="abs" style="top:2px;left:2px;z-index:-100;background:#b08000;width:44px;height:44px;border-radius:45px;"></div>'
					}else html+='<font size="2" color="#CCC">'+v.s.toString().replace(/[.]/,",")+'</font></div>'
					if(v.t>(newTime-82800)){
						date=new Date(v.t*1000)
						hours=date.getHours()
						minutes="0"+date.getMinutes()
						html+='<br><div id="t'+device+'">'+hours+':'+minutes.substr(-2)+'</div>'
					}else html+='<br><div id="t'+device+'">'+formatDate(v.t)+'</div>';
					setHTML(device,html)
					return
			}
	}
	if (device==='n') {
		if(setText('netvalue', v)){
			if (shouldRedraw(prevnet,v,2500,5)) {
				prevnet=v
				if (v < 0) kleur=berekenKleurGroen(-v, 5000)
				else kleur=berekenKleurRood(v, 5000)
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
			if (shouldRedraw(prevzon,v,90)) {
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
	total = d.n + d.z - d.b;
	if (device==='n'||device==='z'||device==='b'){
		if(setText('totalvalue', total)){
			if (shouldRedraw(prevtotal,total,2500)) {
				prevtotal=total
				kleur=berekenKleurRood(d.n, 5000)
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
	level=d[device+'_set'].s;
	heatingset=d.heating.s
	temp=d[device+'_temp'].s
	$mode=d[device+'_set'].m
	if (device==='living'){
		min=16;
		avg=20;
		max=23;
	} else if (device==='badkamer'){
		min=12;
		avg=20;
		max=22;
	} else {
		min=10;
		avg=16;
		max=22;
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
 	current=d[device].s
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
	current=d[device].s
	let currentInt=parseInt(current);
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
	html='<div class="dimmer" ><div style="min-height:220px">'
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
	html='<div class="dimmer" ><div style="min-height:220px"><div id="message" class="dimmer">'
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
	html='<div class="dimmer" ><div style="min-height:220px"><div id="message" class="dimmer"><br><h1>'+device+'</h1><br>'
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
	if(socket) socket.end(true);
	window.location.assign(n)
}
function ajaxcontrol(a,o,n){fetch(`http://192.168.2.2/ajax.php?device=${a}&command=${o}&action=${n}`,{cache:'no-store'}).catch(err=>console.warn('ajaxcontrol error',err));}
function ajaxcontrolbose(a,o,n){fetch(`http://192.168.2.2/ajax.php?boseip=${a}&command=${o}&action=${n}`,{cache:'no-store'}).catch(err=>console.warn('ajaxcontrolbose error',err));}
function floorplanbose(){ajaxbose($ip)(),myAjaxmedia=$.setInterval((function(){ajaxbose($ip)}),1e3)}
function pad(e,n){return len=n-(""+e).length,(len>0?new Array(++len).join("0"):"")+e}
function fix(){var e=this,n=e.parentNode,i=e.nextSibling;n.removeChild(e),setTimeout((function(){n.insertBefore(e,i)}),0)}
function sliderToDimmer(r){return r<=50?r/50*25:25+(r-50)/50*75}
function dimmerToSlider(r){return r<=25?r/25*50:50+(r-25)/75*50}
function setThermostatTemp(t,o){ajaxcontrol(t,"setpoint",o),setView('floorplanheating')}
function setRollerLevel(e,l){ajaxcontrol(e,"roller",l),setView('floorplanheating')}
function setDimmerLevel(e,i){ajaxcontrol(e,"dimmer",i),setView('floorplan')}
function initDimmerSlider(e){
	const t=getElem("sliderTrack"),
	n=getElem("sliderThumb"),
	o=getElem("sliderFill"),
	s=getElem("dimmerValue")
	if(!t)return
	let i=!1,d=parseInt(sessionStorage.getItem(e))||0,c=null;function r(e){const t=Math.round(e);return s&&(0===t?(s.textContent="Uit",s.classList.add("off")):(s.textContent=t+"%",s.classList.remove("off"))),document.querySelectorAll(".level-btn").forEach((e=>{const n=parseInt(e.dataset.level);e.classList.remove("active","below"),n===t?e.classList.add("active"):n<t&&e.classList.add("below")})),d=t,t}function l(t){window.dimmerLocked[e]=!0,ajaxcontrol(e,"dimmer",t),sessionStorage.setItem(e,t)}function a(e){e.cancelable&&e.preventDefault();const s=t.getBoundingClientRect(),d=(e.clientX||(e.touches&&e.touches[0]?e.touches[0].clientX:null))-s.left;if(null===d)return;const a=Math.max(0,Math.min(100,d/s.width*100)),u=(m=a)<=50?m/50*25:25+(m-50)/50*75;var m;const v=r(Math.round(u));return o.style.width=a+"%",n.style.left=a+"%",i&&(c&&clearTimeout(c),c=setTimeout((()=>l(v)),250)),v}function u(t){i=!0,window.dimmerLocked[e]=!0,n.style.cursor="grabbing"}function m(){i&&(i=!1,n.style.cursor="grab",c&&clearTimeout(c),l(d),setTimeout((()=>{window.dimmerLocked[e]=!1}),1e3))}n.addEventListener("mousedown",u),document.addEventListener("mousemove",(e=>{i&&a(e)})),document.addEventListener("mouseup",m),n.addEventListener("touchstart",(e=>{e.preventDefault(),u()}),{passive:!1}),document.addEventListener("touchmove",(e=>{i&&(e.preventDefault(),a(e))}),{passive:!1}),document.addEventListener("touchend",(e=>{i&&(e.preventDefault(),m())}),{passive:!1}),document.addEventListener("touchcancel",(e=>{i&&(e.preventDefault(),m())}),{passive:!1}),t.addEventListener("click",(function(e){if(!i){l(a(e))}}))}
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
function getTrendArrow(t){
	const n=Math.min(Math.abs(t),10),r=Math.round(6+60*n);
	return t>=0.1?`<div class="abs trend-arrow"><img src="/images/trendup.png" height="${r}px" width="15px" style="filter: drop-shadow(0 0 3px rgba(255,100,0,0.8));"></div>`:t<=-0.1?`<div class="abs trend-arrow"><img src="/images/trenddown.png" height="${r}px" width="15px" style="filter: drop-shadow(0 0 3px rgba(100,150,255,0.8));"></div>`:""}
function updateDeviceTime(id) {
    tijd = d[id]?.t ?? 0
    status = d[id]?.s
    delta = newTime - tijd
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
    schedule(() => {
        const el = getElem('log');
        if (!el) return;
        let ts = Date.now();
        const d  = new Date(ts);
        const hh = d.getHours().toString().padStart(2,'0');
        const mm = d.getMinutes().toString().padStart(2,'0');
        const ss = d.getSeconds().toString().padStart(2,'0');
        el.textContent +=
            `${hh}:${mm}:${ss} ${msg}\n`;

//        el.scrollTop = el.scrollHeight;
    });
}
function shouldRedraw(prev,curr,circleMax,degrees=2){
//	if (forceRedraw) return true;
	if (prev===undefined) return true;
	if ((prev <= 0&&curr > 0) || (prev >= 0&&curr < 0)) return true;
	const threshold=circleMax * (degrees / 360);
	return Math.abs(curr - prev) >= threshold;
}
let socket = null
let monitorTimer = null
let lastMessageReceived = 0
let isConnecting = false
let initialConnectDone = false  // ← NIEUW
const DEAD_TIMEOUT = 2900
const MONITOR_INTERVAL = 1000

function connect() {
    if (socket || isConnecting) return;
	ajax()
    isConnecting = true;
    log("🔌 MQTT connect...");

    const doConnect = () => {
        socket = mqtt.connect("ws://192.168.2.22:9001/mqtt", {
            protocolVersion: 4,
            reconnectPeriod: 0,
            connectTimeout: 1200,
            clean: false,
            clientId: getClientId()
        });

        socket.on("connect", onConnect);
        socket.on("message", onMessage);
        socket.on("close", () => hardReconnect("close"));
        socket.on("error", () => hardReconnect("error"));
    };

    // iPad WebKit workaround
    if (isIPad()) {
        requestAnimationFrame(() =>
            setTimeout(doConnect, 50)
        );
    } else {
        doConnect();
    }
}

function isIPad() {
    return (
        navigator.platform === "MacIntel" &&
        navigator.maxTouchPoints > 1
    );
}

function cleanup(reason = "") {
//    log("🧹 Cleanup " + reason)
    fetchajax=true
    stopMonitor()
    if (socket) {
        try {
            socket.removeAllListeners()
            socket.end(true)
        } catch {}
        socket = null
    }
    isConnecting = false
}

function hardReconnect(reason = "") {
//    log("💀 " + reason)
    cleanup(reason)
    connect()
}

function getClientId() {
    let id = localStorage.getItem("mqttClientId")
    if (!id) {
        id = "web_" + Math.random().toString(16).slice(2, 10)
        localStorage.setItem("mqttClientId", id)
    }
    return id
}

function onConnect() {

    isConnecting = false
    initialConnectDone = true  // ← MARKEER als gedaan
    lastMessageReceived = Date.now()

    socket.subscribe("d/#", { qos: 0 }, err => {
        if (err) {
            log("✗ Subscribe fout")
            hardReconnect("subscribe")
            return
        }
        log("✅ Verbonden")
        startMonitor()
    })
}

function onMessage(topic, payload) {
    lastMessageReceived = Date.now()
    const device = topic.split("/").pop()
    if (payload && typeof payload === "object") {
        if (payload.type === "Buffer" && Array.isArray(payload.data)) {
            payload = String.fromCharCode(...payload.data)
        } else if (payload instanceof Uint8Array) {
            payload = new TextDecoder().decode(payload)
        }
    }
    if (typeof payload === "string" && (payload.startsWith("{") || payload.startsWith("["))) {
        try {
            payload = JSON.parse(payload)
        } catch {
            log(`⚠️ Invalid JSON: ${topic} ${payload}`)
            return
        }
    }
    if (typeof payload === "string") {
        const n = Number(payload)
        if (!Number.isNaN(n)) payload = n
    }
    handleResponse(device,payload)
}

function startMonitor() {
    stopMonitor()
    monitorTimer = setInterval(() => {
        if (document.hidden || !socket) return
        const silence = Date.now() - lastMessageReceived
        if (silence > DEAD_TIMEOUT) {
            log(`⚠️ Stilte ${Math.round(silence / 1000)}s`)
            hardReconnect("stale")
        }
    }, MONITOR_INTERVAL)
}

function stopMonitor() {
    if (monitorTimer) {
        clearInterval(monitorTimer)
        monitorTimer = null
    }
}

document.addEventListener("DOMContentLoaded", () => {
    lastMessageReceived = Date.now()
    connect()
})

window.addEventListener("pageshow", e => {
    // ← BLOKKEER tijdens initiële connect op iPad
    if (!initialConnectDone && isIPad()) {
//        log("📄 Pageshow genegeerd (initiële connect bezig)")
        hardReconnect("pageshow")
        return
    }

    if (e.persisted) {
//        log("📄 bfcache restore")
        hardReconnect("bfcache")
    } else {
        const ua = navigator.userAgent || navigator.vendor || window.opera
        const isiOS = /iPad|iPhone|iPod/.test(ua) && !window.MSStream
        if (isiOS) {
//            log("📄 Pageshow iOS")
            hardReconnect("pageshow")
        }
    }
})

window.addEventListener("offline", () => {
    log("🌐 Offline")
    cleanup("offline")
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


function ajax(){
	setTime()
	if(!fetchajax) return
   	fetchajax=false
	log("🅰️ Ajax")
    ajaxJSON('d.php').then(e=>{
        if (!e || e.aborted){
	        setTimeout(()=>ajax(),1000)
	        return
	    }
		handleAjaxResponse(e);
    })
    .catch(err=>{
        if (err.name==='AbortError'){
	        setTimeout(()=>ajax(),1000);
            return;
        }
        console.warn('ajax error',err);
        setTimeout(()=>ajax(),1000);
    });
}
function handleAjaxResponse(response){
	newTime = response.t ?? newTime;
	Object.entries(response).forEach(([device,v])=>{
		if(device=="heating"){
			html='<img src="/images/arrowdown.png" class="i60" alt="Open">'
			if(v.s==0)html+=''
			else if(v.s==-2)html+='<img src="/images/Cooling.png" class="i40" alt="Cooling">'
			else if(v.s==-1)html+='<img src="/images/Cooling_grey.png" class="i40" alt="Cooling">'
			else if(v.s==1)html+='<img src="/images/Cooling_red.png" class="i40" alt="Elec">'
			else if(v.s==2)html+='<img src="/images/AircoGas.png" class="i40" alt="AircoGas">'
			else if(v.s==3)html+='<img src="/images/GasAirco.png" class="i40" alt="GasAirco">'
			else if(v.s==4)html+='<img src="/images/Gas.png" class="i40" alt="Gas">'
			setHTML('heatingbutton',html)
			if(v.s==0)html='<img src="images/close.png" height="40" width="40px" onclick="heating();"></td><td align="left" height="40" width="40px" style="line-height:18px" onclick="heating()">Neutral</td>'
			else if(v.s==-2)html='<img src="images/Cooling.png" onclick="heating();"></td><td align="left" height="60" width="80px" style="line-height:18px" onclick="heating()">Airco<br>cooling</td>'
			else if(v.s==-1)html='<img src="images/Cooling_grey.png" onclick="heating();"></td><td align="left" height="60" width="80px" style="line-height:18px" onclick="heating()">Passive<br>cooling</td>'
			else if(v.s==1)html='<img src="images/Cooling_red.png" onclick="heating();"></td><td align="left" height="60" width="80px" style="line-height:18px" onclick="heating()">Airco<br>heating</td>'
			else if(v.s==2)html='<img src="images/GasAirco.png" onclick="heating();"></td><td align="left" height="60" width="80px" style="line-height:18px" onclick="heating()">Gas-Airco<br>heating</td>'
			else if(v.s==3)html='<img src="images/Gas.png" onclick="heating();"></td><td align="left" height="60" width="80px" style="line-height:18px" onclick="heating()">Gas heating</td>'
			setHTML('trheating',html)
		}else if(device=="sirene"){
			if(v.s!="Off")html='<img src="images/alarm_On.png" width="500px" height="auto" alt="Sirene" onclick="ajaxcontrol(\'sirene\',\'sw\',\'Off\')"><br>'+device
			else html=""
			setHTML('sirene',html)
		}else if(device=="brander"){
			const heatingmode=d.heating?.s ?? 0;
			if(v.s=="Off")html='<img src="images/fire_Off.png" onclick="ajaxcontrol(\'brander\',\'sw\',\'On\')">'
			else html='<img src="images/fire_On.png" onclick="ajaxcontrol(\'brander\',\'sw\',\'Off\')">'
			setHTML('brander',html)

			html='<img src="/images/arrowdown.png" class="i60" alt="Open">'
			if(heatingmode==0)html+=''
			else if(heatingmode==-2)html+='<img src="/images/Cooling.png" class="i40" alt="Cooling">'
			else if(heatingmode==-1)html+='<img src="/images/Cooling_grey.png" class="i40" alt="Cooling">'
			else if(heatingmode==1)html+='<img src="/images/Cooling_red.png" class="i40" alt="Elec">'
			else if(heatingmode==4){
				if(v.s=='On')html+='<img src="/images/fire_On.png" class="i40" id="branderfloorplan" alt="Gas">'
				else html+='<img src="/images/fire_Off.png" class="i40" alt="Gas">'
			}
			setHTML('heating',html)
			if(heatingmode>=1){
				if(v.s=="Off") setAttr('branderfloorplan','src',"/images/fire_Off.png")
				else setAttr('branderfloorplan','src',"/images/fire_On.png")
			} else if(heatingmode>0){
				if(v.s=="Off")setAttr('branderfloorplan','src',"/images/gaselec_Off.png")
				else setAttr('branderfloorplan','src',"/images/gaselec_Off.png")
			} else setAttr('branderfloorplan','src',"")
		} else if (["s","sc"].includes(v?.d)){
			if (lastState[view+device] === v) return
			lastState[view+device] = v
			let html='';
			const isOn=(v.s == "On");
			const iconName=v.i || 'l';
			const specialDevices=["water","regenpomp","steenterras","tuintafel","terras","tuin","auto","media","nas","zetel","grohered","kookplaat","boseliving","bosekeuken","ipaddock","mac"];
			const confirmDevices=["ipaddcok","mac","daikin","grohered","kookplaat","media","boseliving","bosekeuken"];
			const directControlDevices=["regenpomp","nas"];
			let onclickHandler='';
			if (confirmDevices.includes(device)){
				onclickHandler=`confirmSwitch('${device}')`;
			} else if (directControlDevices.includes(device)){
				const newState=isOn ? 'Off' : 'On';
				onclickHandler=`ajaxcontrol('${device}','sw','${newState}');setView('floorplan');`;
			} else {
				const newState=isOn ? 'Off' : 'On';
				onclickHandler=`ajaxcontrol('${device}','sw','${newState}')`;
			}
			if (isOn){
				if (iconName == 'l'){
					html='<img src="/images/l_On.png" id="' + device + '" class="img100" />';
					html += '<div class="dimmercircle" onclick="' + onclickHandler + '">';
					html += '<svg viewBox="0 0 36 36">';
					html += '<path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />';
					html += '<path class="circle" stroke-dasharray="100,100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />';
					html += '</svg>';
					html += '</div>';
				} else {
					html='<img src="/images/' + iconName + '_On.png" id="' + device + '" onclick="' + onclickHandler + '" />';
				}
			} else {
				html='<img src="/images/' + iconName + '_Off.png" id="' + device + '" onclick="' + onclickHandler + '" />';
			}
			setHTML(device,html);
		} else if (["d","hd"].includes(v?.d)){
			let html='';
			const level=parseInt(v.s) || 0;
			if (level == 0){
				html='<img src="/images/l_Off.png" class="img100">';
			} else {
				html='<img src="/images/l_On.png" class="img100">';
				html += '<div class="dimmercircle">';
				html += '<svg viewBox="0 0 36 36">';
				html += '<path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />';
				html += '<path class="circle" stroke-dasharray="' + level + ',100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />';
				html += '</svg>';
				html += '<div class="dimmer-percentage">' + level + '%</div>';
				html += '</div>';
			}
			if (device == "terras"){
				html += 'terras';
			}
			setHTML(device,html);
		} else if (v.d==["b"]){
			if(device=="bose101"||device=="bose106"){
				if(v.m==0)html=''
				else{
					if(v.s=="On")html="<a href='javascript:navigator_Go(\"floorplan.bose.php?ip="+device+"\");'><img src=\"images/ST30_On.png\" id=\""+device+"\" alt=\"bose\"></a>"
					else html="<a href='javascript:navigator_Go(\"floorplan.bose.php?ip="+device+"\");'><img src=\"images/ST30_Off.png\" id=\""+device+"\" alt=\"bose\"></a>"
				}
			}else{
				if(v.m==0)html=''
				else{
					if(v.s=="On")html="<a href='javascript:navigator_Go(\"floorplan.bose.php?ip="+device+"\");'><img src=\"images/ST10_On.png\" id=\""+device+"\" alt=\"bose\"></a>"
					else html="<a href='javascript:navigator_Go(\"floorplan.bose.php?ip="+device+"\");'><img src=\"images/ST10_Off.png\" id=\""+device+"\" alt=\"bose\"></a>"
				}
			}
			setHTML(device,html)
		} else if (v.d==["r"]){
			const opts=v.i.split(",");
			const status=100 - v.s;
			let perc=(status < 100) ? (status / 100) * 0.7 : 1;
			let rollerTop=parseInt(opts[0]);
			let indicatorSize=0;
			if (status == 0){
				indicatorSize=0;
			} else if (status > 0){
				indicatorSize=(parseFloat(opts[2]) * perc) + 8;
				if (indicatorSize > parseFloat(opts[2])){
					indicatorSize=parseFloat(opts[2]);
				}
				rollerTop=parseInt(opts[0]) + parseInt(opts[2]) - indicatorSize;
				addClass(device,'yellow')
			} else {
				indicatorSize=parseFloat(opts[2]);
			}
			if (opts[3] == "P"){
				setStyle(device,'top',rollerTop + 'px')
				setStyle(device,'left',opts[1] + 'px')
				setStyle(device,'width','8px')
				setStyle(device,'height',indicatorSize + 'px')
			} else if (opts[3] == "L"){
				setStyle(device,'top',opts[0] + 'px')
				setStyle(device,'left',opts[1] + 'px')
				setStyle(device,'width',indicatorSize + 'px')
				setStyle(device,'height','9px')
			}
			let html='';
			if (v.s == 100){
				html='<img src="/images/arrowgreendown.png" class="i48">';
			} else if (v.s == 0){
				html='<img src="/images/arrowgreenup.png" class="i48">';
			} else {
				html='<img src="/images/circlegrey.png" class="i48">';
				html += '<div class="fix center dimmerlevel" style="position:absolute;top:19px;left:1px;width:46px;letter-spacing:6px;">';
				html += '<font size="5" color="#CCC">' + v.s + '</font>';
				html += '</div>';
			}
			html += '</div>';
			if (v.t > (newTime - 82800)){
				const date=new Date(v.t * 1000);
				const hours=date.getHours();
				const minutes=("0" + date.getMinutes()).substr(-2);
				html += '<br><div id="t' + device + '">' + hours + ':' + minutes + '</div>';
			} else {
				html += '<br><div id="t' + device + '">' + formatDate(v.t) + '</div>';
			}
			setHTML('R'+device,html)
		} else if (v.d==["p"]){
			temp=device.toString().replace("pir","")
			if(temp=="hall"||temp=="living"){
				if(v.s=="On"){
					addClass('z'+temp+'a','motion')
					addClass('z'+temp+'b','motion')
				}else{
					removeClass('z'+temp+'a','motion')
					removeClass('z'+temp+'b','motion')
				}
			}else{
				if(v.s=="On") addClass('z'+temp,'motion')
				else removeClass('z'+temp,'motion')
			}
			updateDeviceTime(device)
		} else if (v.d=='c'&&device!='raamhall'){
			if (lastState[view+device+'C'] === v) return
			lastState[view+device+'C'] = v
			if(v.s=="Open") addClass(device,'red')
			else removeClass(device,'red')
			updateDeviceTime(device)
		} else if (v.d=="t"){
			renderThermometer(device,v);
		}
		d[device]=v
	})
}
