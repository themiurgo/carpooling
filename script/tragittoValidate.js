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
var numPass = {minimum:1,maximum:7,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Non valido",tooHighMessage:"Sei Sicuro?"};
var soldi = {minimum:0,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Non valido"};

var gPartenza=new LiveValidation('gPartenza',presenzaTesto);
var mPartenza=new LiveValidation('mPartenza',presenzaTesto);
var aPartenza=new LiveValidation('aPartenza',presenzaTesto);
var oraPartenza=new LiveValidation('oraPartenza',presenzaTesto);
var durata=new LiveValidation('durata',presenzaTesto);
var passeggeri=new LiveValidation('passeggeri',presenzaTesto);
var spese=new LiveValidation('spese',presenzaTesto);

gPartenza.add(Validate.Presence, {failureMessage:"Vuoto"});
gPartenza.add( Validate.Numericality, giorno);
mPartenza.add(Validate.Presence, {failureMessage:"Vuoto"});
mPartenza.add( Validate.Numericality, mese);
aPartenza.add(Validate.Presence, {failureMessage:"Vuoto"});
aPartenza.add( Validate.Numericality, anno);

oraPartenza.add(Validate.Presence, {failureMessage:"Vuoto"});
oraPartenza.add(Validate.Format, { pattern: /^[0-2][0-9]:[0-5][0-9]$/, failureMessage:"Valore Errato"});
durata.add(Validate.Presence, {failureMessage:"Vuoto"});
durata.add(Validate.Format, { pattern: /^[0-2][0-9]:[0-5][0-9]$/, failureMessage:"Valore Errato"});

passeggeri.add(Validate.Presence, {failureMessage:"Vuoto"});
passeggeri.add(Validate.Numericality, numPass);
spese.add(Validate.Presence, {failureMessage:"Vuoto"});
spese.add(Validate.Numericality, soldi);
