function navigator_Go(url){window.location.assign(url);}
$LastUpdateTime=parseInt(0);
function ajax(Update=$LastUpdateTime){
    if(Update==0)$LastUpdateTime=0;
    $.ajax({
        url: '/ajax.php?t='+$LastUpdateTime,
        dataType : 'json',
        async: true,
        defer: true,
        success: function(d){
        	$currentTime=parseInt(Math.round(new Date().getTime()/1000));
        	if(d=='NOTAUTHENTICATED'){
        		document.getElementById('placeholder').insertAdjacentHTML('beforeend', 'NOT AUTHENTICATED');
        		navigator_Go('index.php');
        	}
            for (device in d){
                if(d.hasOwnProperty(device)){
                     if(device=="t"){
                        if($LastUpdateTime>100){
							if($LastUpdateTime<=$currentTime-10){
								console.log("Last more than 10 seconds ago, fetching everything.");
								ajax(0);
								log=false;
							}else{
                        		$LastUpdateTime=parseInt(d['t']);
                        		log=true;
							}
                        }else{
                        	console.log("LastUpdateTime = " + $LastUpdateTime);
                        	$LastUpdateTime=parseInt(d['t']);
                        	log=false;
                        }
                    }else{
                    	$value=d[device]['s'];
						$mode=d[device]['m'];
						type=d[device]['dt'];
						$icon=d[device]['ic'];
						time=d[device]['t'];
						date=new Date($currentTime*1000);
						hours=date.getHours();
						minutes="0"+date.getMinutes();
						seconds="0"+date.getSeconds();
						$time=hours+':'+minutes.substr(-2)+':'+seconds.substr(-2);
						if(log==true&&device!='el'&&device!='zon')console.log($LastUpdateTime+' '+$time+' '+device+' = '+ $value + ' | icon = ' + $icon);
						if(device=="Weg"){
							try{
								html='<div class="fix z" onclick="Weg();">';
								if($value==0)html+='<img src="https://home.egregius.be/images/Thuis.png" id="Weg">';
								else if($value==1)html+='<img src="https://home.egregius.be/images/Slapen.png" id="Weg">';
								else if($value==2)html+='<img src="https://home.egregius.be/images/Weg.png" id="Weg">';
								else if($value==3)html+='<img src="https://home.egregius.be/images/Vacation.png" id="Weg">';
								html+='</div>';
								document.getElementById('Weg').innerHTML=html;
							}catch{}
								if($value==0){
									try{
										document.getElementById("zliving").classList.remove("secured");
									}catch{}
									try{
										document.getElementById("zkeuken").classList.remove("secured");
									}catch{}
									try{
										document.getElementById("zgarage").classList.remove("secured");
									}catch{}
									try{
										document.getElementById("zinkom").classList.remove("secured");
									}catch{}
									try{
										document.getElementById("zhalla").classList.remove("secured");
									}catch{}
									try{
										document.getElementById("zhallb").classList.remove("secured");
									}catch{}
								}else if($value==1){
									try{
										document.getElementById("zliving").classList.add("secured");
									}catch{}
									try{
										document.getElementById("zkeuken").classList.add("secured");
									}catch{}
									try{
										document.getElementById("zgarage").classList.add("secured");
									}catch{}
									try{
										document.getElementById("zinkom").classList.add("secured");
									}catch{}
									try{
										document.getElementById("zhalla").classList.remove("secured");
									}catch{}
									try{
										document.getElementById("zhallb").classList.remove("secured");
									}catch{}
								}else if($value>=2){
									try{
										document.getElementById("zliving").classList.add("secured");
									}catch{}
									try{
										document.getElementById("zkeuken").classList.add("secured");
									}catch{}
									try{
										document.getElementById("zgarage").classList.add("secured");
									}catch{}
									try{
										document.getElementById("zinkom").classList.add("secured");
									}catch{}
									try{
										document.getElementById("zhalla").classList.add("secured");
									}catch{}
									try{
										document.getElementById("zhallb").classList.add("secured");
									}catch{}
								}
						}else if(device=="minmaxtemp"){
							try{
								elem=document.getElementById("mintemp");
								elem.innerHTML="<small>&#x21e9;</small>"+$value.toString().replace(/[.]/, ",")+" &#8451;";
								if($value>30)elem.style.color="#F66";
								else if($value>28)elem.style.color="#F77";
								else if($value>26)elem.style.color="#F88";
								else if($value>24)elem.style.color="#F99";
								else if($value>22)elem.style.color="#FAA";
								else if($value>20)elem.style.color="#FBB";
								else if($value>18)elem.style.color="#FCC";
								else if($value<10)elem.style.color="#CCF";
								else if($value<8)elem.style.color="#BBF";
								else if($value<6)elem.style.color="#AAF";
								else if($value<4)elem.style.color="#99F";
								else if($value<2)elem.style.color="#88F";
								else if($value<0)elem.style.color="#77F";
								else elem.style.color=null
								elem=document.getElementById("maxtemp");
								elem.innerHTML="<small>&#x21e7;</small>"+$mode.toString().replace(/[.]/, ",")+" &#8451;";
								if($mode>30)elem.style.color="#F66";
								else if($mode>28)elem.style.color="#F77";
								else if($mode>26)elem.style.color="#F88";
								else if($mode>24)elem.style.color="#F99";
								else if($mode>22)elem.style.color="#FAA";
								else if($mode>20)elem.style.color="#FBB";
								else if($mode>18)elem.style.color="#FCC";
								else if($mode<10)elem.style.color="#CCF";
								else if($mode<8)elem.style.color="#BBF";
								else if($mode<6)elem.style.color="#AAF";
								else if($mode<4)elem.style.color="#99F";
								else if($mode<2)elem.style.color="#88F";
								else if($mode<0)elem.style.color="#77F";
								else elem.style.color=null
							}catch{}
						}else if(device=="civil_twilight"){
							try {
								date=new Date($value*1000);
								hours=date.getHours();
								minutes="0"+date.getMinutes();
								document.getElementById("zonop").innerHTML=' '+hours+':'+minutes.substr(-2);
								date=new Date($mode*1000);
								hours=date.getHours();
								minutes="0"+date.getMinutes();
								document.getElementById("zononder").innerHTML=' '+hours+':'+minutes.substr(-2);
							}catch{}
						}else if(device=="wind"){
							try{
								elem=document.getElementById("wind");
								elem.innerHTML=$value.toString().replace(/[.]/, ",")+"km/u";
								if($value>40)elem.style.color="#F66";
								else if($value>30)elem.style.color="#F77";
								else if($value>25)elem.style.color="#F88";
								else if($value>20)elem.style.color="#F99";
								else if($value>16)elem.style.color="#FAA";
								else if($value>12)elem.style.color="#FBB";
								else if($value>8)elem.style.color="#FCC";
								else elem.style.color=null;
							}catch{}
						}else if(device=="icon"){
							try{
								$('#icon').attr("src", "/images/"+$value+".png");
							}catch{}
						}else if(device=="uv"){
							try{
							   if($value<2)html='<font color="#99EE00">UV: '+$value.toString().replace(/[.]/, ",")+'</font>';
							   else if($value<4)html='<font color="#99CC00">UV: '+$value.toString().replace(/[.]/, ",")+'</font>';
							   else if($value<6)html='<font color="#FFCC00">UV: '+$value.toString().replace(/[.]/, ",")+'</font>';
							   else if($value<8)html='<font color="#FF6600">UV: '+$value.toString().replace(/[.]/, ",")+'</font>';
							   else html='<font color="#FF2200">UV: '+$value.toString().replace(/[.]/, ",")+'</font>';
							   if($mode<2)html+='<br><font color="#99EE00">max: '+$mode.toString().replace(/[.]/, ",")+'</font>';
							   else if($mode<4)html+='<br><font color="#99CC00">max: '+$mode.toString().replace(/[.]/, ",")+'</font>';
							   else if($mode<6)html+='<br><font color="#FFCC00">max: '+$mode.toString().replace(/[.]/, ",")+'</font>';
							   else if($mode<8)html+='<br><font color="#FF6600">max: '+$mode.toString().replace(/[.]/, ",")+'</font>';
							   else html+='<br><font color="#FF2200">max: '+$mode.toString().replace(/[.]/, ",")+'</font>';
							   $("#uv").html(html);
							}catch{}
						}else if(device=="el"){
							try{
								$("#trelec").html("<td>Elec:</td><td id='elec'>"+$value+" W</td><td id='elecvandaag'>"+$mode.toString().replace(/[.]/, ",")+" kWh</td>");
								if($value>6000)document.getElementById("elec").style.color="#FF0000";
								else if($value>5000)document.getElementById("elec").style.color="#FF4400";
								else if($value>4000)document.getElementById("elec").style.color="#FF8800";
								else if($value>3000)document.getElementById("elec").style.color="#FFAA00";
								else if($value>2000)document.getElementById("elec").style.color="#FFCC00";
								else if($value>1000)document.getElementById("elec").style.color="#FFFF00";
								else document.getElementById("elec").style.color=null;
								if($mode>20)document.getElementById("elecvandaag").style.color="#FF0000";
								else if($mode>18)document.getElementById("elecvandaag").style.color="#FF4400";
								else if($mode>16)document.getElementById("elecvandaag").style.color="#FF8800";
								else if($mode>14)document.getElementById("elecvandaag").style.color="#FFAA00";
								else if($mode>12)document.getElementById("elecvandaag").style.color="#FFCC00";
								else if($mode>10)document.getElementById("elecvandaag").style.color="#FFFF00";
								else document.getElementById("elecvandaag").style.color=null;
							}catch{}
						}else if(device=="zon"){
							try{
								$("#zon").html($value+" W");
								if(d['zon']['s']>3500)document.getElementById("zon").style.color="#00FF00";
								else if(d['zon']['s']>3000)document.getElementById("zon").style.color="#33FF00";
								else if(d['zon']['s']>2700)document.getElementById("zon").style.color="#66FF00";
								else if(d['zon']['s']>2400)document.getElementById("zon").style.color="#99FF00";
								else if(d['zon']['s']>2100)document.getElementById("zon").style.color="#CCFF00";
								else if(d['zon']['s']>1800)document.getElementById("zon").style.color="#EEFF00";
								else if(d['zon']['s']>1500)document.getElementById("zon").style.color="#FFFF33";
								else if(d['zon']['s']>1200)document.getElementById("zon").style.color="#FFFF66";
								else if(d['zon']['s']>900)document.getElementById("zon").style.color="#FFFF99";
								else if(d['zon']['s']>600)document.getElementById("zon").style.color="#FFFFCC";
								else if(d['zon']['s']>300)document.getElementById("zon").style.color="#EEEECC";
								else document.getElementById("zon").style.color=null;
							}catch{}
						}else if(device=="zonvandaag"){
							try{
								zonvandaag=parseFloat(Math.round($value*10)/10).toFixed(1);
								$("#zonvandaag").html(zonvandaag.toString().replace(/[.]/, ",")+" kWh");
								if(d['zonvandaag']['m']>120)document.getElementById("zonvandaag").style.color="#00FF00";
								else if(d['zonvandaag']['m']>110)document.getElementById("zonvandaag").style.color="#33FF00";
								else if(d['zonvandaag']['m']>100)document.getElementById("zonvandaag").style.color="#66FF00";
								else if(d['zonvandaag']['m']>90)document.getElementById("zonvandaag").style.color="#99FF00";
								else if(d['zonvandaag']['m']>80)document.getElementById("zonvandaag").style.color="#CCFF00";
								else if(d['zonvandaag']['m']>70)document.getElementById("zonvandaag").style.color="#EEFF00";
								else if(d['zonvandaag']['m']>60)document.getElementById("zonvandaag").style.color="#FFFF33";
								else if(d['zonvandaag']['m']>50)document.getElementById("zonvandaag").style.color="#FFFF66";
								else if(d['zonvandaag']['m']>40)document.getElementById("zonvandaag").style.color="#FFFF99";
								else if(d['zonvandaag']['m']>30)document.getElementById("zonvandaag").style.color="#FFFFCC";
								else if(d['zonvandaag']['m']>20)document.getElementById("zonvandaag").style.color="#EEEECC";
								else document.getElementById("zonvandaag").style.color=null;
							}catch{}
						}else if(device=="gasvandaag"){
							try{
								if($value>0){
									item=parseFloat(Math.round(($value/100)*100)/100).toFixed(3);
									$("#trgas").html('<td id="tdgas">Gas:</td><td colspan="2" id="tdgasvandaag">'+item.toString().replace(/[.]/, ",")+' m<sup>3</sup>');
									if($value>700)document.getElementById("tdgasvandaag").style.color="#FF0000";
									else if($value>600)document.getElementById("tdgasvandaag").style.color="#FF4400";
									else if($value>500)document.getElementById("tdgasvandaag").style.color="#FF8800";
									else if($value>400)document.getElementById("tdgasvandaag").style.color="#FFAA00";
									else if($value>300)document.getElementById("tdgasvandaag").style.color="#FFCC00";
									else if($value>200)document.getElementById("tdgasvandaag").style.color="#FFFF00";
									else document.getElementById("tdgasvandaag").style.color=null;
								}else $("#trgas").html("");
							}catch{}
							localStorage.setItem("tijd_gas", time);
						}else if(device=="watervandaag"){
							try{
								if($value>0){
									item=$value / 1000;
									$("#trwater").html('<td id="tdwater">Water:</td><td colspan="2" id="watervandaag">'+item.toString().replace(/[.]/, ",")+' m<sup>3</sup>');
									if($value>1000)document.getElementById("watervandaag").style.color="#FF0000";
									else if($value>750)document.getElementById("watervandaag").style.color="#FF4400";
									else if($value>500)document.getElementById("watervandaag").style.color="#FF8800";
									else if($value>400)document.getElementById("watervandaag").style.color="#FFAA00";
									else if($value>300)document.getElementById("watervandaag").style.color="#FFCC00";
									else if($value>200)document.getElementById("watervandaag").style.color="#FFFF00";
									else document.getElementById("watervandaag").style.color=null;
								}else $("#trwater").html("");
							}catch{}
							localStorage.setItem("tijd_water", time);
						}else if(device=="watertuin"){
							localStorage.setItem("watertuin", $mode);
							try{
								$("#watertuin").html($mode+' L');
							}catch{}
						}else if(device=="douche"){
							try{
								douchegas=$value * 10;
								douchewater=$mode;
								douchegaseuro=parseFloat(douchegas * 0.004).toFixed(2);
								douchewatereuro=parseFloat(douchewater * 0.005).toFixed(2);
								if($value>0){
									html="<td>D-gas:</td><td>"+douchegas+" L</td><td>"+douchegaseuro.toString().replace(/[.]/, ",")+" &#8364;</td>";
									document.getElementById("trdgas").innerHTML=html;
								}else{
									document.getElementById("trdgas").innerHTML="";
								}
								if($mode>0){
									html="<td>D-water:</td><td>"+douchewater+" L</td><td>"+douchewatereuro.toString().replace(/[.]/, ",")+" &#8364;</td>";
									document.getElementById("trdwater").innerHTML=html;
								}else{
									document.getElementById("trdwater").innerHTML="";
								}
							}catch{}
						}else if(device=="Xlight"){
							heatingset=localStorage.getItem('heating');
							try{
								if($mode>0) {
									width = ($mode * 3.16);
									if (width<48) width=48;
									if ($mode>80) power=$mode*20;
									else if ($mode>60) power=$mode*19;
									else if ($mode>40) power=$mode*18;
									else if ($mode>28) power=$mode*17;
									else if ($mode>16) power=$mode*16;
									else power=$mode*15;
									$('#daikincmpfreq').html(' cmp:'+$mode+'='+power+'W');
									$('#daikincmpfreq').css("background-color","red");
									$('#daikincmpfreq').css("width", width + "px");
									$('#daikincmpfreq').css("display", "inherit");
								}else if($mode<0) {
									width = ($mode * -3.16);
									if (width<48) width=48;
									if ($mode>80) power=$mode*-20;
									else if ($mode>60) power=$mode*-19;
									else if ($mode>40) power=$mode*-18;
									else if ($mode>28) power=$mode*-17;
									else if ($mode>16) power=$mode*-16;
									else power=$mode*-15;
									$('#daikincmpfreq').html(' cmp:'+-$mode+'='+power+'W');
									if(heatingset==-2)$('#daikincmpfreq').css("background-color","blue");
									else $('#daikincmpfreq').css("background-color","black");
									$('#daikincmpfreq').css("width", width + "px");
									$('#daikincmpfreq').css("display", "inherit");
								}else{
									$('#daikincmpfreq').html('');
									$('#daikincmpfreq').css("display", "none");
								}
							}catch{}
						}else if(device=="heating"){
							try{
							   html='<img src="https://home.egregius.be/images/arrowdown.png" class="i60" alt="Open">';
								if($value==0)html+='';
								else if($value==-2)html+='<img src="https://home.egregius.be/images/Cooling.png" class="i40" alt="Cooling">';
								else if($value==-1)html+='<img src="https://home.egregius.be/images/Cooling_grey.png" class="i40" alt="Cooling">';
								else if($value==1)html+='<img src="https://home.egregius.be/images/Cooling_red.png" class="i40" alt="Elec">';
								else if($value==2){
									if(d['brander']['s']=='On')html+='<img src="https://home.egregius.be/images/fire_On.png" class="i40" id="branderfloorplan" alt="Gas">';
									else html+='<img src="https://home.egregius.be/images/fire_Off.png" class="i40" alt="Gas">';
								}
								document.getElementById("heating").innerHTML=html;
							}catch{}
							localStorage.setItem(device, $value);
							localStorage.setItem('tijd_'+device, time);
							try{
								html='<td></td><td width="65px">';
								if($value==0)html+='<img src="images/close.png" height="40" width="40px" onclick="heating();"></td><td align="left" height="40" width="40px" style="line-height:18px" onclick="heating()">Neutral</td>';
								else if($value==-2)html+='<img src="images/Cooling.png" onclick="heating();"></td><td align="left" height="60" width="80px" style="line-height:18px" onclick="heating()">Airco cooling</td>';
								else if($value==-1)html+='<img src="images/Cooling_grey.png" onclick="heating();"></td><td align="left" height="60" width="80px" style="line-height:18px" onclick="heating()">Passive cooling</td>';
								else if($value==1)html+='<img src="images/Cooling_red.png" onclick="heating();"></td><td align="left" height="60" width="80px" style="line-height:18px" onclick="heating()">Airco heating</td>';
								else if($value==2)html+='<img src="images/gaselec_On.png" onclick="heating();"></td><td align="left" height="60" width="80px" style="line-height:18px" onclick="heating()">Gas heating</td>';
								document.getElementById("trheating").innerHTML=html;
							}catch{}
						}else if(device=="$ belknop"){
							localStorage.setItem("tijd_belknop", time);
							try{
								if(time>($currentTime-82800)){
									date=new Date(time*1000);
									hours=date.getHours();
									minutes="0"+date.getMinutes();
									document.getElementById("t"+device).innerHTML=hours+':'+minutes.substr(-2);
								}else{
									document.getElementById("t"+device).innerHTML=formatDate(tijd);
								}
							}catch{}
						}else if(device=="zoldervuur"){
							try{
								if($value=="On")html='<img src="images/fire_On.png" width="28px" height="auto" alt="">';
								else html='';
								document.getElementById("zoldervuur2").innerHTML=html;
							}catch{}
						}else if(device=="Usage_grohered"){
							try{
								if($value==0)html="";
								else if($value>0&&$value<11)html='<img src="images/plug_On.png" width="28px" height="auto" alt="">';
								else html='<img src="images/plug_Red.png" width="28px" height="auto" alt="">';
								document.getElementById("Usage_grohered").innerHTML=html;
							}catch{}
						}else if(device=="kWh_bureeltobi"){
							try{
								elem=$value.split(";");
								item=parseInt(Math.round(elem[0]));
								if(item>0)html=item+" W"
								else html="";
								elem=document.getElementById("bureeltobikwh");
								elem.innerHTML=html;
								if(item>600)elem.style.color="#FF0000";
								else if(item>500)elem.style.color="#FF4400";
								else if(item>400)elem.style.color="#FF8800";
								else if(item>300)elem.style.color="#FFAA00";
								else if(item>200)elem.style.color="#FFCC00";
								else if(item>100)elem.style.color="#FFFF00";
								else elem.style.color=null;
							}catch{}
						}else if(device=="powermeter"){
							try{
								console.log(device+' '+$value);
								elem=$value.split(";");
								html=Math.round(elem[0]*100)/100+" W";
								if(elem[1]>0)html+="<br>"+elem[1]/1000 +" kWh";
								elem=document.getElementById("powermeter");
								elem.innerHTML=html;
								if(item>600)elem.style.color="#FF0000";
								else if(item>500)elem.style.color="#FF4400";
								else if(item>400)elem.style.color="#FF8800";
								else if(item>300)elem.style.color="#FFAA00";
								else if(item>200)elem.style.color="#FFCC00";
								else if(item>100)elem.style.color="#FFFF00";
								else elem.style.color=null;
							}catch{}
						}else if(device=="sirene"){
							try{
								if($value!="Off")html='<img src="images/alarm_On.png" width="500px" height="auto" alt="Sirene" onclick="ajaxcontrol(\'sirene\',\'sw\',\'Off\')"><br>'+device;
								else html="";
								document.getElementById("sirene").innerHTML=html;
							}catch{}
						}else if(type=="smoke detector"){
							try{
								if($value!="Off")html='<img src="images/smoke_On.png" width="500px" height="auto" alt="Sirene" onclick="ajaxcontrol(\'resetsecurity\',\'resetsecurity\',\'Off\')"><br>'+device;
								else html="";
								document.getElementById("sirene").innerHTML=html;
							}catch{}
						}else if(device=="brander"){
							localStorage.setItem(device, $value);
							localStorage.setItem('tijd_'+device, time);
							try{
								if($value=="Off")html='<img src="images/fire_Off.png" onclick="ajaxcontrol(\'brander\',\'sw\',\'On\')">';
								else html='<img src="images/fire_On.png" onclick="ajaxcontrol(\'brander\',\'sw\',\'Off\')">';
								document.getElementById("brander").innerHTML=html;
							}catch{}
							//try{
								//BRANDERFLOORPLAN
								heatingmode=localStorage.getItem('heating');
								if(heatingmode==4){
									if($value=="Off"){
										$('#branderfloorplan').attr("src", "/images/fire_Off.png");
									}else{
										$('#branderfloorplan').attr("src", "/images/fire_On.png");
									}
								} else if(heatingmode==3){
									if($value=="Off")$('#branderfloorplan').attr("src", "/images/gaselec_Off.png");
									else $('#branderfloorplan').attr("src", "/images/gaselec_On.png");
								} else {
									$('#branderfloorplan').attr("src", "");
								}
							//}catch{}
						}else if(device=="heatingauto"){
							localStorage.setItem("tijd_"+device, time);
							try{
								if($value=="Off")html='<td></td><td width="65px"><img src="images/smoke_Off.png" onclick="ajaxcontrol(\'heatingauto\',\'sw\',\'On\')"></td><td align="right" height="60" width="100px" style="line-height:18px">Manueel</td>';
								else html='<td></td><td width="65px"><img src="images/smoke_On.png" onclick="ajaxcontrol(\'heatingauto\',\'sw\',\'Off\')"></td><td align="right" height="60" width="100px" style="line-height:18px">Automatisch</td>';
								document.getElementById("heatingauto").innerHTML=html;
							}catch{}
						}else if(device=="luifel"){
							localStorage.setItem(device, $value);
							localStorage.setItem('tijd_'+device, time);
							localStorage.setItem(device+'mode', $mode);
							try{
								if($value==0)html='<img src="https://home.egregius.be/images/arrowgreenup.png" class="i60">';
								else if($value==100)html='<img src="https://home.egregius.be/images/arrowgreendown.png" class="i60">';
								else html='<img src="https://home.egregius.be/images/arrowdown.png" class="i60"><div class="fix center dimmerlevel" style="position:absolute;top:10px;left:-2px;width:70px;letter-spacing:4;"><font size="5" color="#CCC">'+$value+'</font> </div>';
								if($mode==1)html+='<div class="fix" style="top:2px;left:2px;z-index:-100;background:#fff7d8;width:56px;height:56px;border-radius:45px;"></div>';
								html+='<br><span id="tluifel"></span>';
								document.getElementById(device).innerHTML=html;
							}catch{}
						}else if(device=="raamhall"){
							localStorage.setItem(device, $value);
							localStorage.setItem("tijd_"+device, time);
							try{
								if($value=='Closed') {
									zoldertrap=localStorage.getItem('zoldertrap');
									if(zoldertrap=="Closed")html='<img src="https://home.egregius.be/images/arrowgreenup.png" class="i48" alt="Open" onclick="ajaxcontrol(\'zoldertrap\',\'sw\',\'Off\')"><br>';
									else html='<img src="https://home.egregius.be/images/arrowup.png" class="i48" alt="Open" onclick="ajaxcontrol(\'zoldertrap\',\'sw\',\'Off\')"><br>';
									if(zoldertrap=="Open")html+='<img src="https://home.egregius.be/images/arrowgreendown.png" class="i48" alt="Open" onclick="ajaxcontrol(\'zoldertrap\',\'sw\',\'On\')">';
									else html+='<img src="https://home.egregius.be/images/arrowdown.png" class="i48" alt="Open" onclick="ajaxcontrol(\'zoldertrap\',\'sw\',\'On\')">';
								} else html='';
								document.getElementById('zoldertrap').innerHTML=html;
							}catch{}
							try{
								element=document.getElementById(device);
								if($value=="Open"){
									element.classList.add("red");
								}else{
									element.classList.remove("red");
								}
								if(time>($currentTime-82800)){
									date=new Date(time*1000);
									hours=date.getHours();
									minutes="0"+date.getMinutes();
									document.getElementById("t"+device).innerHTML=hours+':'+minutes.substr(-2);
								}else{
									document.getElementById("t"+device).innerHTML="";
								}
							}catch{}
						}else if(device=="zoldertrap"){
							try{
								raamhall=localStorage.getItem('raamhall');
								if(raamhall=='Closed') {
									if($value=="Closed")html='<img src="https://home.egregius.be/images/arrowgreenup.png" class="i48" alt="Open" onclick="ajaxcontrol(\'zoldertrap\',\'sw\',\'Off\')"><br>';
									else html='<img src="https://home.egregius.be/images/arrowup.png" class="i48" alt="Open" onclick="ajaxcontrol(\'zoldertrap\',\'sw\',\'Off\')"><br>';
									if($value=="Open")html+='<img src="https://home.egregius.be/images/arrowgreendown.png" class="i48" alt="Open" onclick="ajaxcontrol(\'zoldertrap\',\'sw\',\'On\')">';
									else html+='<img src="https://home.egregius.be/images/arrowdown.png" class="i48" alt="Open" onclick="ajaxcontrol(\'zoldertrap\',\'sw\',\'On\')">';
								} else html='';
								document.getElementById(device).innerHTML=html;
							}catch{}
							localStorage.setItem(device, $value);
						}else if(device=="buien"){
							try{
								if(typeof $value !== 'undefined'){
									elem=document.getElementById('buien');
									elem.innerHTML="Buien: "+$value;
									if($value>70)elem.style.color="#39F";
									else if($value>60)elem.style.color="#69F";
									else if($value>50)elem.style.color="#79F";
									else if($value>40)elem.style.color="#89F";
									else if($value>30)elem.style.color="#99F";
									else if($value>20)elem.style.color="#AAF";
									else if($value>10)elem.style.color="#BBD";
									else if($value>0)elem.style.color="#CCF";
									else elem.style.color=null;
								}else{
									elem=document.getElementById('buien');
									elem.innerHTML="Buien: 0";
									elem.style.color="#888";
								}
							}catch{}
						} else if(device=="gcal"){
							localStorage.setItem(device, $value);
							try{
								if(typeof $mode !== 'undefined')document.getElementById("gcal").innerHTML=$mode;
								else document.getElementById("gcal").innerHTML='';
							}catch{}
						}else if(type=="switch"){
							try{
								if(device=="dampkap"||device=="water"||device=="regenpomp"||device=="zwembadfilter"||device=="zwembadwarmte"||device=="auto"||device=="bosesoundlink"||device=="denon"||device=="tv"||device=="lgtv"||device=="nas"||device=="nvidia"){
									if(device=="denon"||device=="tv"||device=="lgtv"||device=="nvidia") {
										if($value=="On")html='<img src="https://home.egregius.be/images/'+$icon+'_On.png" id="'+device+'" onclick="confirmSwitch(\''+device+'\')">';
										else if($value=="Off")html='<img src="https://home.egregius.be/images/'+$icon+'_Off.png" id="'+device+'" onclick="confirmSwitch(\''+device+'\')">';
									} else {
										if($value=="On")html='<img src="https://home.egregius.be/images/'+$icon+'_On.png" id="'+device+'" onclick="ajaxcontrol(\''+device+'\',\'sw\',\'Off\')"/>';
										else if($value=="Off")html='<img src="https://home.egregius.be/images/'+$icon+'_Off.png" id="'+device+'" onclick="ajaxcontrol(\''+device+'\',\'sw\',\'On\')""/>';
									}
									html+='<br>'+device;
									if(time>($currentTime-82800)){
										date=new Date(time*1000);
										hours=date.getHours();
										minutes="0"+date.getMinutes();
										html+='<br>'+hours+':'+minutes.substr(-2);
									} else {
										html+='<br>'+formatDate(time);
									}
									if(device=="water"){
										try{
											if($mode==300){
												document.getElementById("water300").classList.add("btna");
												document.getElementById("water1800").classList.remove("btna");
												document.getElementById("water7200").classList.remove("btna");
											}else if($mode==1800){
												document.getElementById("water300").classList.remove("btna");
												document.getElementById("water1800").classList.add("btna");
												document.getElementById("water7200").classList.remove("btna");
											}else if($mode==7200){
												document.getElementById("water300").classList.remove("btna");
												document.getElementById("water1800").classList.remove("btna");
												document.getElementById("water7200").classList.add("btna");
											}else{
												document.getElementById("water300").classList.remove("btna");
												document.getElementById("water1800").classList.remove("btna");
												document.getElementById("water7200").classList.remove("btna");
											}
										}catch{}
									}else if(localStorage.getItem('view')=='floorplan'&&device=="denon"){
										try{
											if($value=='On')$('#denonicon').attr("src", "/images/denon_On.png");
											else $('#denonicon').attr("src", "/images/denon_Off.png");
										}catch{}
									}else if(localStorage.getItem('view')=='floorplan'&&device=="lgtv"){
										try{
											if($value=='On')$('#lgtvicon').attr("src", "/images/lgtv_On.png");
											else $('#lgtvicon').attr("src", "/images/lgtv_Off.png");
										}catch{}
									}else if(localStorage.getItem('view')=='floorplan'&&device=="tv"){
										try{
											if($value=='On')$('#tvicon').attr("src", "/images/tv_On.png");
											else $('#tvicon').attr("src", "/images/tv_Off.png");
										}catch{}
									}
								}else if(device=="bureeltobi"){
									if($value=="On")html='<img src="https://home.egregius.be/images/'+$icon+'_On.png" id="bureeltobi" onclick="bureeltobi()">';
									else if($value=="Off")html='<img src="https://home.egregius.be/images/'+$icon+'_Off.png" id="bureeltobi" onclick="bureeltobi()">';
								}else{
									if($value=="On")html='<img src="https://home.egregius.be/images/'+$icon+'_On.png" id="'+device+'" onclick="ajaxcontrol(\''+device+'\',\'sw\',\'Off\')"/>';
									else if($value=="Off")html='<img src="https://home.egregius.be/images/'+$icon+'_Off.png" id="'+device+'" onclick="ajaxcontrol(\''+device+'\',\'sw\',\'On\')""/>';
								}
								try{
									$('#'+device).html(html);
								}catch{}
							}catch{}
							localStorage.setItem(device, $value);
							try{
								//localStorage.setItem(device+'mode', $mode);
							}catch{}
						}else if(type=="bose"){
							try{
								if(device=="bose105"){
									if($mode=="Online"){
										if($value=="On"){html="<a href='javascript:navigator_Go(\"floorplan.bose.php?ip="+device+"\");'><img src=\"images/bose_On.png\" id=\"bose105\" alt=\"bose\"></a>";}
										else{html="<a href='javascript:navigator_Go(\"floorplan.bose.php?ip="+device+"\");'><img src=\"images/bose_Off.png\" id=\"bose105\" alt=\"bose\"></a>";}
									}else if($mode=="Offline"){html="";}
								}else{
									if($value=="On"){html="<a href='javascript:navigator_Go(\"floorplan.bose.php?ip="+device+"\");'><img src=\"images/bose_On.png\" id=\""+device+"\" alt=\"bose\"></a>";}
									else{html="<a href='javascript:navigator_Go(\"floorplan.bose.php?ip="+device+"\");'><img src=\"images/bose_Off.png\" id=\""+device+"\" alt=\"bose\"></a>";}
								}
								$('#'+device).html(html);
							}catch{}
							try{
								if($value=="On"){$('#'+device).attr("src", "/images/bose_On.png");}
							    else if($value=="Off"){$('#'+device).attr("src", "/images/bose_Off.png");}
							}catch{}
						}else if(type=="dimmer"){
							localStorage.setItem(device, $value);
							localStorage.setItem(device+'mode', $mode);
							try{
								if($value==0||$value=="Off"){
									html='<img src="https://home.egregius.be/images/light_Off.png" class="'+$icon+'">';
								}else{
									html='<img src="https://home.egregius.be/images/light_On.png" class="'+$icon+'"><div class="fix center dimmerlevel '+$icon+'"><font color="#000">'+$value+'</font></div>';
								}
								if (device=="ledluifel") {
									luifel=localStorage.getItem("luifel");
									if (luifel=="0"&&$value==0)html='';
								}
								$('#'+device).html(html);
							}catch{}
						}else if(type=="rollers"){
							localStorage.setItem(device, $value);
							localStorage.setItem(device+'mode', $mode);
							try{
								opts=$icon.split(",");
								stat=100 - $value;
								if(stat<100)perc=(stat/100)*0.7;
								else perc=1;
								elem=document.getElementById(device);
								if(stat==0){
									nsize=0;
									try{
										elem.classList.remove("yellow");
									}catch{}
								}else if(stat>0){
									nsize=(opts[2]*perc)+8;
									if(nsize>opts[2])nsize=opts[2];
									top=+opts[0] + +opts[2]-nsize;
									try{
										elem.classList.add("yellow");
									}catch{}
								}else{nsize=opts[2];}
								if(opts[3]=="P"){
									elem.style.top=top+'px';
									elem.style.left=opts[1]+'px';
									elem.style.width='7px';
									elem.style.height=nsize+'px';
								}else if(opts[3]=="L"){
									elem.style.top=opts[0]+'px';
									elem.style.left=opts[1]+'px';
									elem.style.width=nsize+'px';
									elem.style.height='7px';
								}
							}catch{}
							try{
								if($value==100){
									html='<img src="https://home.egregius.be/images/arrowgreendown.png" class="i48">';
								}else if($value==0){
									html='<img src="https://home.egregius.be/images/arrowgreenup.png" class="i48">';
								}else{
									html='<img src="https://home.egregius.be/images/circlegrey.png" class="i48">';
									html+='<div class="fix center dimmerlevel" style="position:absolute;top:17px;left:-2px;width:70px;letter-spacing:4;">';
									html+='<font size="5" color="#CCC">';
									html+=$value+'</font></div>';
								}
								html+='</div>';
								if(time>($currentTime-82800)){
									date=new Date(time*1000);
									hours=date.getHours();
									minutes="0"+date.getMinutes();
									html+='<br><div id="t'+device+'">'+hours+':'+minutes.substr(-2)+'</div>';
								}else{
									html+='<br><div id="t'+device+'">'+formatDate(time)+'</div>';
								}
								
								$('#R'+device).html(html);
							}catch{}
							if(localStorage.getItem('view')=='floorplanheating'){
								try{
									if(time>($currentTime-82800)){
										date=new Date(time*1000);
										hours=date.getHours();
										minutes="0"+date.getMinutes();
										document.getElementById("t"+device).innerHTML=hours+':'+minutes.substr(-2);
									}else{
										document.getElementById("t"+device).innerHTML="";
										document.getElementById("t"+device).innerHTML=formatDate(time);
									}
								}catch{}
							}
						}else if(type=="pir"){
							localStorage.setItem(device, $value);
							localStorage.setItem("tijd_"+device, time);
							try{
								device=device.toString().replace("pir", "")
								element=document.getElementById("z"+device);
								if(device=="hall"){
									if($value=="On"){
										document.getElementById("z"+device+"a").classList.add("motion");
										document.getElementById("z"+device+"b").classList.add("motion");
									}else{
										document.getElementById("z"+device+"a").classList.remove("motion");
										document.getElementById("z"+device+"b").classList.remove("motion");
									}
								}else{
									if($value=="On"){
										element.classList.add("motion");
									}else{
										element.classList.remove("motion");
									}
								}
								if(time>($currentTime-82800)){
									date=new Date(time*1000);
									hours=date.getHours();
									minutes="0"+date.getMinutes();
									document.getElementById("tpir"+device).innerHTML=hours+':'+minutes.substr(-2);
								}else{
									document.getElementById("tpir"+device).innerHTML="";
								}
							}catch{}
						}else if(type=="contact"){
							localStorage.setItem(device, $value);
							localStorage.setItem("tijd_"+device, time);
							try{
								element=document.getElementById(device);
								if($value=="Open"){
									element.classList.add("red");
								}else{
									element.classList.remove("red");
								}
								if(time>($currentTime-82800)){
									date=new Date(time*1000);
									hours=date.getHours();
									minutes="0"+date.getMinutes();
									document.getElementById("t"+device).innerHTML=hours+':'+minutes.substr(-2);
								}else{
									document.getElementById("t"+device).innerHTML=formatdate(time);
								}
							}catch{}
						}else if(type=="thermometer"){
							localStorage.setItem(device, $value);
							try{
								 if(device=="diepvries_temp"){
									elem=document.getElementById(device);
									elem.innerHTML=$value.toString().replace(/[.]/, ",")+"&#8451;";
									if($value>-15)elem.style.color="#F00";
									else if($value>-16)elem.style.color="#F44";
									else if($value>-17)elem.style.color="#F88";
									else if($value<-19)elem.style.color="#CCF";
									else if($value<-20)elem.style.color="#88F";
									else elem.style.color=null;
								 }else{
									var hoogte=$value * 3;
									if(hoogte>88)hoogte=88;
									else if(hoogte<20)hoogte=20;
									var top=91 - hoogte;
									if($value >= 22){tcolor="F00";dcolor="55F";}
									else if($value >= 20){tcolor="D12";dcolor="44F";}
									else if($value >= 18){tcolor="B24";dcolor="33F";}
									else if($value >= 15){tcolor="93B";dcolor="22F";}
									else if($value >= 10){tcolor="64D";dcolor="11F";}
									else{tcolor="55F";dcolor="00F";}
									html='<div class="fix tmpbg" style="top:'+top+'px;left:8px;height:'+hoogte+'px;background:linear-gradient(to bottom, #'+tcolor+', #'+dcolor +');">';
									html+='</div>'
									html+='<img src="https://home.egregius.be/images/temp.png" height="100px" width="auto" alt="'+device+'">';
									html+='<div class="fix center" style="top:73px;left:5px;width:30px;" id="temp'+device+'">';
									html+=$value.toString().replace(/[.]/, ",");
									html+='</div>';
									document.getElementById(device).innerHTML=html;
									elem=document.getElementById('temp'+device);
									if($value>30)elem.style.color="#F00";
									else if($value>28)elem.style.color="#F22";
									else if($value>26)elem.style.color="#F44";
									else if($value>24)elem.style.color="#F66";
									else if($value>22)elem.style.color="#F88";
									else if($value>20)elem.style.color="#FAA";
									else elem.style.color=null;
									if($icon>=0.45)html='<div class="fix" style="top:10px;left:13px;"><img src="https://home.egregius.be/images/trendred.png" height="56px" width="15px"></div>';
									else if($icon>=0.35)html='<div class="fix" style="top:10px;left:13px;"><img src="https://home.egregius.be/images/trendred.png" height="42px" width="15px"></div>';
									else if($icon>=0.25)html='<div class="fix" style="top:10px;left:13px;"><img src="https://home.egregius.be/images/trendred.png" height="28px" width="15px"></div>';
									else if($icon>=0.15)html='<div class="fix" style="top:10px;left:13px;"><img src="https://home.egregius.be/images/trendred.png" height="14px" width="15px"></div>';
									else if($icon>=0.05)html='<div class="fix" style="top:10px;left:13px;"><img src="https://home.egregius.be/images/trendup.png" height="14px" width="15px"></div>';
									else if($icon<=-0.45)html='<div class="fix" style="top:10px;left:13px;"><img src="https://home.egregius.be/images/trendblue.png" height="56px" width="15px"></div>';
									else if($icon<=-0.35)html='<div class="fix" style="top:10px;left:13px;"><img src="https://home.egregius.be/images/trendblue.png" height="42px" width="15px"></div>';
									else if($icon<=-0.25)html='<div class="fix" style="top:10px;left:13px;"><img src="https://home.egregius.be/images/trendblue.png" height="28px" width="15px"></div>';
									else if($icon<=-0.15)html='<div class="fix" style="top:10px;left:13px;"><img src="https://home.egregius.be/images/trendblue.png" height="14px" width="15px"></div>';
									else if($icon<=-0.05)html='<div class="fix" style="top:10px;left:13px;"><img src="https://home.egregius.be/images/trenddown.png" height="14px" width="15px"></div>';
									else html="";
									document.getElementById(device).insertAdjacentHTML('beforeend', html);
								}
							}catch{}
						}else if(type=="daikin"){
							localStorage.setItem(device, $mode);
							localStorage.setItem(device+'_value', $value);
						}else if(type=="thermostaat"){
							heatingset=localStorage.getItem('heating');
							localStorage.setItem(device+'_mode', $mode);
							localStorage.setItem(device+'_icon', $icon);
							localStorage.setItem(device, $value);
							localStorage.setItem("tijd_"+device, time);
							
							try{
								temp=localStorage.getItem(device.toString().replace("_set", "_temp"));
								dif=temp-$value;
								if(heatingset>=1||device=='zolder_set'||device=='badkamer_set'){
									if(dif>0.2)circle="hot";
									else if(dif<0)circle="cold";
									else circle="grey";
									if($value>20.5)center="red";
									else if($value>19)center="orange";
									else if($value>14)center="grey";
									else center="blue";
								}else{
									if(device=='living_set')daikin=localStorage.getItem('daikinliving');
									else if(device=='kamer_set')daikin=localStorage.getItem('daikinkamer');
									else if(device=='alex_set')daikin=localStorage.getItem('daikinalex');
									if(daikin==4)circle="hot";
									else if(daikin==3)circle="cold";
									else circle="grey";
									if(daikin==4){
										if($value>17.5)center="orange";
										else if($value>18)center="red";
										else if($value==10)center="grey";
										else center="blue";
									}else if(daikin==3){
										if($value==33)center="grey";
										else if($value>25)center="red";
										else if($value>21)center="orange";
										else center="blue";
									}else center="grey";
								}
								elem=document.getElementById(device);
								html='<img src="https://home.egregius.be/images/thermo'+circle+center+'.png" class="i48" alt="">';
								html+='<div class="fix center" style="top:32px;left:11px;width:26px;">';
								if($mode>0){
									html+='<font size="2" color="#222">'+$value.toString().replace(/[.]/, ",")+'</font></div>';
									html+='<div class="fix" style="top:2px;left:2px;z-index:-100;background:#b08000;width:44px;height:44px;border-radius:45px;"></div>';
								}else{
									html+='<font size="2" color="#CCC">'+$value.toString().replace(/[.]/, ",")+'</font></div>';
								}
								if(time>($currentTime-82800)){
									date=new Date(time*1000);
									hours=date.getHours();
									minutes="0"+date.getMinutes();
									html+='<br><div id="t'+device+'">'+hours+':'+minutes.substr(-2)+'</div>';
								}else{
									html+='<br><div id="t'+device+'">'+formatDate(time)+'</div>';
								}
								
								// Daikin set point
								if(heatingset==-2||heatingset>0){
									if(device=='alex_set'||device=='living_set'||device=='kamer_set'){
										var obj = JSON.parse($icon);
										html+='<br>';
										if(obj.power==0)html+='Off<br>';
										else html+=obj.set+'  '+obj.fan+'<br>';
									}
								}
								
								document.getElementById(device).innerHTML=html;
							}catch{}
						}else if(type=="SetPoint"){
							//console.log(type+" -> "+device+" -> "+$value+" -> "+time+" -> "+$mode);
							try{
								//document.getElementById(device).innerHTML=$value.toString().replace(".0", "");
								document.getElementById(device).innerHTML=$value * 1;
							}catch{}
						}else{
							//console.log(type+" -> "+device+" -> "+$value+" -> "+time+" -> "+$mode);
						}
					}
                }
            }
            try{
                date=new Date($currentTime*1000);
                hours=date.getHours();
                minutes="0"+date.getMinutes();
                seconds="0"+date.getSeconds();
                $("#time").html(hours+':'+minutes.substr(-2)+':'+seconds.substr(-2));
            }catch{}
            try{
                tijd=localStorage.getItem("tijd_water");
                elem=document.getElementById("tdwater");
                if(tijd>$currentTime-15)elem.style.color="#FF0000";
                else if(tijd>$currentTime-30)elem.style.color="#FF4400";
                else if(tijd>$currentTime-60)elem.style.color="#FF8800";
                else if(tijd>$currentTime-90)elem.style.color="#FFAA00";
                else if(tijd>$currentTime-300)elem.style.color="#FFCC00";
                else if(tijd>$currentTime-600)elem.style.color="#FFFF00";
                else elem.style.color=null;
            }catch{}
            try{
                tijd=localStorage.getItem("tijd_gas");
                elem=document.getElementById("tdgas");
                if(tijd>$currentTime-15)elem.style.color="#FF0000";
                else if(tijd>$currentTime-30)elem.style.color="#FF4400";
                else if(tijd>$currentTime-60)elem.style.color="#FF8800";
                else if(tijd>$currentTime-90)elem.style.color="#FFAA00";
                else if(tijd>$currentTime-300)elem.style.color="#FFCC00";
                else if(tijd>$currentTime-600)elem.style.color="#FFFF00";
                else elem.style.color=null;
            }catch{}
            var items=['living_set','badkamer_set','kamer_set','tobi_set','alex_set','zolder_set','belknop','brander','luifel'];
            var arrayLength=items.length;
            for (var i=0; i < arrayLength; i++) {
                try{
                    tijd=localStorage.getItem("tijd_"+items[i]);
                    $value=localStorage.getItem(items[i]);
                    elem=document.getElementById("t"+items[i]);
					date=new Date(tijd*1000);
					hours=date.getHours();
					minutes="0"+date.getMinutes();
					html=hours+':'+minutes.substr(-2);
					if(elem.innerHTML!=html&&tijd>$currentTime-82800)elem.innerHTML=html;
					if(tijd>$currentTime-60)elem.style.color="#FF8800";
					else if(tijd>$currentTime-90)elem.style.color="#FFAA00";
					else if(tijd>$currentTime-300)elem.style.color="#FFCC00";
					else if(tijd>$currentTime-600)elem.style.color="#FFFF00";
					else if(tijd>$currentTime-7200)elem.style.color="#CCC";
					else if(tijd>$currentTime-14400)elem.style.color="#BBB";
					else if(tijd>$currentTime-21600)elem.style.color="#AAA";
					else if(tijd>$currentTime-28800)elem.style.color="#999";
					else if(tijd>$currentTime-36000)elem.style.color="#888";
					else if(tijd>$currentTime-82800)elem.style.color="#777";
					else {
						elem.style.color="#777";
						html=formatDate(tijd);
						if(elem.innerHTML!=html)elem.innerHTML=html;
					}
                }catch{}
            }
            var items=['deurgarage','deurinkom','achterdeur','poort','deurvoordeur','deurbadkamer','deurkamer','deurtobi','deuralex','deurwc','raamliving','raamkeuken','raamkamer','raamtobi','raamalex'];
            var arrayLength=items.length;
            for (var i=0; i < arrayLength; i++) {
                try{
                    tijd=localStorage.getItem("tijd_"+items[i]);
                    $value=localStorage.getItem(items[i]);
                    elem=document.getElementById("t"+items[i]);
                    if($value=="Closed"){
                        if(tijd>$currentTime-60)elem.style.color="#FF8800";
                        else if(tijd>$currentTime-90)elem.style.color="#FFAA00";
                        else if(tijd>$currentTime-300)elem.style.color="#FFCC00";
                        else if(tijd>$currentTime-600)elem.style.color="#FFFF00";
                        else if(tijd>$currentTime-7200)elem.style.color="#CCC";
                        else if(tijd>$currentTime-14400)elem.style.color="#BBB";
                        else if(tijd>$currentTime-21600)elem.style.color="#AAA";
                        else if(tijd>$currentTime-28800)elem.style.color="#999";
                        else if(tijd>$currentTime-36000)elem.style.color="#888";
                        else if(tijd>$currentTime-82800)elem.style.color="#777";
                        else {
                        	elem.style.color="#777";
                        	html=formatDate(tijd);
                        	if(elem.innerHTML!=html)elem.innerHTML=html;
                        }
                    }else{
                        if(tijd>$currentTime-82800)elem.style.color=null;
                        else {
                        	html=formatDate(tijd);
                        	if(elem.innerHTML!=html)elem.innerHTML=html;
                        }
                        elem.style.color="#FFF";
                    }
                }catch{}
            }
            var items=['pirliving','pirinkom','pirhall','pirkeuken','pirgarage'];
            var arrayLength=items.length;
            for (var i=0; i < arrayLength; i++) {
                try{
                    tijd=localStorage.getItem("tijd_"+items[i]);
                    $value=localStorage.getItem(items[i]);
                    elem=document.getElementById("t"+items[i]);
                    if($value=="Off"){
                        if(tijd>$currentTime-60)elem.style.color="#FF8800";
                        else if(tijd>$currentTime-90)elem.style.color="#FFAA00";
                        else if(tijd>$currentTime-300)elem.style.color="#FFCC00";
                        else if(tijd>$currentTime-600)elem.style.color="#FFFF00";
                        else if(tijd>$currentTime-7200)elem.style.color="#CCC";
                        else if(tijd>$currentTime-14400)elem.style.color="#BBB";
                        else if(tijd>$currentTime-21600)elem.style.color="#AAA";
                        else if(tijd>$currentTime-28800)elem.style.color="#999";
                        else if(tijd>$currentTime-36000)elem.style.color="#888";
                        else if(tijd>$currentTime-82800)elem.style.color="#777";
                        else {
                        	elem.style.color="#777";
                        	html=formatDate(tijd);
                        	if(elem.innerHTML!=html)elem.innerHTML=html;
                        }
                    }else{
                        if(tijd>$currentTime-82800)elem.style.color="#FFF";
                        else {
                        	elem.style.color="#FFF";
                        	html=formatDate(tijd);
                        	if(elem.innerHTML!=html)elem.innerHTML=html;
                        }
                    }
                }catch{}
            }
        }
    });
}

function ajaxmedia($ip){
    $.ajax({
        url: '/ajax.php?media',
        dataType : 'json',
        async: true,
        defer: true,
        success: function(data){
        	try{
        		up=human_kb(data['pfsense']['up']*1024);
        		down=human_kb(data['pfsense']['down']*1024);
        	}catch{}
        	denon=localStorage.getItem('denon');
		tv=localStorage.getItem('tv');
		lgtv=localStorage.getItem('lgtv');
			
		try{
				html='<small>&#x21e7;</small> '+up+'<br><small>&#x21e9;</small>'+down;
	            document.getElementById("pfsense").innerHTML=html;
	        }catch{}
            
            try{
				html='';
				if(denon=='Off'&&lgtv=='Off')html+='<button class="btn b1 btnh100" onclick="ajaxcontrol(\'media\', \'media\', \'On\')">Media Power On</button>';
				if(denon=='On'){
					if(data['denon']['power']!='ON')html+='<button class="btn b1 btnh100" onclick="ajaxcontrol(\'media\', \'denon\', \'On\')">Denon Power On</button>';
				}
				if(lgtv=='On'){
					html+='<br>';
					if(data['lgtv']=='com.webos.app.hdmi1')data['lgtv']='Shield';
					else if(data['lgtv']=='youtube.leanback.v4')data['lgtv']='YouTube';
					else if(data['lgtv']=='netflix')data['lgtv']='Netflix';
					inputs=['Shield','Netflix','YouTube'];
					inputs.forEach(function(input){
						if(data['lgtv']==input)html+='<button onclick="ajaxcontrol(\'lgtv\', \'input\', \''+input+'\')" class="btn btna b3 btnh75">'+input+'</button> ';
						else html+='<button onclick="ajaxcontrol(\'lgtv\', \'input\', \''+input+'\')" class="btn b3 btnh75">'+input+'</button> ';
					});
					html+='<br><br><button onclick="ajaxcontrol(\'lgtv\', \'pause\', \'\')" class="btn b2 btnh75">Pause</button><button onclick="ajaxcontrol(\'lgtv\', \'play\', \'\')" class="btn b2 btnh75">Play</button>';
				
				}
				html+='<br><br><button onclick="ajaxcontrol(\'lgtv\', \'volume\', \'down\')" class="btn b2 btnh75">Stiller</button><button onclick="ajaxcontrol(\'lgtv\', \'volume\', \'up\')" class="btn b2 btnh75">Luider</button>';
				if(document.getElementById("media").innerHTML!=html)document.getElementById("media").innerHTML=html;
			 }catch{}
        }
    });
}

function ajaxbose($ip){
    try{clearInterval(myAjax);}catch{};
    $.ajax({
        url: '/ajax.php?bose='+$ip,
        dataType : 'json',
        async: true,
        defer: true,
        success: function(data){
            date=new Date(data["time"]*1000);
            hours=date.getHours();
            minutes="0"+date.getMinutes();
            seconds="0"+date.getSeconds();
            $("#time").html(hours+':'+minutes.substr(-2)+':'+seconds.substr(-2));
			if(data["nowplaying"]["@attributes"]["source"]!="STANDBY"){
				let volume=parseInt(data["volume"]["actualvolume"], 10);
				levels=[-10, -7, -4, -2, -1, 0, 1, 2, 4, 7, 10];
				html="<br>";
				levels.forEach(function(level){
					let newlevel=parseInt(volume+level);
					if(newlevel>=0){
						if(level!=0)html+='<button class="btn volume hover" id="vol'+level+'" onclick="ajaxcontrolbose('+$ip+',\'volume\',\''+newlevel+'\')">'+newlevel+'</button>';
						else html+='<button class="btn volume btna" id="vol'+level+'" onclick="ajaxcontrolbose('+$ip+',\'volume\',\''+newlevel+'\')">'+newlevel+'</button>';
					}
				});
				try{
					if(document.getElementById("volume").innerHTML!=html)document.getElementById("volume").innerHTML=html;
				}catch{};
				let bass=parseInt(data["bass"]["actualbass"], 10);
				levels=[-9, -8, -7, -6, -5, -4, -3, -2, -1, 0];
				html="<br>";
				levels.forEach(function(level){
					if(level!=bass)html+='<button class="btn volume hover" id="bass'+level+'" onclick="ajaxcontrolbose('+$ip+',\'bass\',\''+level+'\')">'+level+'</button>';
					else html+='<button class="btn volume btna" id="bass'+level+'" onclick="ajaxcontrolbose('+$ip+',\'bass\',\''+level+'\')">'+level+'</button>';
				});
				if(document.getElementById("bass").innerHTML!=html)document.getElementById("bass").innerHTML=html;

				if(data["nowplaying"]["@attributes"]["source"]=="SPOTIFY"){
					if(document.getElementById("artist").innerHTML!=data["nowplaying"]["artist"])document.getElementById("artist").innerHTML=data["nowplaying"]["artist"];
					if(document.getElementById("track").innerHTML!=data["nowplaying"]["track"])document.getElementById("track").innerHTML=data["nowplaying"]["track"];
				}else if(data["nowplaying"]["@attributes"]["source"]=="TUNEIN"){
					if(document.getElementById("artist").innerHTML!=data["nowplaying"]["artist"])document.getElementById("artist").innerHTML=data["nowplaying"]["artist"];
					if(document.getElementById("track").innerHTML!=data["nowplaying"]["track"])document.getElementById("track").innerHTML=data["nowplaying"]["track"];
				}else if(data["nowplaying"]["@attributes"]["source"]=="INVALID_SOURCE"){
					try{
						document.getElementById("source").innerHTML=data["nowplaying"]["@attributes"]["source"];
					}catch{};
					html='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'preset\',\'1\')">Mix</button>';
					html+='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'preset\',\'2\')">EDM & Pop</button>';
					html+='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'preset\',\'3\')">EDM + T&K</button>';
					html+='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'preset\',\'4\')">MNM</button>';
					html+='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'preset\',\'5\')">Ballads & Pop</button>';
					html+='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'preset\',\'6\')">Ballads</button>';
					if(document.getElementById("power").textContent!=html){
						document.getElementById("power").innerHTML=html;
					}
				}else{
					document.getElementById("source").innerHTML=data["nowplaying"]["@attributes"]["source"];
				}
				img='None';
				try{
					img=data["nowplaying"]["art"].toString().replace("http://", "https://");
				}catch{};
				if(img=='None')html='';
				else html='<img src="'+img+'" height="160px" width="auto" alt="Art">';
				try{
					elem=document.getElementById("art");
					if(elem.innerHTML!=html)elem.innerHTML=html;
				}catch{};
				if(data["nowplaying"]["@attributes"]["source"]=="SPOTIFY"){
					html='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'skip\',\'prev\')">Prev</button>';
					html+='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'skip\',\'next\')">Next</button>';
					html+='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'preset\',\'1\')">Mix</button>';
					html+='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'preset\',\'2\')">EDM & Pop</button>';
					html+='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'preset\',\'3\')">EDM + T&K</button>';
					html+='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'preset\',\'4\')">MNM</button>';
					html+='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'preset\',\'5\')">Ballads & Pop</button>';
					html+='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'preset\',\'6\')">Ballads</button>';
					html+=data["nowplaying"]["ContentItem"]["itemName"];
				}
				html+='<br><br><br><br><button class="btn b1" onclick="ajaxcontrolbose('+$ip+',\'power\',\'Off\');ajaxbose('+$ip+');myAjaxMedia=setInterval( function() { ajaxbose('+$ip+'); }, 500 );">Power Off</button><br><br>';
			}else{
				document.getElementById("artist").innerHTML="";
				document.getElementById("track").innerHTML="";
				document.getElementById("art").innerHTML="";
				document.getElementById("volume").innerHTML="";
				document.getElementById("bass").innerHTML="";
				html='<button class="btn b1" onclick="ajaxcontrolbose('+$ip+',\'power\',\'On\')">Power On</button>';
				
			}
			if ($ip==101) {
				html+='<br><br>';
				if (data["bose101mode"]==1) {
					html+='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'mode\',\'0\')">Manual</button>';
					html+='<button class="btn b2 btna" onclick="ajaxcontrolbose('+$ip+',\'mode\',\'1\')">Auto</button>';
				} else {
					html+='<button class="btn b2 btna" onclick="ajaxcontrolbose('+$ip+',\'mode\',\'0\')">Manual</button>';
					html+='<button class="btn b2" onclick="ajaxcontrolbose('+$ip+',\'mode\',\'1\')">Auto</button>';
				}
			}
			if(document.getElementById("power").innerHTML!=html){
				document.getElementById("power").innerHTML=html;
			}
        }
    })
}

function ajaxcontrol(device,command,action){
    console.log(device,command,action);
    $.ajax({
        url: '/ajax.php?device='+device+'&command='+command+'&action='+action,
        dataType : 'json',
        async: true,
        defer: true,
        success: function(data){
            console.log(data);
        }
    })
}

function ajaxcontrolbose(ip,command,action){
    console.log(ip,command,action);
    $.ajax({
        url: '/ajax.php?boseip='+ip+'&command='+command+'&action='+action,
        dataType : 'json',
        async: true,
        defer: true,
        success: function(data){
            console.log(data);
        }
    })
}

function floorplan(){
	for (var i = 1; i < 99999; i++){try{window.clearInterval(i);}catch{};}
	localStorage.setItem('view', 'floorplan');
    ajax(0);
	myAjax=setInterval(ajax, 500);
    try{
        html='<div class="fix leftbuttons" id="heating" onclick="floorplanheating();"></div><div class="fix" id="clock" onclick="floorplan();"></div>';
        html+='<div class="fix z1 afval" id="gcal"></div>';
        html+='<div class="fix floorplan2icon" onclick="floorplanothers();"><img src="https://home.egregius.be/images/plus.png" class="i60" alt="plus"></div>';
        html+='<div class="fix picam1" id="picam1"><a href=\'javascript:navigator_Go("picam1/index.php");\'><img src="https://home.egregius.be/images/Camera.png" class="i48" alt="cam"></a></div>';
        html+='<div class="fix Weg" id="Weg"></div>';
        html+='<div class="fix z0 diepvries_temp" id="diepvries_temp"></div>';
        html+='<div class="fix z2" id="sirene"></div>';
        html+='<div class="fix z2" id="daikincmpfreq"></div>';
        html+='<div class="fix z1" id="zoldertrap"></div>';
        items=['alex','eettafel','kamer','ledluifel','lichtbadkamer','terras','tobi','zithoek','zolder','inkom','hall'];
        items.forEach(function(item){html+='<div class="fix z" onclick="dimmer(\''+item+'\');" id="'+item+'"></div>';});
        items=['jbl','diepvries','zoldervuur1','zoldervuur2','badkamervuur1','badkamervuur2','tvtobi','bureeltobi','tuin','kristal','bureel','keuken','wasbak','kookplaat','werkblad1','voordeur','wc','garage','garageled','zolderg','poortrf'];
        items.forEach(function(item){html+='<div class="fix z1 i48" id="'+item+'"></div>';});
		items=['Rbureel','RkeukenL','RkeukenR','Rliving','RkamerL','RkamerR','Rtobi','Ralex'];
        items.forEach(function(item){html+='<div class="fix yellow" id="'+item+'"></div>';});
        items=['raamalex','raamtobi','raamliving','raamkeuken','raamkamer','raamhall','achterdeur','deurvoordeur','deurbadkamer','deurinkom','deurgarage','deurwc','deurkamer','deurtobi','deuralex','poort','zoldervuur2','Usage_grohered','bureeltobikwh','powermeter','zliving','zkeuken','zinkom','zgarage','zhalla','zhallb'];
        items.forEach(function(item){html+='<div class="fix z0" id="'+item+'"></div>';});
        items=['living_temp','badkamer_temp','kamer_temp','tobi_temp','alex_temp','zolder_temp','buiten_temp'];
        items.forEach(function(item){html+='<div class="fix" onclick="location.href=\'temp.php\';" id="'+item+'"></div>';});
		items=['tbelknop','tpirliving','tpirkeuken','tpirgarage','tpirinkom','tpirhall','traamliving','traamkeuken','traamkamer','traamtobi','traamalex','tdeurvoordeur','tdeurbadkamer','tdeurinkom','tdeurgarage','tachterdeur','tpoort','tdeurkamer','tdeurtobi','tdeuralex','tdeurwc'];
        items.forEach(function(item){html+='<div class="fix stamp" id="'+item+'"></div>';});
		items=['bose101','bose102','bose103','bose104','bose105'];
        items.forEach(function(item){html+='<div class="fix" id="'+item+'"></div>';});
        html+='<div class="fix verbruik" onclick="location.href=\'https://verbruik.egregius.be/dag.php?Guy=on\';" id="verbruik"><table><tr id="trelec"></tr><tr id="trzon"><td>Zon:</td><td id="zon"></td><td id="zonvandaag"></td></tr><tr id="trgas"></tr><tr id="trwater"></tr><tr id="trdgas"></tr><tr id="trdwater"></tr>';
        watertuin=localStorage.getItem('watertuin');
			if(watertuin!='undefined')html+='<tr><td>Tuin</td><td id="watertuin">'+watertuin+' L</td></tr>';
		html+='</table></div>';
        $('#placeholder').html(html).fadeIn(3000);
   }catch{}
   sidebar();
}

function floorplanheating(){
	for (var i = 1; i < 99999; i++){try{window.clearInterval(i);}catch{};}
	localStorage.setItem('view', 'floorplanheating');
    heatingset=localStorage.getItem('heating');
    ajax(0);
	myAjax=setInterval(ajax, 500);
    try{
        html='<div class="fix floorplan2icon" onclick="floorplanothers();"><img src="https://home.egregius.be/images/plus.png" class="i60" alt="plus"></div>';
        html+='<div class="fix" id="clock" onclick="floorplanheating();"></div>';
        html+='<div class="fix z2" id="sirene"></div>';
        html+='<div class="fix z2" id="daikincmpfreq"></div>';
        html+='<div class="fix z1" style="top:5px;left:5px;" onclick="floorplan();"><img src="https://home.egregius.be/images/close.png" width="72px" height="72px" alt="Close"></div>';
        html+='<div class="fix z1" style="top:290px;left:415px;"><a href=\'javascript:navigator_Go("floorplan.doorsensors.php");\'><img src="https://home.egregius.be/images/close.png" width="72px" height="72px" alt="Close"></a></div>';
        items=['badkamervuur1','badkamervuur2','zoldervuur1','zoldervuur2','GroheRed'];
        items.forEach(function(item){html+='<div class="fix z1 i48" id="'+item+'"></div>';});
        items=['Rbureel','RkeukenL','RkeukenR','Rliving','RkamerL','RkamerR','Rtobi','Ralex'];
        items.forEach(function(item){html+='<div class="fix yellow" id="'+item+'"></div>';});
        items=['raamalex','raamtobi','raamliving','raamkeuken','raamkamer','raamhall','achterdeur','deurvoordeur','deurbadkamer','deurinkom','deurgarage','deurwc','deurkamer','deurtobi','deuralex','poort','zoldervuur2','Usage_grohered','bureeltobikwh','zliving','zkeuken','zinkom','zgarage','zhalla','zhallb'];
        items.forEach(function(item){html+='<div class="fix" id="'+item+'"></div>';});
		items=['living','badkamer','kamer','tobi','alex','zolder','buiten'];
        items.forEach(function(item){html+='<div class="fix" onclick="location.href=\'temp.php\';" id="'+item+'_temp"></div>';});
		items=['Rliving','Rbureel','RkeukenL','RkeukenR','RkamerL','RkamerR','Rtobi','Ralex'];        
        items.forEach(function(item){html+='<div class="fix z" onclick="roller(\''+item+'\');" id="R'+item+'"></div>';});
        items=['tbelknop','tpirliving','tpirkeuken','tpirgarage','tpirinkom','tpirhall','traamliving','traamkeuken','traamkamer','traamtobi','traamalex','tdeurvoordeur','tdeurbadkamer','tdeurinkom','tdeurgarage','tachterdeur','tpoort','tdeurkamer','tdeurtobi','tdeuralex','tdeurwc'];
        items.forEach(function(item){html+='<div class="fix stamp" id="'+item+'"></div>';});
        if(heatingset==2)items=['living','badkamer','kamer','tobi','alex','zolder'];
        else if(heatingset==1)items=['living','badkamer','kamer','alex','zolder'];
        else if(heatingset==0)items=['badkamer'];
        else if(heatingset==-2)items=['living','kamer','alex'];
		if(heatingset!=-1)items.forEach(function(item){html+='<div class="fix z2 '+item+'_set" onclick="setpoint(\''+item+'\');" id="'+item+'_set"></div>';});
        html+='<div class="fix z" onclick="roller(\'luifel\');" id="luifel"></div>';
        html+='<div class="fix z" id="bovenbeneden"><a href=\'javascript:navigator_Go("floorplan.daikinpowerusage.php");\' class="btn">Daikin Power Usage</a><br><br><button class="btn btnh" onclick="ajaxcontrol(\'tv\',\'roller\',\'tv\');initview();">TV</button> &nbsp; <button class="btn btnf" onclick="roller(\'Beneden\');">Beneden</button> &nbsp; <button class="btn btnf" onclick="roller(\'Boven\');">Boven</button></div>';
        html+='<div class="fix divsetpoints z"><table class="tablesetpoints">';
        if(heatingset==2)html+='<tr><td width="65px" id="brander"></td><td align="left" height="60" width="80px" style="line-height:18px">Brander<br><span id="tbrander"></span></td></tr>';
        html+='<tr id="heatingauto"></tr>';
        html+='<tr id="trheating"></tr>';
        html+='</table></div>';
        $('#placeholder').html(html);
    }catch{}
    sidebar();
}

function floorplanmedia(){
    for (var i = 1; i < 99999; i++){try{window.clearInterval(i);}catch{};}
	localStorage.setItem('view', 'floorplanmedia');
    ajax(0);
    ajaxmedia();
    myAjax=setInterval(ajax, 800);
    myAjaxmedia=setInterval(ajaxmedia, 900);
    try{
        tv=localStorage.getItem("tv");
		lgtv=localStorage.getItem("lgtv");
		html='<div class="fix jbl z1 i48" id="jbl"></div>';
        html+='<div class="fix" id="clock" onclick="floorplanmedia();"></div>';
        html+='<div class="fix z1" style="top:5px;left:5px;" onclick="floorplan();"><img src="https://home.egregius.be/images/close.png" width="72px" height="72px" alt="Close"></div>';
        html+='<div class="fix z2" id="sirene"></div>';
        items=['eettafel','zithoek'];
        items.forEach(function(item){html+='<div class="fix z" onclick="dimmer(\''+item+'\');" id="'+item+'"></div>';});
        items=['kristal','bureel','keuken','wasbak','kookplaat','werkblad1','kristal','kristal','denon','nas','nvidia'];
        items.forEach(function(item){html+='<div class="fix z1 i48" id="'+item+'"></div>';});
		if(tv=="On")html+='<div class="fix z1 i48" id="lgtv"></div>';
		else if(tv=="Off")html+='<div class="fix z1 i48" id="tv"></div>';
		items=['bureel','keukenL','keukenR','living'];
        items.forEach(function(item){html+='<div class="fix yellow" id="R'+item+'"></div>';});
		items=['living','keuken','inkom'];
        items.forEach(function(item){html+='<div class="fix z0" id="z'+item+'"></div>';});
		items=['raamliving','raamkeuken','deurvoordeur','deurinkom','deurgarage','deurwc'];
        items.forEach(function(item){html+='<div class="fix" id="'+item+'"></div>';});
        html+='<div class="fix blackmedia" id="media"></div>';
        html+='<div class="fix" id="mediasidebar"><br><a href=\'javascript:navigator_Go("denon.php");\'><img src="https://home.egregius.be/images/denon.png" class="i48" alt=""></a><br><br><br><a href=\'javascript:navigator_Go("https://films.egregius.be/films.php");\'><img src="https://home.egregius.be/images/kodi.png" class="i48" alt=""><br>Films</a><br><br><a href=\'javascript:navigator_Go("https://films.egregius.be/series.php");\'><img src="https://home.egregius.be/images/kodi.png" class="i48" alt=""><br>Series</a><br><br><a href=\'javascript:navigator_Go("kodi.php");\'><img src="https://home.egregius.be/images/kodi.png" class="i48" alt=""><br>Kodi<br>Control</a><br><br>';
        html+='<div id="playpause"></div>';
        html+='<div id="pfsense"></div></div>';
        $('#placeholder').html(html);
    }catch{}
}

function floorplanbose(){
    for (var i = 1; i < 99999; i++){try{window.clearInterval(i);}catch{};}
	ajaxbose($ip)();
    myAjaxmedia=setInterval(function(){ajaxbose($ip);}, 999);
    try{
	}catch{}
}

function floorplanothers(){
	for (var i = 1; i < 99999; i++){try{window.clearInterval(i);}catch{};}
	localStorage.setItem('view', 'floorplanothers');
    ajax(0);
    myAjax=setInterval(ajax, 999);
    try{
        html='<div class="fix floorplan2icon" onclick="floorplanothers();"><img src="https://home.egregius.be/images/plus.png" class="i60" alt="plus"></div>';
        html+='<div class="fix" id="clock" onclick="floorplanothers();"></div>';
        html+='<div class="fix z1" style="top:5px;left:5px;" onclick="floorplan();"><img src="https://home.egregius.be/images/close.png" width="72px" height="72px"/></div>';
        items=['auto','tv','nvidia','bosesoundlink','denon','water','regenpomp','zwembadfilter','zwembadwarmte','dampkap'];
        items.forEach(function(item){html+='<div class="fix z1 i48" style="width:70px;" id="'+item+'"></div>';});
        html+='<div class="fix z1 center" style="top:370px;left:410px;"><a href=\'javascript:navigator_Go("bat.php");\'><img src="https://home.egregius.be/images/verbruik.png" width="40px" height="40px"/><br/>&nbsp;Bats</a></div><div class="fix z1 center" style="top:20px;left:130px;">';
        gcal=localStorage.getItem('gcal');
        if(gcal==true)html+='Tobi: Beitem';
        else html+='Tobi: Ingelmunster';
        html+='</div>';
        low=localStorage.getItem('regenputleeg');
        high=localStorage.getItem('regenputvol');
        if(low=='Off'&&high=='Off')html+='<div class="fix" id="regenput"><img src="https://home.egregius.be/images/regenputrood.png"></div>';
        else if(low=='On'&&high=='Off')html+='<div class="fix" id="regenput"><img src="https://home.egregius.be/images/regenputblauw.png"></div>';
        else if(low=='On'&&high=='On')html+='<div class="fix" id="regenput"><img src="https://home.egregius.be/images/regenputgroen.png"></div>';
        html+='<div class="fix z1 center" style="top:600px;left:20px;"><a href=\'javascript:navigator_Go("logs.php");\'><img src="https://home.egregius.be/images/log.png" width="40px" height="40px"/><br>Log</a></div>';
		html+='<div class="fix z1 center" style="top:600px;left:120px;"><a href=\'javascript:navigator_Go("floorplan.cache.php?nicestatus");\'><img src="https://home.egregius.be/images/log.png" width="40px" height="40px"/><br>Cache</a></div>';
		html+='<div class="fix z1 center" style="top:600px;left:220px;"><a href=\'javascript:navigator_Go("floorplan.sounds.php");\'><img src="https://home.egregius.be/images/log.png" width="40px" height="40px"/><br>Sounds</a></div>';
		html+='<div class="fix z1 center" style="top:600px;left:320px;"><a href=\'javascript:navigator_Go("floorplan.ontime.php");\'><img src="https://home.egregius.be/images/log.png" width="40px" height="40px"/><br>On-Time</a></div>';
		html+='<div class="fix z1 center" style="top:600px;left:420px;"><a href=\'javascript:navigator_Go("floorplan.ontimemysql.php");\'><img src="https://home.egregius.be/images/log.png" width="40px" height="40px"/><br>On-Time<br>MySQL</a></div>';
		html+='<div class="fix z1 center" style="top:700px;left:0px;width:300px;"><button onclick="ajaxcontrol(\'fetch\', \'fetch\', \'fetch\');floorplan();" class="btn b2">Fetch Domoticz</button></div>';
		html+='<div class="fix z1 center" style="top:700px;left:150px;width:300px;"><button onclick="ajaxcontrol(\'imac\', \'wake\', \'wake\');" class="btn b2">Wake iMac</button></div>';
		html+='<div class="fix z1 center" style="top:760px;left:75px;width:300px;"><a href=\'javascript:navigator_Go("floorplan.proxmox.php");\' class="btn b2">Proxmox</a></div>';
		html+='<div class="fix blackmedia">';
		html+='<div class="fix" style="top:230px;left:0px;width:400px">';
		water=localStorage.getItem('water');
		water$mode=localStorage.getItem('watermode');
		if(water=='On'){
			if ($mode==300) {
            	html+='<button onclick="ajaxcontrol(\'water\', \'water\', 300);" class="btn b3 btna" id="water300">Water 5 min</button>';
			} else {
            	html+='<button onclick="ajaxcontrol(\'water\', \'water\', 300);" class="btn b3" id="water300">Water 5 min</button>';
			}
			if ($mode==1800) {
            	html+='<button onclick="ajaxcontrol(\'water\', \'water\', 1800);" class="btn b3 btna" id="water1800">Water 30 min</button>';
			} else {
            	html+='<button onclick="ajaxcontrol(\'water\', \'water\', 1800);" class="btn b3" id="water1800">Water 30 min</button>';
			}
			if ($mode==7200) {
            	html+='<button onclick="ajaxcontrol(\'water\', \'water\', 7200);" class="btn b3 btna" id="water7200">Water 2 uur</button>';
			} else {
            	html+='<button onclick="ajaxcontrol(\'water\', \'water\', 7200);" class="btn b3" id="water7200">Water 2 uur</button>';
			}
		}else{
			html+='<button onclick="ajaxcontrol(\'water\', \'water\', 300);" class="btn b3" id="water300">Water 5 min</button>';
        	html+='<button onclick="ajaxcontrol(\'water\', \'water\', 1800);" class="btn b3" id="water1800">Water 30 min</button>';
        	html+='<button onclick="ajaxcontrol(\'water\', \'water\', 7200);" class="btn b3" id="water7200">Water 2 uur</button>';
		}
		watertuin=localStorage.getItem('watertuin');
		if(watertuin!='undefined')html+='<br><span id="watertuin">'+watertuin+' L</span>';
		html+='<div class="fix z1 bottom" style="right:0px"><form method="POST"><input type="hidden" name="username" $value="logout"/><input type="submit" name="logout" $value="Logout" class="btn" style="padding:0px;margin:0px;width:90px;height:35px;"/></form><br/><br/></div>';
        $('#placeholder').html(html);
    }catch{}
}

function sidebar(){
    try{
		tv=localStorage.getItem("tv");
		lgtv=localStorage.getItem("lgtv");
		denonpower=localStorage.getItem("denonpower");
		html='<div class="fix weather"><a href=\'javascript:navigator_Go("floorplan.weather.php");\'><img src="" alt="icon" id="icon"></a></div>';
		html+='<div class="fix mediabuttons" onclick="floorplanmedia();">';
		if(denonpower=="On")html+='<img src="https://home.egregius.be/images/denon_On.png" class="i70" alt="denon" id="denonicon">';
		else html+='<img src="https://home.egregius.be/images/denon_Off.png" class="i70" alt="denon" id="denonicon">';
		html+='<br>';
		if(tv=="On"){
			if(lgtv=="On")html+='<img src="https://home.egregius.be/images/lgtv_On.png" class="i60" alt="lgtv" id="lgtvicon">';
			else if(lgtv=="Off")html+='<img src="https://home.egregius.be/images/lgtv_Off.png" class="i60" alt="lgtv" id="lgtvicon">';
		}else if(tv=="Off")html+='<img src="https://home.egregius.be/images/tv_Off.png" class="i60" alt="tv" id="tvicon">';
		html+='<br>';
		html+='<br></div><div class="fix center zon"><span id="maxtemp"></span><br><span id="mintemp"></span><br><a href=\'javascript:navigator_Go("regen.php");\'><span id="buien"></span></a><br><span id="wind"></span><br><br><img src="images/sunrise.png" alt="sunrise"><br><small>&#x21e7;</small><span id="zonop"></span><br><small>&#x21e9;</small><span id="zononder"></span><br><div id="uv"></div></div>';
		document.getElementById('placeholder').insertAdjacentHTML('beforeend', html);
	}catch{}
}

function pad(n, length){
    len=length - (''+n).length;
    return (len>0 ? new Array(++len).join('0') : '')+n
}

function toggle_visibility(id){
    e=document.getElementById(id);
    if(e.style.display=='inherit') e.style.display='none';
    else e.style.display='inherit';
}

function fix(){
    var el=this;
    var par=el.parentNode;
    var next=el.nextSibling;
    par.removeChild(el);
    setTimeout(function() {par.insertBefore(el, next);}, 0)
}

function human_kb(fileSizeInBytes) {
    var i = -1;
    var byteUnits = [' kbps', ' Mbps', ' Gbps', ' Tbps', 'Pbps', 'Ebps', 'Zbps', 'Ybps'];
    do {
        fileSizeInBytes = fileSizeInBytes / 1024;
        i++;
    } while (fileSizeInBytes > 1024);
    return Math.max(fileSizeInBytes, 0.1).toFixed(1) + byteUnits[i];
};


function initview(){
	view=localStorage.getItem('view');
	console.log('view = '+view);
	if(view=="floorplan")window["floorplan"]();
	else if(view=="floorplanmedia")window["floorplanmedia"]();
	else if(view=="floorplanheating")window["floorplanheating"]();
	else if(view=="floorplanothers")window["floorplanothers"]();
	else if(view=="floorplandaikin")window["floorplandaikin"]();
	else window["floorplan"]();
}

function setpoint(device){
	level=localStorage.getItem(device+'_set');
	try{
		icon=JSON.parse(localStorage.getItem(device+'_set_icon'));
	}catch{}
	daikin=JSON.parse(localStorage.getItem('daikin'+device+'_value'));
	heatingset=localStorage.getItem('heating');
	temp=localStorage.getItem(device+'_temp');
	$mode=localStorage.getItem(device+'_set_mode');
	console.log($mode);
	html='<div class="fix dimmer" ><h2>'+device+' = '+temp+'°C</h2><h2>Set = '+level+'°C</h2>';
	if($mode==1){
		html+='<div class="fix btn btna" style="top:105px;left:25px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'storemode\',\'1\');initview();"><br>Manueel</div>';
		html+='<div class="fix btn" style="top:105px;left:380px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'storemode\',\'0\');initview();"><br>Auto</div>';
	}else{
		html+='<div class="fix btn" style="top:105px;left:25px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'storemode\',\'1\');initview();"><br>Manueel</div>';
		html+='<div class="fix btn btna" style="top:105px;left:380px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'storemode\',\'0\');initview();"><br>Auto</div>';
	}
	if(heatingset==2){
		if(device=='badkamer'){
			temps=[10,14,15,16,17,17.5,18,18.5,19,19.5,20,20.5,21,21.5,22];
		}else if(device=='living'){
			temps=[15,16,17,17.5,18,18.5,19,19.5,20,20.5,20.6,20.7,20.8,20.9,21,21.1,21.2,21.3,21.4,21.5,21.6,21.7,21.8,21.9,22];
		}else if(device=='zolder'){
			temps=[4,7,8,9,10,11,12,13,14,15,16,16.5,17,17.5,18,18.5,19,19.5,19.6,19.7,19.8,19.9,20,20.1,20.2,20.3,20.4,20.5];
		}else{
			temps=[10,10.5,11,11.5,12,12.5,13,13.5,14,14.5,15,15.5,16,16.5,17];
		}
	}else if(heatingset==1){
		if(device=='badkamer'){
			temps=[10,14,15,16,17,17.5,18,18.5,19,19.5,20,20.5,21,21.5,22];
		}else if(device=='living'){
			temps=[10,14,15,16,16.5,17,17.5,18,18.5,19,19.5,20,20.5,21,21.5];
		}else if(device=='zolder'){
			temps=[4,14,14.5,15,15.5,16,16.5,17,17.5,18,18.5,19,19.5,20,20.5];
		}else{
			temps=[10,10.5,11,11.5,12,12.5,13,13.5,14,14.5,15,15.5,16,16.5,17];
		}
	}else{
		temps=[17,18,18.5,19,19.5,20,20.5,21,21.5,22,22.5,23,23.5,24,33];
	}
	html+='<div class="fix z" style="top:210px;left:10px;">';
	temps.forEach(function(temp){
		if(level==temp)html+='<button class="dimlevel dimlevela" onclick="ajaxcontrol(\''+device+'\',\'setpoint\',\''+temp+'\');floorplanheating();">'+temp+'</button>';
		else html+='<button class="dimlevel" onclick="ajaxcontrol(\''+device+'\',\'setpoint\',\''+temp+'\');floorplanheating();">'+temp+'</button>';
	});
	html+='</div><div class="fix z" style="top:5px;left:5px;" onclick="floorplanheating();"><img src="https://home.egregius.be/images/close.png" width="72px" height="72px" alt="Close"></div>';
	if(heatingset<-1||heatingset>0){
		if(device=='living'||device=='kamer'||device=='alex'){
			var adv = daikin.adv;
			if (adv == '') {
				streamer=0;
				powermode=0;
			} else if (adv.match(/\//ig)) { 
				var advs = adv.split("/");
				console.log(advs);
				if (advs[1]==13) streamer=1;
				else if (adv[1]=='') streamer=0;
				if (advs[0]==2) powermode=2;
				else if (advs[0]==12) powermode=1;
				else powermode=0;
			} else {
				if (adv==13) {
					streamer=1;
					powermode=0;
				} else if (adv==12) {
					streamer=0;
					powermode=1;
				} else if (adv==2) {
					streamer=0;
					powermode=2;
				} else if (adv=='') {
					streamer=0;
					powermode=0;
				}
				
			}
			console.log(device+' streamer = '+streamer+' powermode = '+powermode);
			html+='<div class="fix z" style="top:670px;left:5px;">';
			if(powermode==0){
				html+='<h3>Powermode</h3><div class="fix btn" style="top:-3px;left:140px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'powermode\',\'Eco\');initview();"><br>Eco</div>';
				html+='<div class="fix btn btna" style="top:-3px;left:252px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'powermode\',\'Normal\');initview();"><br>Normal</div>';
				html+='<div class="fix btn" style="top:-3px;left:364px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'powermode\',\'Power\');initview();"><br>Power</div>';
			} else if(powermode==1){
				html+='<h3>Powermode</h3><div class="fix btn btna" style="top:-3px;left:140px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'powermode\',\'Eco\');initview();"><br>Eco</div>';
				html+='<div class="fix btn" style="top:-3px;left:252px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'powermode\',\'Normal\');initview();"><br>Normal</div>';
				html+='<div class="fix btn" style="top:-3px;left:364px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'powermode\',\'Power\');initview();"><br>Power</div>';
			} else if(powermode==2){
				html+='<h3>Powermode</h3><div class="fix btn" style="top:-3px;left:140px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'powermode\',\'Eco\');initview();"><br>Eco</div>';
				html+='<div class="fix btn" style="top:-3px;left:252px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'powermode\',\'Normal\');initview();"><br>Normal</div>';
				html+='<div class="fix btn btna" style="top:-3px;left:364px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'powermode\',\'Power\');initview();"><br>Power</div>';
			}
			if(streamer==0){
				html+='<br><h3>Streamer</h3><div class="fix btn" style="top:81px;left:140px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'streamer\',\'On\');initview();"><br>On</div>';
				html+='<div class="fix btn btna" style="top:81px;left:252px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'streamer\',\'Off\');initview();"><br>Off</div>';
			} else {
				html+='<br><h3>Streamer</h3><div class="fix btn btna" style="top:81px;left:140px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'streamer\',\'On\');initview();"><br>On</div>';
				html+='<div class="fix btn" style="top:81px;left:252px;width:110px;height:80px;font-size:2em" onclick="ajaxcontrol(\''+device+'_set\',\'streamer\',\'Off\');initview();"><br>Off</div>';
			}
			html+='</div>';
		}
	}	
	$('#placeholder').html(html);
	
}

function dimmer(device,floorplan='floorplan'){
	clearInterval(myAjax);
	$mode=localStorage.getItem(device+'mode');
	current=localStorage.getItem(device);
	console.log(device+current+$mode);
	html='<div class="dimmer" ><div style="min-height:140px">';
	if(current==0)html+='<h2>'+device+' Off</h2>';
	else html+='<h2>'+device+' '+current+' %</h2>';
	html+='<div class="fix z" style="top:80px;left:30px;" onclick="ajaxcontrol(\''+device+'\',\'dimmer\',\'0\');initview();"><img src="images/light_Off.png" class="i90"></div>';
	html+='<div class="fix z" style="top:80px;left:150px;" onclick="ajaxcontrol(\''+device+'\',\'dimmersleep\',\'0\');initview();"><img src="images/Sleepy.png" class="i90"></div>';
	if($mode==1)html+='<div class="fix" style="top:80px;left:150px;z-index:-100;background:#ffba00;width:90px;height:90px;border-radius:45px;"></div>';
	html+='<div class="fix z" style="top:80px;left:265px;" onclick="ajaxcontrol(\''+device+'\',\'dimmerwake\',\'0\');initview();"><img src="images/Wakeup.png" class="i90"></div>';
	if($mode==2)html+='<div class="fix" style="top:80px;left:265px;z-index:-100;background:#ffba00;width:90px;height:90px;border-radius:45px;"></div>';
	html+='<div class="fix z" style="top:80px;left:385px;" onclick="ajaxcontrol(\''+device+'\',\'dimmer\',\'100\');initview();"><img src="images/light_On.png" class="i90"></div>';
	html+='</div><div>';
	levels=[1,2,3,4,5,6,7,8,9,10,12,14,16,18,20,22,24,26,28,30,32,35,40,45,50,55,60,65,70,75,80,85,90,95,100];
	if(levels.includes(parseInt(current))){console.log('if');}else{console.log('false');if(current>0&&current<100)levels.push(current);}
	levels.sort((a, b) => a - b);
	levels.forEach(function(level){
		if(current==level)html+='<button class="dimlevel dimlevela" onclick="ajaxcontrol(\''+device+'\',\'dimmer\',\''+level+'\');initview();">'+level+'</button>';
		else html+='<button class="dimlevel" onclick="ajaxcontrol(\''+device+'\',\'dimmer\',\''+level+'\');initview();">'+level+'</button>';
	});
	html+='</div><div class="fix z" style="top:5px;left:5px;" onclick="floorplan();"><img src="https://home.egregius.be/images/close.png" width="72px" height="72px" alt="Close"></div>';
	$('#placeholder').html(html);
}

function roller(device,floorplan='floorplanheating'){
	clearInterval(myAjax);
	current=localStorage.getItem(device);
	html='<div class="dimmer" ><div style="min-height:140px">';
	if(current==0){
		if(device=='luifel')html+='<h2>'+device+' Dicht</h2><div class="fix" style="top:90px;left:385px;z-index:-100;background:#ffba00;width:90px;height:90px;border-radius:45px;"></div>';
		else html+='<h2>'+device+' Open</h2><div class="fix" style="top:90px;left:385px;z-index:-100;background:#ffba00;width:90px;height:90px;border-radius:45px;"></div>';
	} else if(current==100){
		if(device=='luifel')html+='<h2>'+device+' Open</h2><div class="fix" style="top:90px;left:30px;z-index:-100;background:#ffba00;width:90px;height:90px;border-radius:45px;"></div>';
		else html+='<h2>'+device+' Dicht</h2><div class="fix" style="top:90px;left:30px;z-index:-100;background:#ffba00;width:90px;height:90px;border-radius:45px;"></div>';
	} else if(device!='Beneden'&&device!='Boven') html+='<h2>'+device+' '+current+' %</h2>';
	else html+='<h2>'+device+'</h2>';
	if(device=='luifel'){
		mode=localStorage.getItem(device+'mode');
		if(mode==1)html+='<button class="btn btna b4" onclick="ajaxcontrol(\'luifel\',\'mode\',\'1\');initview();">Manueel</button><button class="btn b4" onclick="ajaxcontrol(\'luifel\',\'mode\',\'0\');initview();">Auto</button>';
		else html+='<button class="btn b4" onclick="ajaxcontrol(\'luifel\',\'mode\',\'1\');initview();">Manueel</button><button class="btn btna b4" onclick="ajaxcontrol(\'luifel\',\'mode\',\'0\');initview();">Auto</button>';
	}
	html+='<div class="fix z" style="top:90px;left:30px;"><img src="images/arrowgreendown.png" class="i90" onclick="ajaxcontrol(\''+device+'\',\'roller\',\'100\');initview();"></div>';
	
	html+='<div class="fix z" style="top:90px;left:385px;"><img src="images/arrowgreenup.png" class="i90"  onclick="ajaxcontrol(\''+device+'\',\'roller\',\'0\');initview();"></div>';
	html+='</div><div class="fix z" style="top:190px;left:0px;">';
	levels=[5,10,15,20,25,30,32,34,36,38,40,42,44,46,48,50,52,54,56,58,60,62,64,66,68,70,72,74,76,78,80,82,85,90,95];
	if(levels.includes(parseInt(current))){console.log('if');}else{console.log('false');if(current>0&&current<100)levels.push(current);}
	levels.sort((a, b) => a - b);
	levels.forEach(function(level){
		if(current==level)html+='<button class="dimlevel dimlevela" onclick="ajaxcontrol(\''+device+'\',\'roller\',\''+level+'\');initview();">'+level+'</button>';
		else html+='<button class="dimlevel" onclick="ajaxcontrol(\''+device+'\',\'roller\',\''+level+'\');initview();">'+level+'</button>';
	});
	html+='</div><div class="fix z" style="top:5px;left:5px;" onclick="floorplanheating();"><img src="https://home.egregius.be/images/close.png" width="72px" height="72px" alt="Close"></div>';
	$('#placeholder').html(html);
}

function Weg(){
	html='<div class="dimmer" ><div style="min-height:140px">';
	html+='<div class="fix" style="top:5px;left:5px;z-index:200000" onclick="floorplan();"><img src="https://home.egregius.be/images/close.png" width="72px" height="72px" alt="Close"></div>';
	html+='<div id="message" class="dimmer">';
	poort=localStorage.getItem('poort');
	achterdeur=localStorage.getItem('achterdeur');
	bureeltobi=localStorage.getItem('bureeltobi');
	raamliving=localStorage.getItem('raamliving');
	if(poort=='Open'||achterdeur=='Open'||raamliving=='Open'||raamhall=='Open'||bureeltobi=='On')html+='<h1>OPGELET!<br>';
	if(poort=='Open')html+='Poort OPEN<br>';
	if(achterdeur=='Open')html+='Achterdeur OPEN<br>';
	if(raamliving=='Open')html+='Raam Living OPEN<br>';
	if(raamhall=='Open')html+='Raam Hall OPEN<br>';
	if(raamkeuken=='Open')html+='Raam Keuken OPEN<br>';
	if(bureeltobi=='On')html+='Bureel Tobi AAN<br>';
	if(poort=='Open'||achterdeur=='Open'||raamliving=='Open'||raamhall=='Open'||bureeltobi=='On'){
		html+='</h1>';
		huge='huge6';
	}
	else huge='huge4';
	html+='<button class="btn '+huge+'" style="display:inline-block;background-image:url(images/Vacation.png);background-repeat:no-repeat;background-position:center left 58px;" onclick="ajaxcontrol(\'Weg\',\'Weg\',\'3\');initview();">Vacation</button>';
	html+='<button class="btn '+huge+'" style="display:inline-block;background-image:url(images/Weg.png);background-repeat:no-repeat;background-position:center left 58px;" onclick="ajaxcontrol(\'Weg\',\'Weg\',\'2\');initview();">Weg</button>';
    html+='<button class="btn '+huge+'" style="display:inline-block;background-image:url(images/Slapen.png);background-repeat:no-repeat;background-position:center left 58px;" onclick="ajaxcontrol(\'Weg\',\'Weg\',\'1\');initview();">Slapen</button>';
    html+='<button class="btn '+huge+'" style="display:inline-block;background-image:url(images/Thuis.png);background-repeat:no-repeat;background-position:center left 58px;" onclick="ajaxcontrol(\'Weg\',\'Weg\',\'0\');initview();">Thuis</button>';
    html+='</div>';
	html+='</div>';
	$('#placeholder').html(html);
}

function heating(){
	html='<div class="dimmer" ><div style="min-height:140px">';
	html+='<div class="fix" style="top:5px;left:5px;z-index:200000" onclick="floorplanheating();"><img src="https://home.egregius.be/images/close.png" width="72px" height="72px" alt="Close"></div>';
	html+='<div id="message" class="dimmer">';
	html+='<button class="btn huge5" style="display:inline-block;background-image:url(images/fire_On.png);background-repeat:no-repeat;background-position:center left 58px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'2\');sleep(500);initview();">Gas heating</button>';
    html+='<button class="btn huge5" style="display:inline-block;background-image:url(images/Cooling_red.png);background-repeat:no-repeat;background-position:center left 58px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'1\');sleep(500);initview();">Airco heating</button>';
    html+='<button class="btn huge5" style="display:inline-block;background-image:url(images/close.png);background-repeat:no-repeat;background-position:center left 58px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'0\');sleep(500);initview();">Neutral</button>';
    html+='<button class="btn huge5" style="display:inline-block;background-image:url(images/Cooling_grey.png);background-repeat:no-repeat;background-position:center left 58px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'-1\');sleep(500);initview();">Passive cooling</button>';
    html+='<button class="btn huge5" style="display:inline-block;background-image:url(images/Cooling.png);background-repeat:no-repeat;background-position:center left 58px;" onclick="ajaxcontrol(\'heating\',\'heating\',\'-2\');sleep(500);initview();">Airco cooling</button>';
    html+='</div>';
	html+='</div>';
	$('#placeholder').html(html);
}

function confirmSwitch(device){
	console.log(device);
	$value=localStorage.getItem(device);
	html='<div class="dimmer" ><div style="min-height:140px">';
	html+='<div class="fix" style="top:5px;left:5px;z-index:200000" onclick="floorplan();"><img src="https://home.egregius.be/images/close.png" width="72px" height="72px" alt="Close"></div>';
	html+='<div id="message" class="dimmer">';
	html+='<br><h1>'+device+' = '+$value+'</h1><br>';
	html+='<button class="btn huge3" onclick="ajaxcontrol(\''+device+'\',\'sw\',\'On\');initview();">On</button>';
    html+='<button class="btn huge3" onclick="ajaxcontrol(\''+device+'\',\'sw\',\'Off\');initview();">Off</button>';
    html+='</div>';
	html+='</div>';
	$('#placeholder').html(html);
}

function bureeltobi(){
	$value=localStorage.getItem('bureeltobi');
	html='<div class="dimmer" ><div style="min-height:140px">';
	html+='<div class="fix" style="top:5px;left:5px;z-index:200000" onclick="floorplan();"><img src="https://home.egregius.be/images/close.png" width="72px" height="72px" alt="Close"></div>';
	html+='<div id="message" class="dimmer">';
	html+='<br><h1>Bureel Tobi = '+$value+'</h1><br>';
	html+='<button class="btn huge3" onclick="ajaxcontrol(\'bureeltobi\',\'sw\',\'On\');initview();">On</button>';
    html+='<button class="btn huge3" onclick="ajaxcontrol(\'bureeltobi\',\'sw\',\'Off\');initview();">Off</button>';
    html+='</div>';
	html+='</div>';
	$('#placeholder').html(html);
}

function formatDate(nowDate) {
	date=new Date(nowDate*1000);
	var day = date.getDate();
	var month = date.getMonth()+1;
	return (day+'/'+month);			
}

function sleep(millis)
{
    console.log('sleep = '+millis);
    var date = new Date();
    var curDate = null;
    do { curDate = new Date(); }
    while(curDate-date < millis);
}
