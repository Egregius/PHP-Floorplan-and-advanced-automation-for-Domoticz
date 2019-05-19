function navigator_Go(url) {window.location.assign(url);}
var $LastUpdateTime = parseInt(0);
function ajax() {
    $.ajax({
        url: '/ajax.php?timestamp='+$LastUpdateTime,
        dataType : 'json',
        success: function(data) {
            for (var device in data) {
                if (data.hasOwnProperty(device)) {
                    var name = data[device]['n'];
                    var time = data[device]['t'];
                    if (name=="time") {
                        $LastUpdateTime = parseInt(time);
                        try {
                            var date = new Date(time*1000);
                            var hours = date.getHours();
                            var minutes = "0" + date.getMinutes();
                            var seconds = "0" + date.getSeconds();
                            document.getElementById("clock").innerHTML = hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
                        } catch {}
                    } else {
                        var value = data[device]['s'];
                        var mode = data[device]['m'];
                        var type = data[device]['dt'];
                        var icon = data[device]['icon'];
                        if (name=="Weg") {
                            try {
                                if (value==0) {
                                    document.getElementById("zliving").classList.remove("secured");
                                    document.getElementById("zkeuken").classList.remove("secured");
                                    document.getElementById("zgarage").classList.remove("secured");
                                    document.getElementById("zinkom").classList.remove("secured");
                                    document.getElementById("zhalla").classList.remove("secured");
                                    document.getElementById("zhallb").classList.remove("secured");
                                } else if (value==1) {
                                    document.getElementById("zliving").classList.add("secured");
                                    document.getElementById("zkeuken").classList.add("secured");
                                    document.getElementById("zgarage").classList.add("secured");
                                    document.getElementById("zinkom").classList.add("secured");
                                    document.getElementById("zhalla").classList.remove("secured");
                                    document.getElementById("zhallb").classList.remove("secured");
                                } else if (value==2) {
                                    document.getElementById("zliving").classList.add("secured");
                                    document.getElementById("zkeuken").classList.add("secured");
                                    document.getElementById("zgarage").classList.add("secured");
                                    document.getElementById("zinkom").classList.add("secured");
                                    document.getElementById("zhalla").classList.add("secured");
                                    document.getElementById("zhallb").classList.add("secured");
                                }
                            } catch {}
                        } else if (name=="minmaxtemp") {
                            try {
                                document.getElementById("mintemp").innerHTML = value.toString().replace(/[.]/, ",");
                                document.getElementById("maxtemp").innerHTML = mode.toString().replace(/[.]/, ",");
                            } catch {}
                        } else if (name=="wind") {
                            try {
                                document.getElementById("wind").innerHTML = value.toString().replace(/[.]/, ",");
                            } catch {}
                        } else if (name=="icon") {
                            try {
                                document.getElementById("hum").innerHTML = mode;
                                $('#icon').attr("src", "https://openweathermap.org/img/w/" + value + ".png");
                            } catch {}
                        } else if (name=="uv") {
                            try {
                                document.getElementById("uv").innerHTML = value;
                                document.getElementById("uvmax").innerHTML = mode;
                            } catch {}
                        } else if (name=="elec"){
                            try {
                                document.getElementById("elec").innerHTML = value + " W";
                                document.getElementById("elecvandaag").innerHTML = mode.toString().replace(/[.]/, ",") + " kWh";
                            } catch {}
                        } else if (name=="zon"){
                            try {
                                document.getElementById(name).innerHTML = value + " W";
                            } catch {}
                        } else if (name=="zonvandaag"){
                            try {
                                document.getElementById(name).innerHTML = value + " kWh";
                            } catch {}
                        } else if (name=="gasvandaag"){
                            try {
                                var item = pad(value / 100, 4);
                                document.getElementById(name).innerHTML = item.toString().replace(/[.]/, ",") + " m<sup>3</sup>";
                            } catch {}
                        } else if (name=="watervandaag"){
                            try {
                                var item = value / 1000;
                                document.getElementById(name).innerHTML = item.toString().replace(/[.]/, ",") + " m<sup>3</sup>";
                            } catch {}
                        } else if (name=="douche"){
                            try {
                                var douchegas = value * 10;
                                var douchewater = mode;
                                var douchegaseuro = douchegas * 10 * 0.0004;
                                var douchewatereuro = douchewatereuro * 0.005;
                                document.getElementById('douchegas').innerHTML = douchegas + " L";
                                document.getElementById('douchegaseuro').innerHTML = douchegaseuro + " L";
                                document.getElementById('douchewater').innerHTML = douchewater + " L";
                                document.getElementById('douchewatereuro').innerHTML = douchewatereuro + " L";
                            } catch {}
                        } else if (type=="switch") {
                            try {
                                var html = '<form method="POST" action="" id="form">';
                                html += '<input type="hidden" name="Naam" value="' + name + '">';
                                if (value=="On") {
                                    html += '<input type="hidden" name="Actie" value="Off">';
                                    html += '<input type="image" src="/images/' + icon + '_On.png" id="' + name + '">';
                                } else if (value=="Off") {
                                    html += '<input type="hidden" name="Actie" value="On">';
                                    html += '<input type="image" src="/images/' + icon + '_Off.png" id="' + name + '">';
                                }
                                html += '</form>';
                                document.getElementById(name).innerHTML = html;
                            } catch {}
                        } else if (type=="bose") {
                            try {
                                if (name=="bose105") {
                                    if (mode=="Online") {
                                        var html = "Online";
                                        if (value=="On") {
                                            var html = "<a href='javascript:navigator_Go(\"floorplan.bose.php?ip=105\");'><img src=\"images/bose_On.png\" id=\"bose105\" alt=\"bose\"></a>";
                                        } else {
                                            var html = "<a href='javascript:navigator_Go(\"floorplan.bose.php?ip=105\");'><img src=\"images/bose_Off.png\" id=\"bose105\" alt=\"bose\"></a>";
                                        }
                                    } else if (mode=="Offline") {
                                        var html = "";
                                    }
                                    document.getElementById("bosediv105").innerHTML = html;
                                }
                                if (value=="On") {
                                    $('#' + name).attr("src", "/images/bose_On.png");
                                } else if (value=="Off") {
                                    $('#' + name).attr("src", "/images/bose_Off.png");
                                }
                            } catch {}
                        } else if (type=="dimmer") {
                            try {
                                if (value==0) {
                                    $('#' + name).attr("src", "/images/light_Off.png");
                                    document.getElementById("level" + name).innerHTML = "";
                                } else {
                                    $('#' + name).attr("src", "/images/light_On.png");
                                    document.getElementById("level" + name).innerHTML = value;
                                }
                            } catch {}
                        } else if (type=="pir") {
                            try {
                                var name = name.toString().replace("pir", "")
                                var element = document.getElementById("z" + name);
                                if (name=="hall") {
                                    if (value=="On") {
                                        document.getElementById("z" + name + "a").classList.add("motion");
                                        document.getElementById("z" + name + "b").classList.add("motion");
                                    } else {
                                        document.getElementById("z" + name + "a").classList.remove("motion");
                                        document.getElementById("z" + name + "b").classList.remove("motion");
                                    }
                                } else {
                                    if (value=="On") {
                                        element.classList.add("motion");
                                    } else {
                                        element.classList.remove("motion");
                                    }
                                }
                                var date = new Date(time*1000);
                                var hours = date.getHours();
                                var minutes = "0" + date.getMinutes();
                                document.getElementById("tpir" + name).innerHTML = hours + ':' + minutes.substr(-2);
                            } catch {}
                        } else if (type=="contact") {
                            try {
                                var element = document.getElementById(name);
                                if (value=="Open") {
                                    element.classList.add("red");
                                } else {
                                    element.classList.remove("red");
                                }
                                var date = new Date(time*1000);
                                var hours = date.getHours();
                                var minutes = "0" + date.getMinutes();
                                document.getElementById("t" + name).innerHTML = hours + ':' + minutes.substr(-2);
                            } catch {}
                        } else if (type=="thermometer") {
                             try {
                                 if (name=="diepvries_temp") {
                                    document.getElementById(name).innerHTML = value.toString().replace(/[.]/, ",") + "°C";
                                 } else {
                                    var hoogte = value * 3;
                                    if (hoogte > 88) {
                                        hoogte = 88;
                                    } else if (hoogte < 20) {
                                        hoogte = 20;
                                    }
                                    var top = 91 - hoogte;
                                    if (value >= 22) {
                                        var tcolor = "F00";
                                        var dcolor = "55F";
                                    } else if (value >= 20) {
                                        var tcolor = "D12";
                                        var dcolor = "44F";
                                    } else if (value >= 18) {
                                        var tcolor = "B24";
                                        var dcolor = "33F";
                                    } else if (value >= 15) {
                                        var tcolor = "93B";
                                        var dcolor = "22F";
                                    } else if (value >= 10) {
                                        var tcolor = "64D";
                                        var dcolor = "11F";
                                    } else {
                                        var tcolor = "55F";
                                        var dcolor = "00F";
                                    }
                                    var html = '<div class="fix tmpbg" style="top:' + top + 'px;left:8px;height:' + hoogte + 'px;background:linear-gradient(to bottom, #' + tcolor + ', #' + dcolor +');">';
                                    html += '</div>'
                                    html += '<img src="/images/temp.png" height="100px" width="auto" alt="' + name + '">';
                                    html += '<div class="fix center" style="top:73px;left:5px;width:30px;">';
                                    html += value.toString().replace(/[.]/, ",");
                                    html += '</div>';
                                    document.getElementById(name).innerHTML = html;
                                }
                            } catch {}
                        } else if (type=="rollers") {
                            try {
                                /*
                                opts[0] = top
                                opts[1] = left
                                opts[2] = size
                                opts[3] = rotation
                                */
                                var opts = icon.split(",");
                                var stat = 100 - value;
                                if (stat < 100) {
                                    var perc = (stat/100)*0.7;
                                } else {
                                    var perc = 1;
                                }
                                var elem = document.getElementById(name);
                                if (stat==0) {
                                    var nsize = 0;
                                    elem.classList.remove("yellow");
                                } else if (stat > 0) {
                                    var nsize = (opts[2]*perc)+8;
                                    if (nsize > opts[2]) {
                                        nsize = opts[2];
                                    }
                                    var top = opts[0] + (opts[2]-nsize);
                                } else {
                                    var nsize = opts[2];
                                }
                                if (opts[3]=="P") {
                                    elem.style.top = opts[0]+'px';
                                    elem.style.left = opts[1]+'px';
                                    elem.style.width = '7px';
                                    elem.style.height = nsize+'px';
                                } else if (opts[3]=="L") {
                                    elem.style.top = opts[0]+'px';
                                    elem.style.left = opts[1]+'px';
                                    elem.style.width = nsize+'px';
                                    elem.style.height = '7px';
                                }
                            } catch {}
                        } else if (type=="thermostaat") {
                                var dif=data[name.toString().replace("_set", "_temp")]['s']-value;
                                var opts = icon.split(",");
                                if (dif > 0.2) {
                                    var circle = "hot";
                                } else if (dif < 0) {
                                    var circle = "cold";
                                } else {
                                    var circle = "grey";
                                }
                                if (value > 20.5) {
                                    var center = "red";
                                } else if (value > 19) {
                                    var center = "orange";
                                } else if (value > 14) {
                                    var center = "grey";
                                } else {
                                    var center = "blue";
                                }
                                var elem = document.getElementById(name);
                                elem.style.top = opts[0]+'px';
                                elem.style.left = opts[1]+'px';
                                var html = '<img src="/images/thermo' + circle + center + '.png" class="i48" alt="">';
                                html += '<div class="fix center" style="top:32px;left:11px;width:26px;">';
                                if (mode > 0) {
                                    html += '<font size="2" color="#222">' + value.toString().replace(/[.]/, ",") + '</font></div>';
                                    html += '<div class="fix" style="top:2px;left:2px;z-index:-100;background:#b08000;width:44px;height:44px;border-radius:45px;"></div>';
                                } else {
                                    html += '<font size="2" color="#CCC">' + value.toString().replace(/[.]/, ",") + '</font></div>';
                                }

                                console.log('thermostaat' + name);
                                console.log(html);
                                document.getElementById(name).innerHTML = html;
                        } else if (type=="setpoint") {
                            try {
                                document.getElementById(name).innerHTML = value;
                            } catch {}
                        } else if (type=="rollers") {
                                /*setTimeout(ajaxinit, 1000);*/
                        } else {
                            /*console.log(type + " -> " + name + " -> " + value + " -> " + time + " -> " + mode);*/
                        }
                    }
                }
            }
        },
    });
}
function ajaxbose($ip) {
    $.ajax({
        url: '/ajaxfloorplan.bose.php?ip=' + $ip,
        dataType : 'json',
        success: function(data) {
            var date = new Date(data["time"]*1000);
            var hours = date.getHours();
            var minutes = "0" + date.getMinutes();
            var seconds = "0" + date.getSeconds();
            document.getElementById("clock").innerHTML = hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
            let volume = parseInt(data["volume"]["actualvolume"], 10);
            if ($('#currentvolume').text()!=volume) {
                var levels = [-10, -7, -4, -2, -1, 0, 1, 2, 4, 7, 10];
                var html = "<br><br>";
                levels.forEach(function(level) {
                    let newlevel = volume + level;
                    if (level==0) {
                        html += "<button type=\"submit\" name=\"volume\" value=\"" + newlevel + "\" class=\"btn volume btna\" id=\"currentvolume\">" + newlevel + "</button>";
                    } else {
                        html += "<button type=\"submit\" name=\"volume\" value=\"" + newlevel + "\" class=\"btn volume\">" + newlevel + "</button>";
                    }
                });
                document.getElementById("volume").innerHTML = html;
            }
            let bass = parseInt(data["bass"]["actualbass"], 10);
            if ($('#currentbass').text()!=bass) {
                var levels = [-9, -8, -7, -6, -5, -4, -3, -2, -1, 0];
                var html = "";
                levels.forEach(function(level) {
                    if (level==bass) {
                        html += "<button type=\"submit\" name=\"bass\" value=\"" + level + "\" class=\"btn volume btna\" id=\"currentbass\">" + level + "</button>";
                    } else {
                        html += "<button type=\"submit\" name=\"bass\" value=\"" + level + "\" class=\"btn volume\">" + level + "</button>";
                    }
                });
                document.getElementById("bass").innerHTML = html;
            }
            if (data["nowplaying"]["@attributes"]["source"]=="SPOTIFY") {
                document.getElementById("source").innerHTML = "Spotify";
                document.getElementById("artist").innerHTML = data["nowplaying"]["artist"];
                document.getElementById("track").innerHTML = data["nowplaying"]["track"];
            } else if (data["nowplaying"]["@attributes"]["source"]=="TUNEIN") {
                document.getElementById("source").innerHTML = "Internet Radio";
                document.getElementById("artist").innerHTML = data["nowplaying"]["track"];
                document.getElementById("track").innerHTML = data["nowplaying"]["artist"];
            } else {
                document.getElementById("source").innerHTML = data["nowplaying"]["@attributes"]["source"];
            }
            $('#art').attr("src", data["nowplaying"]["art"].toString().replace("http", "https"));
        }
    })
}
function pad(n, length) {
    var len = length - (''+n).length;
    return (len > 0 ? new Array(++len).join('0') : '') + n
}