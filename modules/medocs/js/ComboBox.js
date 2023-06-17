/*
 *	ComboBox
 *	By Jared Nuzzolillo
 *
 *	Updated by Erik Arvidsson
 *	http://webfx.eae.net/contact.html#erik
 *	2002-06-13	Fixed Mozilla support and improved build performance
 *
 */

Global_run_event_hook = true;
Global_combo_array    = new Array();

Array.prototype.remove=function(dx)
{ 
    if(isNaN(dx)||dx>this.length){self.status='Array_remove:invalid request-'+dx;return false}
    for(var i=0,n=0;i<this.length;i++)
    {  
        if(this[i]!=this[dx])
        {
            this[n++]=this[i]
        }
    }
    this.length-=1
}

function ComboBox_make()
{
    var bt,nm;
    nm = this.name+"txt"; 
    
    this.txtview = document.createElement("INPUT")
    this.txtview.type = "text";
    this.txtview.name = nm;
    this.txtview.id = nm;
    this.txtview.className = "combo-input"
    this.view.appendChild(this.txtview);
	 	     
    this.valcon = document.createElement("INPUT");
    this.valcon.type = "hidden";
    this.view.appendChild(this.valcon)
   
    var tmp = document.createElement("IMG");
    tmp.src = "___";
    tmp.style.width = "1px";
    tmp.style.height = "0";
    this.view.appendChild(tmp);
	 
	 //added by VAN 03-28-08
	 var img1 = document.createElement("IMG");
    img1.setAttribute("src", "../../images/redpfeil_l.gif");
	 img1.setAttribute("border", "0");
	 img1.setAttribute("align", "absmiddle");
    
    var tmp = document.createElement("BUTTON");
	 var image;
	 //tmp.appendChild(document.createTextNode('?'));
	 tmp.appendChild(img1);
    tmp.className = "combo-button";
	 
	this.view.appendChild(tmp);
   	tmp.onfocus = function () { this.blur(); };
	tmp.onclick = new Function ("", this.name + ".toggle()");
}

function ComboBox_choose(realval,txtval)
{
    this.value         = realval;
    var samstring = this.name+".view.childNodes[0].value='"+txtval+"'"
    window.setTimeout(samstring,1)
    this.valcon.value  = realval;
	 //alert(this.valcon.value);
	 /*
	 	'onFocus="setKeyCode(0,"'+sess_en+'","'+encounter_type+'","'+encounter_type_a+'","'+sess_user_name+'")"'+ 
			'onBlur="trimString(this);"
	 */
	 var encounter_type = $('encounter_type').value;
	 var sess_en = $('sess_en').value;
	 var encounter_type_a = $('encounter_type_a').value;
	 var sess_user_name = $('sess_user_name').value;
	 
	 setKeyCode(0,sess_en,encounter_type,encounter_type_a,sess_user_name);
	 //alert(this.name);
	 //trimString(this.id);
}

function ComboBox_mouseDown(e)
{
    var obj,len,el,i;
    el = e.target ? e.target : e.srcElement;
    while (el.nodeType != 1) el = el.parentNode;
    var elcl = el.className;
    if(elcl.indexOf("combo-")!=0)
    {
				
        len=Global_combo_array.length
        for(i=0;i<len;i++)
        {
        
            curobj = Global_combo_array[i]
            
            if(curobj.opslist)
            {
                curobj.opslist.style.display='none'
            }
        }
    }
}

function ComboBox_handleKey(e)
{ 		
	 //added by VAN 03-28-08
	 var key,obj,eobj,el,strname;
    eobj = e;
    key  = eobj.keyCode;
    el = e.target ? e.target : e.srcElement;
	 //refreshCombo(el.value);
	 //alert(el.value);
	 while (el.nodeType != 1) el = el.parentNode;
    elcl = el.className
    if(elcl.indexOf("combo-")==0)
    {
        if(elcl.split("-")[1]=="input")
        {
            strname = el.id.split("txt")[0]
            obj = window[strname];
			
            obj.expops.length=0
            obj.update();
            obj.build(obj.expops);
            if(obj.expops.length==1&&obj.expops[0].text=="(No matches)"){}//empty
            else{obj.opslist.style.display='block';}
            obj.value = el.value;
            obj.valcon.value = el.value;
			}
     }
	  //refreshCombo(el.value, obj.expops.length);
	  //refreshCombo(el.value);
}

function ComboBox_update()
{
    var opart,astr,alen,opln,i,boo;
    boo=false;
    opln = this.options.length
    astr = this.txtview.value.toLowerCase();
    //alert(this.txtview.value);
	 //refreshCombo(this.txtview.value);
	 
	 alen = astr.length
	 if(alen==0)
    {
        for(i=0;i<opln;i++)
        {
            this.expops[this.expops.length]=this.options[i];boo=true;
        }
    }
    else
    {
        for(i=0;i<opln;i++)
        {
            opart=this.options[i].text.toLowerCase().substring(0,alen)
            if(astr==opart)
            {
                this.expops[this.expops.length]=this.options[i];boo=true;
            }
        }
    }
    if(!boo){this.expops[0]=new ComboBoxItem("(No matches)","")}
}


function ComboBox_remove(index)
{
    this.options.remove(index)
}

function ComboBox_add()
{
    var i,arglen;
    arglen=arguments.length
    for(i=0;i<arglen;i++)
    {
        this.options[this.options.length]=arguments[i]
    }
}

function ComboBox_build(arr)
{
    var str,arrlen
    arrlen=arr.length;
    str = '<table id="tableId" class="combo-list-width" cellpadding=0 cellspacing=0>';
    var strs = new Array(arrlen);
	 //alert('encounter_type = '+$('encounter_type').value);
	 this.clear;
	 for(var i=0;i<arrlen;i++)
    {	
	 		//onFocus="" onBlur=""
        /*
		  strs[i] = '<tr>' +
			'<td class="combo-item" onClick="'+this.name+'.choose(\''+arr[i].value+'\',\''+arr[i].text+'\');'+this.name+'.opslist.style.display=\'none\';"' +
			'onMouseOver="this.className=\'combo-hilite\';" onMouseOut="this.className=\'combo-item\'" >&nbsp;'+arr[i].text+' : '+arr[i].id+'&nbsp;</td>' +
			'</tr>';
		 */
		 strs[i] = '<tr>' +
			'<td class="combo-item" onClick="'+this.name+'.choose(\''+arr[i].text+'\',\''+arr[i].text+'\');'+this.name+'.opslist.style.display=\'none\';"' +
			'onMouseOver="this.className=\'combo-hilite\';" onMouseOut="this.className=\'combo-item\'">&nbsp;'+
			arr[i].text+' : '+arr[i].id+'&nbsp;</td>' +
			'</tr>';
    }
    str = str + strs.join("") + '</table>'
    
    if(this.opslist){this.view.removeChild(this.opslist);}
    
    this.opslist = document.createElement("DIV")
    this.opslist.innerHTML=str;
    this.opslist.style.display='none';
    this.opslist.className = "combo-list";
	 //alert(this.opslist.length);
    this.opslist.onselectstart=returnFalse;
    this.view.appendChild(this.opslist);    
}
/*
function ComboBox_clear(){
	var list = document.getElementById('tableId');
	list.innerHTML = "";
}
*/


function ComboBox_toggle()
{
    if(this.opslist)
    {
        if(this.opslist.style.display=="block")
        {
            this.opslist.style.display="none"
        }
        else
        {
            this.update();
            this.build(this.options);
            this.view.style.zIndex = ++ComboBox.prototype.COMBOBOXZINDEX
            this.opslist.style.display="block"
        }
    }
    else
    {
        this.update();
        this.build(this.options);
        this.view.style.zIndex = ++ComboBox.prototype.COMBOBOXZINDEX
        this.opslist.style.display="block"
    }
}

function ComboBox()
{
    if(arguments.length==0)
    {
        self.status="ComboBox invalid - no name arg"
    }

    this.name     = arguments[0];
	 //added by VAN 03-28-08
	 this.id = arguments[0];
	 
    this.par      = arguments[1]||document.body
    this.view     = document.createElement("DIV");
    this.view.style.position='absolute';
    this.view.style.marginTop='-10px';	 
    this.options  = new Array();
    this.expops   = new Array();
    this.value    = ""

    this.build  = ComboBox_build
    this.make   = ComboBox_make;
    this.choose = ComboBox_choose;
    this.add    = ComboBox_add;
    this.toggle = ComboBox_toggle;
    this.update = ComboBox_update;
    this.remove = ComboBox_remove;
	 
	 //added by VAN 03-29-08
	 //this.clear = ComboBox_clear;
	 

    this.make()
    this.txtview = this.view.childNodes[0]
    this.valcon  = this.view.childNodes[1]
    
    this.par.appendChild(this.view)
    Global_combo_array[Global_combo_array.length]=this;
    if(Global_run_event_hook){ComboBox_init()}
}

ComboBox.prototype.COMBOBOXZINDEX = 1000 //change this if you must

function ComboBox_init() 
{
	if (document.addEventListener) {
		document.addEventListener("keyup", ComboBox_handleKey, false );
		document.addEventListener("mousedown", ComboBox_mouseDown, false );
	}
	else if (document.attachEvent) {
		document.attachEvent("onkeyup", function () { ComboBox_handleKey(window.event); } );
		document.attachEvent("onmousedown", function () { ComboBox_mouseDown(window.event); } );
	}
	
    Global_run_event_hook = false;
}

function returnFalse(){return false}

function ComboBoxItem(text,desc,value)
{	
	 this.id    = desc;
    this.text  = text;
    this.value = value;
}

document.write('<link rel="STYLESHEET" type="text/css" href="js/ComboBox.css">')