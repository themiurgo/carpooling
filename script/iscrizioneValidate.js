
var day = {minimum:1,maximum:31,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Errore",tooHighMessage:"Errore",
   insertAfterWhatNode:"annoNascita"};
var month = {minimum:1,maximum:12,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Errore",tooHighMessage:"Errore"};
var year = {minimum:1900,maximum:2050,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Errore",tooHighMessage:"Errore"};

var citta=new LiveValidation('citta',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'cittaLabel'});
var nome=new LiveValidation('nome',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'nomeLabel'});
var cognome=new LiveValidation('cognome',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'cognomeLabel'});

var dBorn=new LiveValidation('giornoNascita',{
   insertAfterWhatNode:'nascitaLabel',validMessage: ' ',onlyOnBlur:true});
var mBorn=new LiveValidation('meseNascita',{
   insertAfterWhatNode:'nascitaLabel',validMessage: ' ',onlyOnBlur:true});
var yBorn=new LiveValidation('annoNascita',{
   insertAfterWhatNode:'nascitaLabel',validMessage: ' ',onlyOnBlur:true});

var dDrive=new LiveValidation('giornoPatente',{
   insertAfterWhatNode:'patenteLabel',validMessage: ' ',onlyOnBlur:true});
var mDrive=new LiveValidation('mesePatente',{
   insertAfterWhatNode:'patenteLabel',validMessage: ' ',onlyOnBlur:true});
var yDrive=new LiveValidation('annoPatente',{
   insertAfterWhatNode:'patenteLabel',validMessage: ' ',onlyOnBlur:true});

var email=new LiveValidation('email',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'emailLabel'});
var user=new LiveValidation('user',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'userLabel'});
var psw=new LiveValidation('psw',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'pswLabel'});
var psw2=new LiveValidation('psw2',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'psw2Label'});



nome.add(Validate.Presence, {failureMessage:"Campo vuoto"});
citta.add(Validate.Presence, {failureMessage:"Campo vuoto"});

cognome.add(Validate.Presence, {failureMessage:"Campo vuoto"});

dBorn.add(Validate.Presence, {
   failureMessage:"Vuoto"});
dBorn.add( Validate.Numericality, day);

mBorn.add(Validate.Presence, {failureMessage:"Vuoto"});
mBorn.add( Validate.Numericality, month);

yBorn.add(Validate.Presence, {failureMessage:"Vuoto"});
yBorn.add( Validate.Numericality, year);

dDrive.add(Validate.Presence, {failureMessage:"Vuoto"});
dDrive.add( Validate.Numericality, day);

mDrive.add(Validate.Presence, {failureMessage:"Vuoto"});
mDrive.add( Validate.Numericality, month);

yDrive.add(Validate.Presence, {failureMessage:"Vuoto"});
yDrive.add( Validate.Numericality, year);

email.add(Validate.Presence, {failureMessage:"Campo vuoto"});
email.add(Validate.Email, {failureMessage: "Indirizzo non valido"});

user.add(Validate.Presence, {failureMessage:"Campo vuoto"});

psw.add(Validate.Presence, {failureMessage:"Campo vuoto"});

psw2.add( Validate.Confirmation, { match: 'psw', failureMessage: "Le psw sono diverse" } );



// Diamo il focus al primo elemento
setFormFocus("iscrizione");

function validaIscr() {
   var areAllValid = LiveValidation.massValidate( [  citta,nome, cognome,email,user,psw,psw2,dBorn,mBorn,yBorn,dDrive,mDrive,yDrive]);
   
   if (areAllValid) {
      iscrizione = document.getElementById("iscrizioneForm"); 
      iscrizione.submit();
   }
   
}
