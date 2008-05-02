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
var dBorn=new LiveValidation('dBorn',options);
var mBorn=new LiveValidation('mBorn',options);
var yBorn=new LiveValidation('yBorn',options);
var dDrive=new LiveValidation('dDrive',options);
var mDrive=new LiveValidation('mDrive',options);
var yDrive=new LiveValidation('yDrive',options);
var email=new LiveValidation('email',options);
var user=new LiveValidation('user',options);
var psw=new LiveValidation('psw',options);
var psw2=new LiveValidation('psw2',options);
var citta=new LiveValidation('citta',options);

nome.add(Validate.Presence, {failureMessage:"Campo vuoto"});

cognome.add(Validate.Presence, {failureMessage:"Campo vuoto"});

dBorn.add(Validate.Presence, {failureMessage:"Vuoto"});
dBorn.add( Validate.Numericality, day);
mBorn.add(Validate.Presence, {failureMessage:"Vuoto"});
mBorn.add( Validate.Numericality, month);
yBorn.add(Validate.Presence, {failureMessage:"Vuoto"});
yBorn.add( Validate.Numericality, year);

dDrive.add(Validate.Presence, {failureMessage:"Vuoto"});
mDrive.add(Validate.Presence, {failureMessage:"Vuoto"});
yDrive.add(Validate.Presence, {failureMessage:"Vuoto"});

email.add(Validate.Presence, {failureMessage:"Campo vuoto"});
email.add(Validate.Email, {failureMessage: "Indirizzo non valido"});

user.add(Validate.Presence, {failureMessage:"Campo vuoto"});


psw.add(Validate.Presence, {failureMessage:"Campo vuoto"});

psw2.add( Validate.Confirmation, { match: 'psw', failureMessage: "Le password sono diverse" } );


citta.add(Validate.Presence, {failureMessage:"Campo vuoto"});

// Diamo il focus al primo elemento
setFormFocus("iscrizione");

function validaIscr() {
   var areAllValid = LiveValidation.massValidate( [  nome, cognome,email,user,psw,psw2,citta ] );
    is = document.getElementById("iscrizioneForm"); 
    
   if (areAllValid) {
      document.iscrizioneForm.submit();
   }
   
}
