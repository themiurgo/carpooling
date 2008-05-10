var presenzaTesto = {validMessage : " ",onlyOnBlur:true};

var anno = {minimum:1900,maximum:2050,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Errore",tooHighMessage:"Errore"};
var cil = {minimum:1,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Non valido"};


var marca=new LiveValidation('marca',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'marcaLabel'});
var modello=new LiveValidation('modello',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'modelloLabel'});
var cilindrata=new LiveValidation('cilindrata',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'cilLabel'});
var targa=new LiveValidation('targa',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'targaLabel'});
var annoImm=new LiveValidation('annoImmatr',presenzaTesto);
var cond =new LiveValidation('condizioni',presenzaTesto);

marca.add(Validate.Presence, {failureMessage:"Vuoto"});
modello.add(Validate.Presence, {failureMessage:"Vuoto"});
cilindrata.add(Validate.Presence, {failureMessage:"Vuoto"});
cilindrata.add(Validate.Numericality, cil);

targa.add(Validate.Presence, {failureMessage:"Vuoto"});
targa.add(Validate.Format, { pattern: /^\w\w[0-9][0-9][0-9]\w\w$/, failureMessage:"Targa non Italiana"});

annoImm.add(Validate.Presence, {failureMessage:"Vuoto"});
annoImm.add(Validate.Numericality, anno);

cond.add(Validate.Presence, {failureMessage:"Vuoto"});
cond.add(Validate.Numericality, cil);

function validaAuto() {
   var areAllValid = LiveValidation.massValidate( [  marca, modello, cilindrata,annoImm,cond ] );
    
   if (areAllValid) {
   var form=document.getElementById('autoForm');
      form.submit();
   }
}
