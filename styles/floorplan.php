<?php
include "general.php";
$css="
html{padding:0px;margin:0px;color:#ccc;font-family:sans-serif;}
body{margin:0px;background:#000;width:100%}
h2{font-size:36px;}
h3{font-size:24px;}
h4{font-size:18px;}
.center{text-align:center;}
.fix{position:absolute;}
.i20{width:20px;height:auto;}
.i40{width:40px;height:auto;}
.i48{width:48px;height:auto;}
.i60{width:60px;height:auto;}
.i70{width:70px;height:auto;}
.i90{width:90px;height:auto;}
.r0{transform:rotate(0deg);-webkit-transform:rotate(0deg);}
.r90{transform:rotate(90deg);-webkit-transform:rotate(90deg);}
.r270{transform:rotate(270deg);-webkit-transform:rotate(270deg);}
.tmpbg{background-repeat:no-repeat;z-index:-1;left:7px;width:26px;border-radius:8px;}
.z0{z-index:-1;}
.z{z-index:1;}
.z1{z-index:2;}
.z2{z-index:3;}
.fontred{color:#FF0000;}
.fontFF4400e{color:#FF4400;}
.fontFF8800{color:#FF8800;}
.fontFFAA00{color:#FFAA00;}
.fontFFCC00{color:#FFCC00;}
.fontFFFF00{color:#FFFF00;}
.font00FF00{color:#00FF00;}
.font33FF00{color:#33FF00;}
.font66FF00{color:#66FF00;}
.fontCCCCCC{color:#CCCCCC;}
.fontCCCCCC{color:#CCCCCC;}
.fontCCCCCC{color:#CCCCCC;}
.fontCCCCCC{color:#CCCCCC;}
.fontCCCCCC{color:#CCCCCC;}
.fontCCCCCC{color:#CCCCCC;}
.fontCCCCCC{color:#CCCCCC;}
td{text-align:right;font-size:1.1em;}
.blackmedia{top:254px;left:79px;height:585px;width:410px;background-color:#000;}
#mediasidebar{top:80px;left:0px;width:80px;background-color:#000;}
.picam1{top:127px;left:445px;}
.picam2{top:538px;left:210px;}
.volume{width:33px;height:60px;padding:2px 0px 0px 0px!important;margin:0;}
.leftbuttons{top:130px;left:0px;width:80px;}
.mediabuttons{top:288px;left:0px;width:80px;}
.divbrander{top:165px;left:0px;width:420px;border-spacing:0;padding:0;margin-bottom:0px;}
.tablebrander{width:99%;border-spacing:0;padding:0;margin-bottom:0px;}
.tablebrander tr, .brander td{text-align:right;line-height:1.2;font-size:16px;margin-bottom:-30px;}
.divsetpoints{top:644px;left:228px;border-spacing:0;padding:0;}
.tablesetpoints{width:99%;border-spacing:0;padding:0;margin:0;}
.tablesetpoints tr{text-align:center;line-height:1.6;font-size:18px;}
.bottom{position:fixed;bottom:0px;}
.green{background:rgba(50,255,50,0.6);padding:1px 2px 1px 2px;}
.red{background:rgba(255,50,50,0.6);padding:1px 2px 1px 2px;}
.yellow{background:rgba(255,200,0,1);z-index:-10;}
.stamp{width:38px;text-align:center;font-size:100%}
.secured{background:repeating-linear-gradient(135deg,rgba(255,0,0,0),rgba(255,0,0,0) 7px,rgba(255,0,0,0) 8px,rgba(255,0,0,0.6) 15px);z-index:-1000;}
.motion{background:rgba(255,0,0,0.4);z-index:-100;}
.motionr{background:rgba(255,0,0,0.8);z-index:-100;}
.huge2{width:100%;height:48%;margin:1% 0px 1% 0px;font-size:3em;}
.huge3{width:100%;height:31%;margin:1% 0px 1% 0px;font-size:3em;}
.huge4{width:100%;height:24%;margin:1% 0px 1% 0px;font-size:3em;}
#afval{top:370px;left:88px;padding:0;width:317px;font-size:1.7em;textalign:center;}
.huge6{width:100%;height:15.5%;margin:1% 0px 1% 0px;font-size:3em;}
.dimmer{position:fixed;top:0px;left:0px;height:100%;width:100%;background:#000;z-index:100000;}
.dimmerlevel{top:20px;left:0px;width:100%;color:#000;font-size:90%;}
.dimlevel{background-color:#333;color:#eee;font-size:300%;padding:0px;margin-bottom:2px;text-align:center;width:18.5%;height:89px;}
.dimlevela{background-color:#ffba00;color:#000;}
#clock{top:5px;left:266px;width:142px;text-align:center;font-size:33px;font-weight:500;color:#CCC;}
.floorplan{cursor:default;-webkit-touch-callout:none;-webkit-user-select:none;-moz-user-select:none;font-size:80%;font-family:Arial;padding:0px;text-align:center;background:#000;background-image:url(/images/HomeZw.png);background-repeat:no-repeat;background-position:-4px -15px;height:848px;width:486px;padding-top:0px;margin:0 auto;}
.confirm{top:0px;left:0px;height:100%;width:100%;padding:0px;background:#000;z-index:1000;}
.floorplan2icon{top:776px;left:9px;}
.sirene{top:30px;left:200px;}
.sd{top:30px;left:200px;}
.zon{top:586px;left:0px;width:80px;text-align:center;}
.weather{top:542px;left:15px;}

#elec{min-width:60px;}
#gcal{top:378px;left:88px;width:318px;font-size:1.1em;}
#nas{top:120px;left:140px;}
#eettafel{top:140px;left:245px;}
#terras{top:4px;left:16px;}
#ledluifel{top:38px;left:60px;}
#tuin{top:74px;left:16px;height:48px;width:auto;}
#zithoek{top:140px;left:110px;}
#kamer{top:547px;left:330px;}
#tobi{top:432px;left:135px;}
#alex{top:555px;left:135px;}
#lichtbadkamer{top:418px;left:378px;}
#tvled{top:7px;left:87px;height:50px;width:auto;}
#kristal{top:7px;left:147px;height:50px;width:auto;}
#bureel{top:7px;left:207px;height:50px;width:auto;}
#inkom{top:51px;left:349px;height:50px;width:auto;}
#keuken{top:159px;left:390px;height:50px;width:auto;}
#wasbak{top:145px;left:345px;height:40px;width:auto;}
#kookplaat{top:115px;left:386px;height:40px;width:auto;}
#werkblad1{top:208px;left:434px;height:40px;width:auto;}
#voordeur{top:59px;left:442px;height:42px;width:auto;}
#hall{top:410px;left:252px;height:50px;width:auto;}
#garage{top:305px;left:190px;height:60px;width:auto;}
#garageled{top:315px;left:268px;height:42px;width:auto;}
#zolderg{top:315px;left:130px;height:42px;width:auto;}
#auto{top:312px;left:299px;height:40px;width:auto;}
#Weg{top:286px;left:410px;height:80px;width:auto;}
#zolder{top:690px;left:210px;height:48px;width:48px;}
#wc{top:11px;left:412px;height:28px;width:auto;}
#regenput{top:500px;left:18px;}
#bureeltobi{top:798px;left:375px;height:28px;width:auto;}
#tvtobi{top:798px;left:152px;height:28px;width:auto;}
#bureeltobikwh{top:811px;left:420px;text-align:left;min-width:60px;}
#tvtobikwh{top:811px;left:89px;text-align:right;min-width:60px;}
#badkamervuur1{top:403px;left:346px;height:24px;width:auto;}
#badkamervuur2{top:403px;left:360px;height:24px;width:auto;}
#jbl{top:218px;left:267px;height:30px;width:auto;}
#bose101{top:203px;left:185px;height:auto;width:60px;}
#bose102{top:472px;left:344px;height:auto;width:50px;}
#bose103{top:605px;left:280px;height:auto;width:50px;}
#bose104{top:255px;left:100px;height:auto;width:50px;}
#bose105{top:228px;left:8px;height:auto;width:auto;}
#GroheRed{top:186px;left:342px;height:16px;width:auto;}
#Usage_grohered{top:186px;left:342px;height:16px;width:auto;}
#poortrf{top:305px;left:318px;height:60px;width:auto;}
#diepvries{top:256px;left:175px;height:16px;width:auto;}
#auto{top:280px;left:235px;height:48px;width:auto;}
#meldingen{top:280px;left:350px;height:48px;width:auto;}
#verbruik{top:652px;left:290px;width:200px;height:48px;width:auto;}
#zoldervuur{top:670px;left:140px;height:48px;width:auto;}
#zoldervuur2{top:783px;left:455px;height:48px;width:auto;}
#water{top:100px;left:0px;height:48px;width:auto;}
#regenpomp{top:180px;left:0px;height:48px;width:auto;}
#zwembadfilter{top:260px;left:0px;height:48px;width:auto;}
#zwembadwarmte{top:340px;left:0px;height:48px;width:auto;}
#dampkap{top:280px;left:110px;height:48px;width:auto;}
#heater1{top:226px;left:90px;height:18px;width:auto;}
#heater2{top:223px;left:106px;height:22px;width:auto;}
#heater3{top:223px;left:123px;height:22px;width:auto;}
#heater4{top:223px;left:140px;height:22px;width:auto;}

#tbelknop{top:17px;left:466px;}
#tpirgarage{top:256px;left:300px;}
#tpirliving{top:230px;left:300px;}
#tpirlivingR{top:230px;left:105px;}
#tpirkeuken{top:115px;left:345px;}
#tpirinkom{top:89px;left:398px;}
#tpirhall{top:403px;left:215px;}

#buiten_temp{top:450px;left:20px;}
#floorplanstats{top:810px;left:80px;width:410px;}

/* Media */
#denon{top:77px;left:87px;}
#tv{top:70px;left:168px;}
#lgtv{top:70px;left:168px;}
#nvidia{top:70px;left:219px;}
#bosesoundlink{top:70px;left:280px;height:48px;width:auto;}
#nas{top:70px;left:280px;}

/* Deuren */
.red{background:rgba(255,0,0,1);}

#achterdeur{top:264px;left:81px;width:30px;height:48px;z-index:-10;}#tachterdeur{top:280px;left:70px;transform:rotate(270deg);-webkit-transform:rotate(270deg);}
#deurbadkamer{top:421px;left:341px;width:7px;height:46px;z-index:-10;}#tdeurbadkamer{top:435px;left:329px;transform:rotate(90deg);-webkit-transform:rotate(90deg);}



#poort{top:261px;left:404px;width:25px;height:128px;z-index:-10;}#tpoort{top:315px;left:376px;transform:rotate(90deg);-webkit-transform:rotate(90deg);}
#deurinkom{top:56px;left:338px;width:7px;height:46px;z-index:-10;}#tdeurinkom{top:70px;left:324px;transform:rotate(90deg);-webkit-transform:rotate(90deg);}
#deurgarage{top:248px;left:341px;width:46px;height:8px;z-index:-10;}#tdeurgarage{top:244px;left:344px;}
#deurkamer{top:468px;left:290px;width:42px;height:7px;z-index:-10;}#tdeurkamer{top:464px;left:293px;}
#deurtobi{top:449px;left:207px;width:4px;height:43px;z-index:-10;}#tdeurtobi{top:463px;left:194px;transform:rotate(270deg);-webkit-transform:rotate(270deg);}
#deuralex{top:534px;left:214px;width:41px;height:7px;z-index:-10;}#tdeuralex{top:530px;left:217px;}

/* Ramen */
#traamliving{top:72px;left:75px;transform:rotate(270deg);-webkit-transform:rotate(270deg);}
#traamtobi{top:462px;left:75px;transform:rotate(270deg);-webkit-transform:rotate(270deg);}
#traamalex{top:581px;left:75px;transform:rotate(270deg);-webkit-transform:rotate(270deg);}
#traamkamer{top:598px;left:455px;transform:rotate(90deg);-webkit-transform:rotate(90deg);}
#raamliving{top:46px;left:87px;width:11px;height:163px;z-index:-10;}
#raamtobi{top:448px;left:87px;width:11px;height:42px;z-index:-10;}
#raamalex{top:568px;left:87px;width:11px;height:42px;z-index:-10;}
#raamkamer{top:586px;left:467px;width:11px;height:42px;z-index:-10;}
#raamhall{top:403px;left:214px;width:50px;height:12px;z-index:-10;}

/* Thermometers */
#living_temp{top:95px;left:193px;}
#badkamer_temp{top:403px;left:441px;}
#kamer_temp{top:530px;left:427px;}
#tobi_temp{top:433px;left:94px;}
#alex_temp{top:544px;left:94px;}
#zolder_temp{top:670px;left:94px;}
#diepvries_temp{top:256px;left:191px;}

/* Rollers */
#luifel{top:140px;left:5px;}#tluifel{top:140px;left:5px;}
#RRliving{top:97px;left:90px;}#tRliving{top:217px;left:68px;transform:rotate(270deg);-webkit-transform:rotate(270deg);}
#RRbureel{top:5px;left:195px;}#tRbureel{top:-2px;left:170px;}
#RRkeukenL{top:116px;left:405px;}#tRkeukenL{top:102px;left:456px;transform:rotate(90deg);-webkit-transform:rotate(90deg);}
#RRkeukenR{top:186px;left:405px;}#tRkeukenR{top:229px;left:456px;transform:rotate(90deg);-webkit-transform:rotate(90deg);}
#RRtobi{top:404px;left:137px;}#tRtobi{top:510px;left:68px;transform:rotate(270deg);-webkit-transform:rotate(270deg);}
#RRalex{top:560px;left:137px;}#tRalex{top:620px;left:68px;transform:rotate(270deg);-webkit-transform:rotate(270deg);}
#RRkamerL{top:512px;left:350px;}#tRkamerL{top:504px;left:462px;transform:rotate(90deg);-webkit-transform:rotate(90deg);}
#RRkamerR{top:580px;left:350px;}#tRkamerR{top:638px;left:462px;transform:rotate(90deg);-webkit-transform:rotate(90deg);}
#zoldertrap{top:670px;left:145px;}#tzoldertrap{top:675px;left:170px;}

/* Zones */
#zliving{top:51px;left:89px;width:249px;height:197px;}
#zkeuken{top:115px;left:345px;width:129px;height:133px;}
#zinkom{top:51px;left:345px;width:129px;height:56px;}
#zgarage{top:256px;left:89px;width:315px;height:139px;}
#zhalla{top:403px;left:214px;width:127px;height:66px;}
#zhallb{top:469px;left:214px;width:44px;height:66px;}

/* Setpoints */
#kamerZ{top:523px;left:455px;text-align:left;transform:rotate(90deg);-webkit-transform:rotate(90deg);}
#tobiZ{top:415px;left:76px;text-align:right;transform:rotate(270deg);-webkit-transform:rotate(270deg);}
#alexZ{top:555px;left:76px;text-align:right;transform:rotate(270deg);-webkit-transform:rotate(270deg);}
";
$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
$css = str_replace(': ', ':', $css);
$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
echo($css);