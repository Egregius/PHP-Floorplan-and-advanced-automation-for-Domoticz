function navigator_Go(url){window.location.assign(url);}
var $LastUpdateTime=parseInt(0);
function ajax(){
    $.ajax({
        url: '/ajax.php?timestamp='+$LastUpdateTime,
        dataType : 'json',
        async: true,
        success: function(d){
            for (var device in d){
                if(d.hasOwnProperty(device)){
                    name=device;
                    time=d[device]['t'];
                    if(name=="time"){
                        $LastUpdateTime=parseInt(time);
                        try{
                            date=new Date(time*1000);
                            hours=date.getHours();
                            minutes="0"+date.getMinutes();
                            seconds="0"+date.getSeconds();
                            document.getElementById("clock").innerHTML=hours+':'+minutes.substr(-2)+':'+seconds.substr(-2);
                        } catch {}
                    }else{
                        var value=d[device]['s'];
                        var mode=d[device]['m'];
                        var type=d[device]['dt'];
                        var icon=d[device]['ic'];
                        if(name=="Weg"){
                            try{
                                html='<form action="floorplan.php" method="GET"><input type="hidden" name="Weg" value="true">';
                                if(value==0)html+='<input type="image" src="/images/Thuis.png" id="Weg">';
                                else if(value==1)html+='<input type="image" src="/images/Slapen.png" id="Weg">';
                                else if(value==2)html+='<input type="image" src="/images/Weg.png" id="Weg">';
                                html+='</form>';
                                document.getElementById('Weg').innerHTML=html;
                                if(value==0){
                                    document.getElementById("zliving").classList.remove("secured");
                                    document.getElementById("zkeuken").classList.remove("secured");
                                    document.getElementById("zgarage").classList.remove("secured");
                                    document.getElementById("zinkom").classList.remove("secured");
                                    document.getElementById("zhalla").classList.remove("secured");
                                    document.getElementById("zhallb").classList.remove("secured");
                                }else if(value==1){
                                    document.getElementById("zliving").classList.add("secured");
                                    document.getElementById("zkeuken").classList.add("secured");
                                    document.getElementById("zgarage").classList.add("secured");
                                    document.getElementById("zinkom").classList.add("secured");
                                    document.getElementById("zhalla").classList.remove("secured");
                                    document.getElementById("zhallb").classList.remove("secured");
                                }else if(value==2){
                                    document.getElementById("zliving").classList.add("secured");
                                    document.getElementById("zkeuken").classList.add("secured");
                                    document.getElementById("zgarage").classList.add("secured");
                                    document.getElementById("zinkom").classList.add("secured");
                                    document.getElementById("zhalla").classList.add("secured");
                                    document.getElementById("zhallb").classList.add("secured");
                                }
                            } catch {}
                        }else if(name=="minmaxtemp"){
                            try{
                                document.getElementById("mintemp").innerHTML=value.toString().replace(/[.]/, ",");
                                document.getElementById("maxtemp").innerHTML=mode.toString().replace(/[.]/, ",");
                            } catch {}
                        }else if(name=="wind"){
                            try{
                                document.getElementById("wind").innerHTML=value.toString().replace(/[.]/, ",");
                            } catch {}
                        }else if(name=="icon"){
                            try{
                                document.getElementById("hum").innerHTML=mode;
                                $('#icon').attr("src", "https://openweathermap.org/img/w/"+value+".png");
                            } catch {}
                        }else if(name=="uv"){
                            try{
                                if(value<2)html='<font color="#99EE00">UV: '+value+'</font>';
                               else if(value<4)html='<font color="#99CC00">UV: '+value+'</font>';
                               else if(value<6)html='<font color="#FFCC00">UV: '+value+'</font>';
                               else if(value<8)html='<font color="#FF6600">UV: '+value+'</font>';
                                else html='<font color="#FF2200">UV: '+value+'</font>';
                                if(mode<2)html+='<br><font color="#99EE00">max: '+mode+'</font>';
                               else if(mode<4)html+='<br><font color="#99CC00">max: '+mode+'</font>';
                               else if(mode<6)html+='<br><font color="#FFCC00">max: '+mode+'</font>';
                               else if(mode<8)html+='<br><font color="#FF6600">max: '+mode+'</font>';
                                else html+='<br><font color="#FF2200">max: '+mode+'</font>';
                                document.getElementById("uv").innerHTML=html;
                            } catch {}
                        }else if(name=="el"){
                            try{
                                html="<td>Elec:</td><td id='elec'>"+value+" W</td><td id='elecvandaag'>"+mode.toString().replace(/[.]/, ",")+" kWh</td>";
                                document.getElementById("trelec").innerHTML=html;
                                if(value>6000)document.getElementById("elec").style.color="#FF0000";
                                else if(value>5000)document.getElementById("elec").style.color="#FF4400";
                                else if(value>4000)document.getElementById("elec").style.color="#FF8800";
                                else if(value>3000)document.getElementById("elec").style.color="#FFAA00";
                                else if(value>2000)document.getElementById("elec").style.color="#FFCC00";
                                else if(value>1000)document.getElementById("elec").style.color="#FFFF00";
                                if(mode>20)document.getElementById("elecvandaag").style.color="#FF0000";
                                else if(mode>18)document.getElementById("elecvandaag").style.color="#FF4400";
                                else if(mode>16)document.getElementById("elecvandaag").style.color="#FF8800";
                                else if(mode>14)document.getElementById("elecvandaag").style.color="#FFAA00";
                                else if(mode>12)document.getElementById("elecvandaag").style.color="#FFCC00";
                                else if(mode>10)document.getElementById("elecvandaag").style.color="#FFFF00";
                            } catch {}
                        }else if(name=="zon"||name=="zonvandaag"){
                            try{
                                if(d['zon']['s']>0||d['zonvandaag']['s']){
                                    zonvandaag=parseFloat(Math.round(d['zonvandaag']['s']*10)/10).toFixed(1);
                                    html="<td>Zon:</td><td id='zon'>"+d['zon']['s']+" W</td><td id='zonvandaag'>"+zonvandaag.toString().replace(/[.]/, ",")+" kWh</td>";
                                    document.getElementById("trzon").innerHTML=html;
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
                                }
                            } catch {}
                        }else if(name=="gasvandaag"){
                            try{
                                if(value>0){
                                    item=parseFloat(Math.round((value/100)*100)/100).toFixed(3);
                                    html='<td id="tdgas">Gas:</td><td colspan="2">'+item.toString().replace(/[.]/, ",")+' m<sup>3</sup>';
                                    document.getElementById("trgas").innerHTML=html;
                                    if(value>700)document.getElementById("trgas").style.color="#FF0000";
                                    else if(value>600)document.getElementById("trgas").style.color="#FF4400";
                                    else if(value>500)document.getElementById("trgas").style.color="#FF8800";
                                    else if(value>400)document.getElementById("trgas").style.color="#FFAA00";
                                    else if(value>300)document.getElementById("trgas").style.color="#FFCC00";
                                    else if(value>200)document.getElementById("trgas").style.color="#FFFF00";
                                    if(time>$LastUpdateTime-15)document.getElementById("tdgas").style.color="#FF0000";
                                    else if(time>$LastUpdateTime-30)document.getElementById("tdgas").style.color="#FF4400";
                                    else if(time>$LastUpdateTime-60)document.getElementById("tdgas").style.color="#FF8800";
                                    else if(time>$LastUpdateTime-90)document.getElementById("tdgas").style.color="#FFAA00";
                                    else if(time>$LastUpdateTime-300)document.getElementById("tdgas").style.color="#FFCC00";
                                    else if(time>$LastUpdateTime-600)document.getElementById("tdgas").style.color="#FFFF00";
                                }
                            } catch {}
                        }else if(name=="watervandaag"){
                            try{
                                if(value>0){
                                    var item=value / 1000;
                                    html='<td id="tdwater">Water:</td><td colspan="2">'+item.toString().replace(/[.]/, ",")+' m<sup>3</sup>';
                                    document.getElementById("trwater").innerHTML=html;
                                    if(value>1000)document.getElementById("trwater").style.color="#FF0000";
                                    else if(value>750)document.getElementById("trwater").style.color="#FF4400";
                                    else if(value>500)document.getElementById("trwater").style.color="#FF8800";
                                    else if(value>400)document.getElementById("trwater").style.color="#FFAA00";
                                    else if(value>300)document.getElementById("trwater").style.color="#FFCC00";
                                    else if(value>200)document.getElementById("trwater").style.color="#FFFF00";
                                    if(time>$LastUpdateTime-15)document.getElementById("tdwater").style.color="#FF0000";
                                    else if(time>$LastUpdateTime-30)document.getElementById("tdwater").style.color="#FF4400";
                                    else if(time>$LastUpdateTime-60)document.getElementById("tdwater").style.color="#FF8800";
                                    else if(time>$LastUpdateTime-90)document.getElementById("tdwater").style.color="#FFAA00";
                                    else if(time>$LastUpdateTime-300)document.getElementById("tdwater").style.color="#FFCC00";
                                    else if(time>$LastUpdateTime-600)document.getElementById("tdwater").style.color="#FFFF00";
                                }
                            } catch {}
                        }else if(name=="douche"){
                            try{
                                var douchegas=value * 10;
                                var douchewater=mode;
                                var douchegaseuro=parseFloat(Math.round(douchegas * 10 * 0.0004*10)/10).toFixed(2);
                                var douchewatereuro=parseFloat(Math.round(douchewater * 0.005*10)/10).toFixed(2);
                                if(value>0){
                                    html="<td>D-gas:</td><td>"+douchegas+" L</td><td>"+douchegaseuro.toString().replace(/[.]/, ",")+" €</td>";
                                    document.getElementById("trdgas").innerHTML=html;
                                }else{
                                    document.getElementById("trdgas").innerHTML="";
                                }
                                if(mode>0){
                                    html="<td>D-water:</td><td>"+douchewater+" L</td><td>"+douchewatereuro.toString().replace(/[.]/, ",")+" €</td>";
                                    document.getElementById("trdwater").innerHTML=html;
                                }else{
                                    document.getElementById("trdgas").innerHTML="";
                                }
                            } catch {}
                        }else if(name=="heating"){
                            //try{
                               html='<img src="/images/arrowdown.png" class="i60" alt="Open">';
                                if(value==0)html+='';
                                else if(value==1)html+='<img src="/images/Cooling.png" class="i40" alt="Cooling">';
                                else if(value==2)html+='<img src="/images/Elec.png" class="i40" alt="Elec">';
                                else if(value==3){
                                    if(d['brander']['s']=='On')html+='<img src="/images/fire_On.png" class="i40" alt="Gas">';
                                    else html+='<img src="/images/fire_Off.png" class="i40" alt="Gas">';
                                }
                                document.getElementById("heating").innerHTML=html;
                           // } catch {}
                        }else if(name=="belknop"){
                            try{
                                if(time>($LastUpdateTime-82800)){
                                    var date=new Date(time*1000);
                                    var hours=date.getHours();
                                    var minutes="0"+date.getMinutes();
                                    document.getElementById("t"+name).innerHTML=hours+':'+minutes.substr(-2);
                                }else{
                                    document.getElementById("t"+name).innerHTML="";
                                }
                            } catch {}
                        }else if(name=="gcal"){
                            try{
                                if (typeof mode !== 'undefined')document.getElementById("gcal").innerHTML=mode;
                                else document.getElementById("gcal").innerHTML='';
                            } catch {}
                        }else if(type=="switch"){
                            try{
                                if(name=="bureeltobi"||name=="poortrf"){
                                    var html='<form method="POST" action="" id="form"><input type="hidden" name="Naam" value="'+name+'">';
                                    if(value=="On")html+='<input type="hidden" name="Actie" value="Off"><input type="image" src="/images/'+icon+'_On.png" id="'+name+'">';
                                    else if(value=="Off")html+='<input type="hidden" name="Actie" value="On"><input type="image" src="/images/'+icon+'_Off.png" id="'+name+'">';
                                    html+='</form>';
                                    document.getElementById(name).innerHTML=html;
                                }else{
                                    if(value=="On")html='<img src="/images/'+icon+'_On.png" id="'+name+'" onclick="ajaxcontrol(\''+name+'\',\'sw\',\'Off\')"/>';
                                   else if(value=="Off")html='<img src="/images/'+icon+'_Off.png" id="'+name+'" onclick="ajaxcontrol(\''+name+'\',\'sw\',\'On\')""/>';
                                    document.getElementById(name).innerHTML=html;
                                }
                            } catch {}
                        }else if(type=="bose"){
                            try{
                                if(name=="bose105"){
                                    if(mode=="Online"){
                                        var html="Online";
                                        if(value=="On"){var html="<a href='javascript:navigator_Go(\"floorplan.bose.php?ip=105\");'><img src=\"images/bose_On.png\" id=\"bose105\" alt=\"bose\"></a>";}
                                        else{var html="<a href='javascript:navigator_Go(\"floorplan.bose.php?ip=105\");'><img src=\"images/bose_Off.png\" id=\"bose105\" alt=\"bose\"></a>";}
                                    }else if(mode=="Offline"){var html="";}
                                    document.getElementById("bosediv105").innerHTML=html;
                                }
                                if(value=="On"){$('#'+name).attr("src", "/images/bose_On.png");}
                               else if(value=="Off"){$('#'+name).attr("src", "/images/bose_Off.png");}
                            } catch {}
                        }else if(type=="dimmer"){
                            try{
                                if(value==0){
                                    $('#'+name).attr("src", "/images/light_Off.png");
                                    document.getElementById("level"+name).innerHTML="";
                                }else{
                                    $('#'+name).attr("src", "/images/light_On.png");
                                    document.getElementById("level"+name).innerHTML=value;
                                }
                            } catch {}
                        }else if(type=="pir"){
                            try{
                                var name=name.toString().replace("pir", "")
                                var element=document.getElementById("z"+name);
                                if(name=="hall"){
                                    if(value=="On"){
                                        document.getElementById("z"+name+"a").classList.add("motion");
                                        document.getElementById("z"+name+"b").classList.add("motion");
                                    }else{
                                        document.getElementById("z"+name+"a").classList.remove("motion");
                                        document.getElementById("z"+name+"b").classList.remove("motion");
                                    }
                                }else{
                                    if(value=="On"){
                                        element.classList.add("motion");
                                    }else{
                                        element.classList.remove("motion");
                                    }
                                }
                                if(time>($LastUpdateTime-82800)){
                                    var date=new Date(time*1000);
                                    var hours=date.getHours();
                                    var minutes="0"+date.getMinutes();
                                    document.getElementById("tpir"+name).innerHTML=hours+':'+minutes.substr(-2);
                                }else{
                                    document.getElementById("tpir"+name).innerHTML="";
                                }
                            } catch {}
                        }else if(type=="contact"){
                            try{
                                var element=document.getElementById(name);
                                if(value=="Open"){
                                    element.classList.add("red");
                                }else{
                                    element.classList.remove("red");
                                }
                                if(time>($LastUpdateTime-82800)){
                                    var date=new Date(time*1000);
                                    var hours=date.getHours();
                                    var minutes="0"+date.getMinutes();
                                    document.getElementById("t"+name).innerHTML=hours+':'+minutes.substr(-2);
                                }else{
                                    document.getElementById("t"+name).innerHTML="";
                                }
                            } catch {}
                        }else if(type=="thermometer"){
                             try{
                                 if(name=="diepvries_temp"){
                                    var elem=document.getElementById(name);
                                    elem.innerHTML=value.toString().replace(/[.]/, ",")+"°C";
                                    if(value>-15)elem.style.color="#F00";
                                 }else{
                                    hoogte=value * 3;
                                    if(hoogte>88)hoogte=88;
                                    else if(hoogte<20)hoogte=20;
                                    top=91 - hoogte;
                                    if(value >= 22){tcolor="F00";dcolor="55F";}
                                    else if(value >= 20){tcolor="D12";dcolor="44F";}
                                    else if(value >= 18){tcolor="B24";dcolor="33F";}
                                    else if(value >= 15){tcolor="93B";dcolor="22F";}
                                    else if(value >= 10){tcolor="64D";dcolor="11F";}
                                    else{tcolor="55F";dcolor="00F";}
                                    html='<div class="fix tmpbg" style="top:'+top+'px;left:8px;height:'+hoogte+'px;background:linear-gradient(to bottom, #'+tcolor+', #'+dcolor +');">';
                                    html+='</div>'
                                    html+='<img src="/images/temp.png" height="100px" width="auto" alt="'+name+'">';
                                    html+='<div class="fix center" style="top:73px;left:5px;width:30px;">';
                                    html+=value.toString().replace(/[.]/, ",");
                                    html+='</div>';
                                    document.getElementById(name).innerHTML=html;
                                }
                            } catch {}
                        }else if(type=="rollers"){
                            try{
                                var opts=icon.split(",");
                                var stat=100 - value;
                                if(stat<100){var perc=(stat/100)*0.7;}
                                else{var perc=1;}
                                var elem=document.getElementById(name);
                                if(stat==0){
                                    var nsize=0;
                                    elem.classList.remove("yellow");
                                }else if(stat>0){
                                    var nsize=(opts[2]*perc)+8;
                                    if(nsize>opts[2]){nsize=opts[2];}
                                    var top=opts[0]+(opts[2]-nsize);
                                }else{var nsize=opts[2];}
                                if(opts[3]=="P"){
                                    elem.style.top=opts[0]+'px';
                                    elem.style.left=opts[1]+'px';
                                    elem.style.width='7px';
                                    elem.style.height=nsize+'px';
                                }else if(opts[3]=="L"){
                                    elem.style.top=opts[0]+'px';
                                    elem.style.left=opts[1]+'px';
                                    elem.style.width=nsize+'px';
                                    elem.style.height='7px';
                                }

                                var html='<form method="POST" action="">';
                                html+='<input type="hidden" name="rollers" value="'+name+'">';
                                if(value==100){
                                    html+='<input type="image" src="/images/arrowgreendown.png" class="i60">';
                                }else if(value==0){
                                    html+='<input type="image" src="/images/arrowgreenup.png" class="i60">';
                                }else{
                                    html+='<input type="image" src="/images/circlegrey.png" class="i60">';
                                    html+='<div class="fix center dimmerlevel" style="position:absolute;top:17px;left:-2px;width:70px;letter-spacing:4;" onclick="location.href=\'floorplan.heating.php?rollers='+name+'\';">';
                                    if(mode == 2){html+='<font size="5" color="#F00">';}
                                    else if(mode == 1){html+='<font size="5" color="#222">';}
                                    else{html+='<font size="5" color="#CCC">';}
                                    html+=value+'</font></div>';
                                }
                                if(mode == 2){html+='<div class="fix" style="top:2px;left:2px;z-index:-100;background:#fc8000;width:56px;height:56px;border-radius:45px;"></div>';}
                                else if(mode == 1){html+='<div class="fix" style="top:2px;left:2px;z-index:-100;background:#fff7d8;width:56px;height:56px;border-radius:45px;"></div>';}
                                html+='</div></form>';
                                document.getElementById('R'+name).innerHTML=html;

                                if(time>($LastUpdateTime-82800)){
                                    var date=new Date(time*1000);
                                    var hours=date.getHours();
                                    var minutes="0"+date.getMinutes();
                                    document.getElementById("t"+name).innerHTML=hours+':'+minutes.substr(-2);
                                }else{
                                    document.getElementById("t"+name).innerHTML="";
                                }
                            } catch {}
                        }else if(type=="thermostaat"){
                            try{
                                var dif=d[name.toString().replace("_set", "_temp")]['s']-value;
                                var opts=icon.split(",");
                                if(dif>0.2){var circle="hot";}
                                else if(dif<0){var circle="cold";}
                                else{var circle="grey";}
                                if(value>20.5){var center="red";}
                                else if(value>19){var center="orange";}
                                else if(value>14){var center="grey";}
                                else{var center="blue";}
                                var elem=document.getElementById(name);
                                elem.style.top=opts[0]+'px';
                                elem.style.left=opts[1]+'px';
                                var html='<img src="/images/thermo'+circle+center+'.png" class="i48" alt="">';
                                html+='<div class="fix center" style="top:32px;left:11px;width:26px;">';
                                if(mode>0){
                                    html+='<font size="2" color="#222">'+value.toString().replace(/[.]/, ",")+'</font></div>';
                                    html+='<div class="fix" style="top:2px;left:2px;z-index:-100;background:#b08000;width:44px;height:44px;border-radius:45px;"></div>';
                                }else{
                                    html+='<font size="2" color="#CCC">'+value.toString().replace(/[.]/, ",")+'</font></div>';
                                }
                                document.getElementById(name).innerHTML=html;
                            } catch {}
                        }else if(type=="setpoint"){
                            try{
                                document.getElementById(name).innerHTML=value;
                            } catch {}
                        }else{
                            //console.log(type+" -> "+name+" -> "+value+" -> "+time+" -> "+mode);
                        }
                    }
                }
            }
        }
    });
}
function ajaxbose($ip){
    $.ajax({
        url: '/ajaxfloorplan.bose.php?ip='+$ip,
        dataType : 'json',
        success: function(data){
            var date=new Date(data["time"]*1000);
            var hours=date.getHours();
            var minutes="0"+date.getMinutes();
            var seconds="0"+date.getSeconds();
            document.getElementById("clock").innerHTML=hours+':'+minutes.substr(-2)+':'+seconds.substr(-2);
            let volume=parseInt(data["volume"]["actualvolume"], 10);
            if($('#currentvolume').text()!=volume){
                var levels=[-10, -7, -4, -2, -1, 0, 1, 2, 4, 7, 10];
                var html="<br><br>";
                levels.forEach(function(level){
                    let newlevel=volume+level;
                    if(level==0){
                        html+="<button type=\"submit\" name=\"volume\" value=\""+newlevel+"\" class=\"btn volume btna\" id=\"currentvolume\">"+newlevel+"</button>";
                    }else{
                        html+="<button type=\"submit\" name=\"volume\" value=\""+newlevel+"\" class=\"btn volume\">"+newlevel+"</button>";
                    }
                });
                document.getElementById("volume").innerHTML=html;
            }
            let bass=parseInt(data["bass"]["actualbass"], 10);
            if($('#currentbass').text()!=bass){
                var levels=[-9, -8, -7, -6, -5, -4, -3, -2, -1, 0];
                var html="";
                levels.forEach(function(level){
                    if(level==bass){
                        html+="<button type=\"submit\" name=\"bass\" value=\""+level+"\" class=\"btn volume btna\" id=\"currentbass\">"+level+"</button>";
                    }else{
                        html+="<button type=\"submit\" name=\"bass\" value=\""+level+"\" class=\"btn volume\">"+level+"</button>";
                    }
                });
                document.getElementById("bass").innerHTML=html;
            }
            if(data["nowplaying"]["@attributes"]["source"]=="SPOTIFY"){
                document.getElementById("source").innerHTML="Spotify";
                document.getElementById("artist").innerHTML=data["nowplaying"]["artist"];
                document.getElementById("track").innerHTML=data["nowplaying"]["track"];
            }else if(data["nowplaying"]["@attributes"]["source"]=="TUNEIN"){
                document.getElementById("source").innerHTML="Internet Radio";
                document.getElementById("artist").innerHTML=data["nowplaying"]["track"];
                document.getElementById("track").innerHTML=data["nowplaying"]["artist"];
            }else{
                document.getElementById("source").innerHTML=data["nowplaying"]["@attributes"]["source"];
            }
            $('#art').attr("src", data["nowplaying"]["art"].toString().replace("http", "https"));
        }
    })
}
function ajaxcontrol(device,command,action){
    console.log(device,command,action);
    $.ajax({
        url: '/ajaxcontrol.php?device='+device+'&command='+command+'&action='+action,
        dataType : 'json',
        async: true,
        success: function(data){
            console.log(data);
        }
    })
}
function floorplan(){
    document.getElementById("picam1").innerHTML='<a href=\'javascript:navigator_Go("picam1/index.php");\'><img src="/images/Camera.png" class="i48" alt="cam"></a>';
    document.getElementById("picam2").innerHTML='<a href=\'javascript:navigator_Go("picam2/index.php");\'><img src="/images/Camera.png" class="i48" alt="cam"></a>';
    html='<div class="fix kristal z1 i48" id="kristal"></div>';
    html+='<div class="fix bureel z1 i48" id="bureel"></div>';
    html+='<div class="fix inkom z1 i48" id="inkom"></div>';
    html+='<div class="fix keuken z1 i48" id="keuken"></div>';
    html+='<div class="fix wasbak z1 i48" id="wasbak"></div>';
    html+='<div class="fix kookplaat z1 i48" id="kookplaat"></div>';
    html+='<div class="fix voordeur z1 i48" id="voordeur"></div>';
    html+='<div class="fix hall z1 i48" id="hall"></div>';
    html+='<div class="fix werkblad1 z1 i48" id="werkblad1"></div>';
    html+='<div class="fix garage z1 i48" id="garage"></div>';
    html+='<div class="fix garageled z1 i48" id="garageled"></div>';
    html+='<div class="fix zolderg z1 i48" id="zolderg"></div>';
    html+='<div class="fix tuin z1 i48" id="tuin"></div>';
    html+='<div class="fix zolder z1 i48" id="zolder"></div>';
    html+='<div class="fix wc z1 i48" id="wc"></div>';
    html+='<div class="fix bureeltobi z1 i48" id="bureeltobi"></div>';
    html+='<div class="fix tvtobi z1 i48" id="tvtobi"></div>';
    html+='<div class="fix badkamervuur1 z1 i48" id="badkamervuur1"></div>';
    html+='<div class="fix badkamervuur2 z1 i48" id="badkamervuur2"></div>';
    html+='<div class="fix heater1 z1 i48" id="heater1"></div>';
    html+='<div class="fix heater2 z1 i48" id="heater2"></div>';
    html+='<div class="fix heater3 z1 i48" id="heater3"></div>';
    html+='<div class="fix heater4 z1 i48" id="heater4"></div>';
    html+='<div class="fix diepvries z1 i48" id="diepvries"></div>';
    html+='<div class="fix poortrf z1 i48" id="poortrf"></div>';
    html+='<div class="fix jbl z1 i48" id="jbl"></div>';
    html+='<div class="fix yellow" id="Rbureel"></div>';
    html+='<div class="fix yellow" id="RkeukenL"></div>';
    html+='<div class="fix yellow" id="RkeukenR"></div>';
    html+='<div class="fix yellow" id="Rliving"></div>';
    html+='<div class="fix yellow" id="RkamerL"></div>';
    html+='<div class="fix yellow" id="RkamerR"></div>';
    html+='<div class="fix yellow" id="Rtobi"></div>';
    html+='<div class="fix yellow" id="Ralex"></div>';
   document.getElementById("placeholder").innerHTML=html;
}
function floorplanmedia(){
    html='<div class="fix jbl z1 i48" id="jbl"></div>';
    html+='<div class="fix kristal z1 i48" id="kristal"></div>';
    html+='<div class="fix bureel z1 i48" id="bureel"></div>';
    html+='<div class="fix keuken z1 i48" id="keuken"></div>';
    html+='<div class="fix wasbak z1 i48" id="wasbak"></div>';
    html+='<div class="fix kookplaat z1 i48" id="kookplaat"></div>';
    html+='<div class="fix werkblad1 z1 i48" id="werkblad1"></div>';
    html+='<div class="fix kristal z1 i48" id="kristal"></div>';
    html+='<div class="fix yellow" id="Rbureel"></div>';
    html+='<div class="fix yellow" id="RkeukenL"></div>';
    html+='<div class="fix yellow" id="RkeukenR"></div>';
    html+='<div class="fix yellow" id="Rliving"></div>';
    document.getElementById("placeholder").innerHTML=html;
}
function pad(n, length){
    var len=length - (''+n).length;
    return (len>0 ? new Array(++len).join('0') : '')+n
}
function toggle_visibility(id){
    var e=document.getElementById(id);
    if(e.style.display=='inherit') e.style.display='none';
    else e.style.display='inherit';
}