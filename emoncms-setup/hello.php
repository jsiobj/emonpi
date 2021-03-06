<?php global $path; ?>

<style>

.welcome { 
  font-size:32px;
  line-height:52px;
  color:#fff;
}

.welcome2 { 
  font-weight:bold;
  font-size:52px;
  line-height:52px;
  color:#fff;
  padding-bottom:10px;
}

.welcome2 a {
  color:#fff;
}

p {
  color:#fff;
  font-size:18px;
}

.setupbox {
  color:#fff;
  font-size:18px;
  padding:20px;
  border:1px #fff solid;  
  border-bottom:0;
  cursor:pointer;
}

.setupbox:hover {
  background-color:rgba(255,255,255,0.1);
}

.wifinetworks-bound {
  font-size:16px;
  color:#fff;
}

#networks-scanning {
    padding-top:50px;
    padding-bottom:20px;
    height:100px;
    text-align:center;
    background-color:rgba(255,255,255,0.1);
    border:1px #fff solid; 
}

#networks { }

.network-item {
  padding:10px;
  border:1px #fff solid;  
  border-bottom:0;
  cursor:pointer;
}

.network-item:hover {
  background-color:rgba(255,255,255,0.1);
}

#network-authentication {
  padding:10px;
  border:1px #fff solid;  
  text-align:left;
}

.auth-heading {
  font-weight:bold;
  font-size:18px;
  line-height:25px;
}

.auth-message { margin-bottom:10px; }
.auth-showpass { margin-bottom:10px; }
#wifi-password { width:260px }

.iconwifi { 
  width:18px; 
  margin-top:-3px; 
  padding-right:10px; 
}

</style>
<br><br>

<div id="page1">
  <div class="welcome">Welcome to your</div>
  <div class="welcome2"><span style="color:#c8e9f6">emon</span><span>Pi</span></div>
  <p>This is a quick setup wizard to get you started.</p>
  <div style="clear:both; height:20px"></div>

  <div id="setup-step1">
    <p><b>WIFI Configuration:</b> Would you like to:</p>
    <div id="setup-ethernet" class="setupbox hide">Continue on Ethernet</div>
    <div id="setup-standalone" class="setupbox hide">Continue in stand-alone WIFI Access Point mode</div>
    <div id="setup-wificlient" class="setupbox">Connect to WIFI network</div>
  </div>

  <div id="setup-step2" style="display:none">
    <p><b>WIFI Configuration</b></p> 
    <p>Select WIFI network to connect to:</p>
    <div class="wifinetworks-bound">
      <div id="networks-scanning">Scanning for networks<br><br><img src="<?php echo $path; ?>Modules/wifi/icons/ajax-loader.gif" loop=infinite></div>
      <div id="networks"></div>
      <div id="network-authentication" style="display:none">
        <div class="auth-heading">Authentication required</div>
        <div class="auth-message">Passwords or encryption keys are required to access Wi-Fi network:<br><b><span id="WIFI_SSID"></span></b></div>
        Password:<br>
        <input id="wifi-password" type="password" style="height:auto">
        <div class="auth-showpass"><input id="showpass" type="checkbox" style="margin-top:-3px"> Show password</div>
        <button id="auth-cancel" class="btn">Cancel</button> <button id="wifi-connect" class="btn">Connect</button>
      </div>
    </div>
  </div>
</div>

<div id="page2" style="display:none; text-align:center">
  <div class="welcome">WiFi network setting saved. Rebooting system... please wait a couple of minutes before navigating to your emonpi's hostname:</div>
  <div class="welcome2"><a href="http://emonpi.local">http://emonpi.local</a> <span style="color:#c8e9f6">or</span> <a href="http://emonpi">http://emonpi</a></div>
  <br>
  <p>If the hostname does not work on your network, navigate to the IP address shown on the emonPi LCD.</p>
  <p><b>Note:</b> If incorrect password is entered and wifi connection fails, connect emonpi via Ethernet to complete setup</p></p>

</div>

<script>

// Authentication required by network
var path = "<?php echo $path; ?>";
var networks = [];

var ethernet = false;
var wlan0 = false;

$("body").css("background-color","#1d8dbc");
$(".setupbox").last().css("border-bottom","1px solid #fff");

$.ajax({type: 'GET', url: path+"setup/ethernet-status", dataType: 'text', async: true, success: function(result) {
    ethernet = result;
    if (ethernet!="false") $("#setup-ethernet").show();
}});

$.ajax({type: 'GET', url: path+"setup/wlan0-status", dataType: 'text', async: true, success: function(result) {
    wlan0 = result;
    if (wlan0!="false") $("#setup-standalone").show();
}});

wifi_scan();

$("#setup-standalone").click(function(){
    $("#setup-step1").hide();
    $.ajax({type: 'POST', url: path+"setup/setwifi?mode=standalone", dataType: 'text', async: true, success: function(result) {
        window.location = path+"user/login";   
    }});
});

$("#setup-ethernet").click(function(){
    $("#setup-step1").hide();
    $.ajax({type: 'POST', url: path+"setup/setwifi?mode=ethernet", dataType: 'text', async: true, success: function(result) {
        window.location = path+"user/login";   
    }});
});

$("#setup-wificlient").click(function(){
    $("#setup-step1").hide();
    $("#setup-step2").show();
});

function draw_network_list()
{
    var out = "";
    for (var z in networks) {
        var signal = 0;
        if (networks[z]["SIGNAL"]>-100) signal = 1;
        if (networks[z]["SIGNAL"]>-85) signal = 2;
        if (networks[z]["SIGNAL"]>-70) signal = 3;
        if (networks[z]["SIGNAL"]>-60) signal = 4;
        
        var secure = "secure";
        if (networks[z]["SECURITY"]=="ESS") secure = "";
        
        out += "<div class='network-item' ssid='"+z+"'>";
        out += "<img class='iconwifi' src='"+path+"Modules/wifi/icons/wifi"+signal+secure+".png' title='"+networks[z]["SIGNAL"]+"dbm'>";
        out += z;
        out += "</div>";
    }
    $("#networks").html(out);
    $("#networks-scanning").hide();
    $(".network-item").last().css("border-bottom","1px solid #fff");
}

$("#networks").on("click",".network-item",function(){
    var ssid = $(this).attr("ssid");
    $("#networks").hide();
    $("#WIFI_SSID").html(ssid);
    $("#network-authentication").show();
});

$("#auth-cancel").click(function(){
    $("#network-authentication").hide();
    $("#networks").show();
});

$("#showpass").click(function(){
    if ($("#wifi-password").attr("type")=="password") {
        $("#wifi-password").removeAttr("type");
        $("#wifi-password").prop("type","text");
    } else {
        $("#wifi-password").removeAttr("type");
        $("#wifi-password").prop("type","password");
    }
});

$("#wifi-connect").click(function(){
    $("#page1").hide();
    $("#page2").show();
    
    var ssid = $("#WIFI_SSID").html();
    var networks_to_save = {};
    networks[ssid]["PSK"] = $("#wifi-password").val();
    networks[ssid].enabled = true;
    networks_to_save[ssid] = networks[ssid];

    $.ajax({type: 'POST', url: path+"setup/setwifi?mode=client", dataType: 'text', async: true });
    
    $.ajax({type: 'POST', url: path+"wifi/setconfig", data: "networks="+JSON.stringify(networks_to_save), dataType: 'text', async: true });
});

function wifi_scan()
{
    $.ajax({url: path+"wifi/scan", dataType: 'json', async: true,
        success: function(data) {
            for (z in data) {
                if (networks[z]==undefined) networks[z] = {};
                for (key in data[z]) {
                    networks[z][key] = data[z][key];
                }
            }
            draw_network_list();
        }
    });
}

</script>

