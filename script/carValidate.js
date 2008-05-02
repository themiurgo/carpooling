var presenzaTesto = {validMessage : " ",onlyOnBlur:true};

var giorno = {minimum:1,maximum:31,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Errore",tooHighMessage:"Errore"};
var mese = {minimum:1,maximum:12,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Errore",tooHighMessage:"Errore"};
var anno = {minimum:1900,maximum:2050,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Errore",tooHighMessage:"Errore"};
var cil = {minimum:1,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Non valido"};


var marca=new LiveValidation('marca',presenzaTesto);
var modello=new LiveValidation('modello',presenzaTesto);
var cilindrata=new LiveValidation('cilindrata',presenzaTesto);
var targa=new LiveValidation('targa',presenzaTesto);
var dAuto=new LiveValidation('dAuto',presenzaTesto);
var mAuto=new LiveValidation('mAuto',presenzaTesto);
var yAuto=new LiveValidation('yAuto',presenzaTesto);

marca.add(Validate.Presence, {failureMessage:"Vuoto"});
modello.add(Validate.Presence, {failureMessage:"Vuoto"});
cilindrata.add(Validate.Presence, {failureMessage:"Vuoto"});
cilindrata.add(Validate.Numericality, cil);

targa.add(Validate.Presence, {failureMessage:"Vuoto"});
targa.add(Validate.Format, { pattern: /^\w\w[0-9][0-9][0-9]\w\w$/, failureMessage:"Targa non Italiana"});
dAuto.add(Validate.Presence, {failureMessage:"Vuoto"});
dAuto.add( Validate.Numericality, giorno);
mAuto.add(Validate.Presence, {failureMessage:"Vuoto"});
mAuto.add( Validate.Numericality, mese);
yAuto.add(Validate.Presence, {failureMessage:"Vuoto"});
yAuto.add( Validate.Numericality, anno);

function validaAuto() {
   var areAllValid = LiveValidation.massValidate( [  marca, modello, cilindrata,dAuto,mAuto,yAuto ] );
    
    
   if (areAllValid) {
      document.autoForm.submit();
   }
   
}