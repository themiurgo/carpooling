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
var gAuto=new LiveValidation('gAuto',presenzaTesto);
var mAuto=new LiveValidation('mAuto',presenzaTesto);
var aAuto=new LiveValidation('aAuto',presenzaTesto);

marca.add(Validate.Presence, {failureMessage:"Vuoto"});
modello.add(Validate.Presence, {failureMessage:"Vuoto"});
cilindrata.add(Validate.Presence, {failureMessage:"Vuoto"});
cilindrata.add(Validate.Numericality, cil);

targa.add(Validate.Presence, {failureMessage:"Vuoto"});
targa.add(Validate.Format, { pattern: /^[a-z][a-z][0-9][0-9][0-9][a-z][a-z]$/, failureMessage:"Targa non Italiana"});
gAuto.add(Validate.Presence, {failureMessage:"Vuoto"});
gAuto.add( Validate.Numericality, giorno);
mAuto.add(Validate.Presence, {failureMessage:"Vuoto"});
mAuto.add( Validate.Numericality, mese);
aAuto.add(Validate.Presence, {failureMessage:"Vuoto"});
aAuto.add( Validate.Numericality, anno);
