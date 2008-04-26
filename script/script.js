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
    } else {
        e.style.visibility = 'visible';
        e.style.display = 'block';
    }
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
