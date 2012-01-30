
function OnLoad99()
{
    document.getElementById("display1").innerHTML = "<center><br><br><br>Connecting... Please Wait<br><img src='loader.gif'></center>";
    GetStatus();
    setInterval("GetStatus",10000);	
}


function DoNotify()
{
	
    xmlHttpDn=GetXmlHttpObject();

    if (xmlHttpDn==null)
    {

        alert ("Browser does not support HTTP Request");

        return;

    }



    //xmlHttpN.abort();
	

    var url="index.php?action=notify";

     
    xmlHttpDn.open("GET",url,true);
    xmlHttpDn.setRequestHeader("Content-type","application/x-www-form-urlencoded");

  

    xmlHttpDn.send();


//WriteByMe(document.getElementById("msg1").value);


}


function GetXmlHttpObject()
{

    var xmlHttp=null;

    try

    {

        // Firefox, Opera 8.0+, Safari

        xmlHttp=new XMLHttpRequest();

    }

    catch (e)

    {

        //Internet Explorer

        try

        {

            xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");

        }

        catch (e)

        {

            xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");

        }

    }

    return xmlHttp;

}


function doRefresh(msg)
{
	
    xmlHttp=GetXmlHttpObject();

    if (xmlHttp==null)
    {

        alert ("Browser does not support HTTP Request");

        return;

    }



    //xmlHttpN.abort();
	

    var url="index.php";

    xmlHttp.onreadystatechange=stateChanged;
    xmlHttp.open("POST",url,true);
    xmlHttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

  

    xmlHttp.send("action=send&msg="+msg);


//WriteByMe(document.getElementById("msg1").value);


}


var last="";
function stateChanged()
{

    //         documentr.getElementById("display").innerHTML="";
    //  document.getElementById("tbdy").innerHTML="<div><div><img src='images/loader.gif' style='position: relative; top: 24px; left: 215px;'></div><div style='padding-left:260px;'><span style='font:normal 12px Arial, Helvetica, sans-serif;'>Updating Results...</span></div></div>";
    // document.getElementById("rightloader").style.display="";
    if (xmlHttp.readyState==4)
    {
//Notify();
}
}

function WriteByMe(msg)
{
    document.getElementById("display1").innerHTML +="<font color='blue'>You: </font>"+msg+"<br>";
    Dump(msg);
    document.getElementById("msg1").value="";
}
function doRefreshK(event)
{
    if(event.keyCode==13)
    {
        //	doRefresh();
        WriteByMe(document.getElementById("msg1").value);
		
    }
}



function StartPro()
{
    document.getElementById("display1").innerHTML ="";
    //setTimeout("ReadMsg()",3000);
    //setTimeout("Notify()",3000);
    document.getElementById("display1").scrollTop=document.getElementById("display1").scrollHeight; 
    document.getElementById("display1").innerHTML += "<font color='green'>System: </font>&nbsp;Welcome in Chat support. How can i help you today?<br>";
    DoNotify();
    ReadMsg();
}

function StopPro()
{
    document.getElementById("display1").innerHTML = "<center><br><br><br><br>Sorry! The Operator is offline.<br><br><img src='sorry.gif'></center>";

}

function Notify()
{

    //alert("ok");
    xmlHttpN=GetXmlHttpObject();

    if (xmlHttpN==null)

    {

        alert ("Browser does not support HTTP Request");

        return;

    }

    var url="notify.php";

    // xmlHttp.onreadystatechange=stateChangedRead;
    xmlHttpN.open("GET",url,true);
    xmlHttpN.setRequestHeader("Content-type","application/x-www-form-urlencoded");


    xmlHttpN.send();

}

var msgg="";

function Dump(sent)
{
	
    //xmlHttpN.abort();
	
    xmlHttpD=GetXmlHttpObject();

    if (xmlHttpD==null)

    {

        alert ("Browser does not support HTTP Request");

        return;

    }




    var url="dump.php?sent="+sent;
    msgg=sent;

    xmlHttpD.onreadystatechange=stateChangedD;
    xmlHttpD.open("GET",url,true);
    xmlHttpD.setRequestHeader("Content-type","application/x-www-form-urlencoded");


    xmlHttpD.send(null);
		

}


function stateChangedD()
{
    if (xmlHttpD.readyState==4)
    {
        doRefresh(msgg);
    //	  Notify();
    }
	
}
var flag=false;
function ReadMsg()
{

	
    //xmlHttpN.abort();
	
    xmlHttpR=GetXmlHttpObject();

    if (xmlHttpR==null)

    {

        alert ("Browser does not support HTTP Request");

        return;

    }



    flag=true;
    var url="read.php";

    xmlHttpR.onreadystatechange=stateChangedRead;
    xmlHttpR.open("GET",url,true);
    xmlHttpR.setRequestHeader("Content-type","application/x-www-form-urlencoded");

    xmlHttpR.send(null);

		

}

function stateChangedRead()
{

    if (xmlHttpR.readyState==4)
    {

		
        if(last !=xmlHttpR.responseText && xmlHttpR.responseText !="<br>")
        {
            document.getElementById("display1").innerHTML += "<font color='green'>Operator: </font>"+xmlHttpR.responseText;
            last=xmlHttpR.responseText;
        }
        ReadMsg();
        //Notify();
        flag=false;
    }
}





function GetStatus()
{

	
    //xmlHttpN.abort();
	
    xmlHttpS=GetXmlHttpObject();

    if (xmlHttpS==null)

    {

        alert ("Browser does not support HTTP Request");

        return;

    }




    var url="st.php";

    xmlHttpS.onreadystatechange=stateChangedStts;
    xmlHttpS.open("GET",url,true);
    xmlHttpS.setRequestHeader("Content-type","application/x-www-form-urlencoded");

    xmlHttpS.send(null);

		

}

function stateChangedStts()
{

    if (xmlHttpS.readyState==4)
    {
        var s=xmlHttpS.responseText;
        if(s.search(/y/i) >= 0)
        {
            document.getElementById("stts").innerHTML = "<font color='white'> >>Online<< </font>";

            StartPro();
        }
        else
        {
            document.getElementById("stts").innerHTML = "<font color='white'> >>Offline<< </font>";
            StopPro();
        }
		   
		
    }
}

