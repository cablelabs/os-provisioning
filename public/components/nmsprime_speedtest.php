<script type="text/javascript" src="speedtest.js"></script>
<script type="text/javascript">
function I(i){return document.getElementById(i);}
//INITIALIZE SPEEDTEST
var s=new Speedtest(); //create speedtest object
s.setParameter("url_dl","customer/speedtest/garbage")
s.setParameter("url_ul","backend/empty.php")
s.setParameter("url_ping","backend/empty.php")
s.setParameter("telemetry_level","basic"); //enable telemetry

var meterBk=/Trident.*rv:(\d+\.\d+)/i.test(navigator.userAgent)?"#EAEAEA":"#80808040";
var dlColor="#6060AA",
  ulColor="#309030",
  pingColor="#AA6060",
  jitColor="#AA6060";
var progColor=meterBk;

//CODE FOR GAUGES
function drawMeter(c,amount,bk,fg,progress,prog){
  var ctx=c.getContext("2d");
  var dp=window.devicePixelRatio||1;
  var cw=c.clientWidth*dp, ch=c.clientHeight*dp;
  var sizScale=ch*0.0055;
  if(c.width==cw&&c.height==ch){
    ctx.clearRect(0,0,cw,ch);
  }else{
    c.width=cw;
    c.height=ch;
  }
  ctx.beginPath();
  ctx.strokeStyle=bk;
  ctx.lineWidth=12*sizScale;
  ctx.arc(c.width/2,c.height-58*sizScale,c.height/1.8-ctx.lineWidth,-Math.PI*1.1,Math.PI*0.1);
  ctx.stroke();
  ctx.beginPath();
  ctx.strokeStyle=fg;
  ctx.lineWidth=12*sizScale;
  ctx.arc(c.width/2,c.height-58*sizScale,c.height/1.8-ctx.lineWidth,-Math.PI*1.1,amount*Math.PI*1.2-Math.PI*1.1);
  ctx.stroke();
  if(typeof progress !== "undefined"){
    ctx.fillStyle=prog;
    ctx.fillRect(c.width*0.3,c.height-16*sizScale,c.width*0.4*progress,4*sizScale);
  }
}
function mbpsToAmount(s){
  return 1-(1/(Math.pow(1.3,Math.sqrt(s))));
}
function msToAmount(s){
  return 1-(1/(Math.pow(1.08,Math.sqrt(s))));
}
//UI CODE
var uiData=null;
function startStop(){
    if(s.getState()==3){
    //speedtest is running, abort
    s.abort();
    data=null;
    I("startStopBtn").className="";
    initUI();
  }else{
    //test is not running, begin
    I("startStopBtn").className="running";
    I("shareArea").style.display="none";
    s.onupdate=function(data){
            uiData=data;
    };
    s.onend=function(aborted){
            I("startStopBtn").className="";
            updateUI(true);
            if(!aborted){
                //if testId is present, show sharing panel, otherwise do nothing
                try{
                    var testId=uiData.testId;
                    if(testId!=null){
                        var shareURL=window.location.href.substring(0,window.location.href.lastIndexOf("/"))+"/results/?id="+testId;
                        I("resultsImg").src=shareURL;
                        I("resultsURL").value=shareURL;
                        I("testId").innerHTML=testId;
                        I("shareArea").style.display="";
                    }
                }catch(e){}
            }
    };
    s.start();
  }
}
//this function reads the data sent back by the test and updates the UI
function updateUI(forced){
  if(!forced&&s.getState()!=3) return;
  if(uiData==null) return;
  var status=uiData.testState;
  I("ip").textContent=uiData.clientIp;
  I("dlText").textContent=(status==1&&uiData.dlStatus==0)?"...":uiData.dlStatus;
  drawMeter(I("dlMeter"),mbpsToAmount(Number(uiData.dlStatus*(status==1?oscillate():1))),meterBk,dlColor,Number(uiData.dlProgress),progColor);
  I("ulText").textContent=(status==3&&uiData.ulStatus==0)?"...":uiData.ulStatus;
  drawMeter(I("ulMeter"),mbpsToAmount(Number(uiData.ulStatus*(status==3?oscillate():1))),meterBk,ulColor,Number(uiData.ulProgress),progColor);
  I("pingText").textContent=uiData.pingStatus;
  drawMeter(I("pingMeter"),msToAmount(Number(uiData.pingStatus*(status==2?oscillate():1))),meterBk,pingColor,Number(uiData.pingProgress),progColor);
  I("jitText").textContent=uiData.jitterStatus;
  drawMeter(I("jitMeter"),msToAmount(Number(uiData.jitterStatus*(status==2?oscillate():1))),meterBk,jitColor,Number(uiData.pingProgress),progColor);
}
function oscillate(){
  return 1+0.02*Math.sin(Date.now()/100);
}
//update the UI every frame
window.requestAnimationFrame=window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||window.msRequestAnimationFrame||(function(callback,element){setTimeout(callback,1000/60);});
function frame(){
  requestAnimationFrame(frame);
  updateUI();
}
frame(); //start frame loop
//function to (re)initialize UI
function initUI(){
  drawMeter(I("dlMeter"),0,meterBk,dlColor,0);
  drawMeter(I("ulMeter"),0,meterBk,ulColor,0);
  drawMeter(I("pingMeter"),0,meterBk,pingColor,0);
  drawMeter(I("jitMeter"),0,meterBk,jitColor,0);
  I("dlText").textContent="";
  I("ulText").textContent="";
  I("pingText").textContent="";
  I("jitText").textContent="";
  I("ip").textContent="";
}
</script>
