/* ---------------------
 * FUNZIONI DI SERVIZIO
 * --------------------- */

/* 
 *  Funzione per far apparire e scomparire il menù di login
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
