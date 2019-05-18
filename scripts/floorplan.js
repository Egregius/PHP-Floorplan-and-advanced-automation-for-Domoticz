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
                    var value = data[device]['s'];
                    var time = data[device]['t'];
                    var mode = data[device]['m'];
                    var type = data[device]['dt'];
                    if (name=="time") {
                        $LastUpdateTime = parseInt(time);
                        try {
                            var date = new Date(time*1000);
                            var hours = date.getHours();
                            var minutes = "0" + date.getMinutes();
                            var seconds = "0" + date.getSeconds();
                            document.getElementById("clock").innerHTML = hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
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
                    } else if (type=="light") {
                        try {
                            if (value=="On") {
                                $('#' + name).attr("src", "/images/light_On.png");
                                $('#action' + name).val("Off");
                            } else if (value=="Off") {
                                $('#' + name).attr("src", "/images/light_Off.png");
                                $('#action' + name).val("On");
                            }
                        } catch {}
                    } else if (type=="plug") {
                        try {
                            if (value=="On") {
                                $('#' + name).attr("src", "/images/plug_On.png");
                                $('#action' + name).val("Off");
                            } else if (value=="Off") {
                                $('#' + name).attr("src", "/images/plug_Off.png");
                                $('#action' + name).val("On");
                            }
                        } catch {}
                    } else if (type=="fire") {
                        try {
                            if (value=="On") {
                                $('#' + name).attr("src", "/images/fire_On.png");
                                $('#action' + name).val("Off");
                            } else if (value=="Off") {
                                $('#' + name).attr("src", "/images/fire_Off.png");
                                $('#action' + name).val("On");
                            }
                        } catch {}
                    } else if (type=="fan") {
                        try {
                            if (value=="On") {
                                $('#' + name).attr("src", "/images/fan_On.png");
                                $('#action' + name).val("Off");
                            } else if (value=="Off") {
                                $('#' + name).attr("src", "/images/fan_Off.png");
                                $('#action' + name).val("On");
                            }
                        } catch {}
                    } else if (type=="alarm") {
                        try {
                            if (value=="On") {
                                $('#' + name).attr("src", "/images/alarm_On.png");
                                $('#action' + name).val("Off");
                            } else if (value=="Off") {
                                $('#' + name).attr("src", "/images/alarm_Off.png");
                                $('#action' + name).val("On");
                            }
                        } catch {}
                    } else if (type=="bose") {
                        try {
                            if (name=="bose105") {
                                var html = "watisdathier";
                                if (mode=="Online") {
                                    var html = "Online";
                                    if (value=="On") {
                                        var html = "<a href='javascript:navigator_Go(\"floorplan.bose.php?ip=105\");'><img src=\"images/Bose_On.png\" id=\"bose105\" alt=\"bose\"></a>";
                                    } else {
                                        var html = "<a href='javascript:navigator_Go(\"floorplan.bose.php?ip=105\");'><img src=\"images/Bose_Off.png\" id=\"bose105\" alt=\"bose\"></a>";
                                    }
                                } else if (mode=="Offline") {
                                    var html = "";
                                }
                                document.getElementById("bosediv105").innerHTML = html;
                            }
                            console.log(type + " -> " + name + " -> " + value + " -> " + time + " -> " + mode);
                            if (value=="On") {
                                $('#' + name).attr("src", "/images/Bose_On.png");
                            } else if (value=="Off") {
                                $('#' + name).attr("src", "/images/Bose_Off.png");
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
                            var element = document.getElementById(name);
                            if (value=="On") {
                                element.classList.add("motion");
                            } else {
                                element.classList.remove("motion");
                            }
                            var date = new Date(time*1000);
                            var hours = date.getHours();
                            var minutes = "0" + date.getMinutes();
                            document.getElementById("t" + name).innerHTML = hours + ':' + minutes.substr(-2);
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
                                document.getElementById(name).innerHTML = value.toString().replace(/[.]/, ",");
                            }
                        } catch {}
                    } else if (type=="thermostaat") {
                        try {
                            document.getElementById(name).innerHTML = value.toString().replace(/[.]/, ",");
                        } catch {}
                    } else if (type=="setpoint") {
                        try {
                            document.getElementById(name).innerHTML = value;
                        } catch {}
                    } else if (type=="rollers") {
                            /*setTimeout(ajaxinit, 1000);*/
                    } else {
                        console.log(type + " -> " + name + " -> " + value + " -> " + time + " -> " + mode);
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