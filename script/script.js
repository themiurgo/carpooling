/* ---------------------
 * FUNZIONI DI SERVIZIO
 * --------------------- */

/* 
 * Mostra / Nasconde il menù di login
 */
function loginScript() {
   var e = document.getElementById("login");
   if (e.style.visibility == 'visible') {
      e.style.visibility = 'hidden';
      e.style.display = 'none';
   }
   else {
      e.style.visibility = 'visible';
      e.style.display = 'block';
   }
   setFormFocus("login");
}
 
/*
 * Da' il focus al primo input attivo del form.
 */
function setFormFocus(formId){
    var form = document.getElementById(formId);
    var len = form.elements.length;

    for(var i = 0;i < len;i++){
      var curElement = form.elements[i];

      if(curElement.constructor  == HTMLInputElement){
        curElement.focus();
        return;
      }
    }
}

/*
 * Ritorna un oggetto xmlHttp compatibile con il browser
 */
function GetXmlHttpObject() {
   var xmlHttp=null;
   try {
      // Firefox, Opera 8.0+, Safari
      xmlHttp=new XMLHttpRequest();
   }
   catch (e) {
      // Internet Explorer
      try {
         xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
      }
      catch (e) {
         xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
  }
   return xmlHttp;
}
 
/* 
 *  Funzione per rendere non editabili le caselle di testo 
 *  della pagina 'modifica auto'.
 */
function disableText(){
    document.autoForm.marca.readOnly = true; 
    document.autoForm.modello.readOnly = true; 
    document.autoForm.targa.readOnly = true; 
    document.autoForm.cilindrata.readOnly = true; 
    document.autoForm.gAuto.readOnly = true; 
    document.autoForm.mAuto.readOnly = true; 
    document.autoForm.aAuto.readOnly = true; 
}	


/* Aggiornamento AJAX dei tragitti nella pagina 'cerca'*/
function risultatiAjax() {
   var xmlHttp=GetXmlHttpObject();
   res = document.getElementById("risultati");
   mapForm = document.getElementById("mapForm"); 
   //userName=document.getElementById("search_username");
   
   xmlHttp.onreadystatechange=function() {
      if(xmlHttp.readyState==4) {
         res.innerHTML=xmlHttp.responseText;
         creaPercorso();
      }
   }
   if (xmlHttp==null) {
      alert ("Il browser non supporta AJAX");
      return;
   }
   var url="./pages/ajax.php";
   var partenza = mapForm.partenzaText.value;
   var arrivo = mapForm.arrivoText.value;
   url=url+"?pa=" + partenza+"&ar="+arrivo;
   //alert (url);
   xmlHttp.open("GET",url,true);
   xmlHttp.send(null);
}
