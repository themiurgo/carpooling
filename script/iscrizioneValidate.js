var options = {validMessage : " ",onlyOnBlur:true};

var day = {minimum:1,maximum:31,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Errore",tooHighMessage:"Errore"};
var month = {minimum:1,maximum:12,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Errore",tooHighMessage:"Errore"};
var year = {minimum:1900,maximum:2050,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Errore",tooHighMessage:"Errore"};

var nome=new LiveValidation('nome',options);
var cognome=new LiveValidation('cognome',options);
var gNascita=new LiveValidation('gNascita',options);
var mNascita=new LiveValidation('mNascita',options);
var aNascita=new LiveValidation('aNascita',options);
var gPatente=new LiveValidation('gPatente',options);
var mPatente=new LiveValidation('mPatente',options);
var aPatente=new LiveValidation('aPatente',options);
var email=new LiveValidation('email',options);
var user=new LiveValidation('user',options);
var psw=new LiveValidation('psw',options);
var citta=new LiveValidation('citta',options);

nome.add(Validate.Presence, {failureMessage:"Campo vuoto"});

cognome.add(Validate.Presence, {failureMessage:"Campo vuoto"});

gNascita.add(Validate.Presence, {failureMessage:"Vuoto"});
gNascita.add( Validate.Numericality, day);
mNascita.add(Validate.Presence, {failureMessage:"Vuoto"});
mNascita.add( Validate.Numericality, month);
aNascita.add(Validate.Presence, {failureMessage:"Vuoto"});
aNascita.add( Validate.Numericality, year);

gPatente.add(Validate.Presence, {failureMessage:"Vuoto"});
mPatente.add(Validate.Presence, {failureMessage:"Vuoto"});
aPatente.add(Validate.Presence, {failureMessage:"Vuoto"});

email.add(Validate.Presence, {failureMessage:"Campo vuoto"});
email.add(Validate.Email, {failureMessage: "Indirizzo non valido"});

user.add(Validate.Presence, {failureMessage:"Campo vuoto"});

psw.add(Validate.Presence, {failureMessage:"Campo vuoto"});

citta.add(Validate.Presence, {failureMessage:"Campo vuoto"});

// Diamo il focus al primo elemento
setFormFocus("iscrizione");
