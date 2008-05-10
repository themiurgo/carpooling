var options = {validMessage: ' ',onlyOnBlur:true};

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

var nome=new LiveValidation('nome',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'nomeLabel'});
var cognome=new LiveValidation('cognome',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'cognomeLabel'});
//~ var dBorn=new LiveValidation('giornoNascita',{
   //~ insertAfterWhatNode:'nascitaLabel',validMessage: ' ',onlyOnBlur:true});
//~ var dBorn=new LiveValidation('meseNascita',{
   //~ insertAfterWhatNode:'nascitaLabel',validMessage: ' ',onlyOnBlur:true});
//~ var dBorn=new LiveValidation('annoNascita',{
   //~ insertAfterWhatNode:'nascitaLabel',validMessage: ' ',onlyOnBlur:true});
//~ var dDrive=new LiveValidation('giornoPatente',options);
//~ var mDrive=new LiveValidation('mesePatente',options);
//~ var yDrive=new LiveValidation('annoPatente',options);
var email=new LiveValidation('email',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'emailLabel'});
var user=new LiveValidation('user',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'userLabel'});
var psw=new LiveValidation('psw',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'pswLabel'});
var psw2=new LiveValidation('psw2',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'psw2Label'});
var citta=new LiveValidation('citta',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'cittaLabel'});

nome.add(Validate.Presence, {failureMessage:"Campo vuoto"});

cognome.add(Validate.Presence, {failureMessage:"Campo vuoto"});

//~ dBorn.add(Validate.Presence, {
   //~ failureMessage:"Vuoto"});
//~ dBorn.add( Validate.Numericality, day);

//~ mBorn.add(Validate.Presence, {failureMessage:"Vuoto"});
//~ mBorn.add( Validate.Numericality, month);

//~ yBorn.add(Validate.Presence, {failureMessage:"Vuoto"});
//~ yBorn.add( Validate.Numericality, year);

//~ dDrive.add(Validate.Presence, {failureMessage:"Vuoto"});
//~ dDrive.add( Validate.Numericality, day);

//~ mDrive.add(Validate.Presence, {failureMessage:"Vuoto"});
//~ mDrive.add( Validate.Numericality, month);

//~ yDrive.add(Validate.Presence, {failureMessage:"Vuoto"});
//~ yDrive.add( Validate.Numericality, year);

email.add(Validate.Presence, {failureMessage:"Campo vuoto"});
email.add(Validate.Email, {failureMessage: "Indirizzo non valido"});

user.add(Validate.Presence, {failureMessage:"Campo vuoto"});

psw.add(Validate.Presence, {failureMessage:"Campo vuoto"});

psw2.add( Validate.Confirmation, { match: 'psw', failureMessage: "Le password sono diverse" } );

citta.add(Validate.Presence, {failureMessage:"Campo vuoto"});

// Diamo il focus al primo elemento
setFormFocus("iscrizione");

function validaIscr() {
   var areAllValid = LiveValidation.massValidate( [  nome, cognome,email,user,psw,psw2,citta]);//,email,dBorn,mBorn,yBorn,dDrive,mDrive,yDrive,user,psw,psw2,citta ] );
    is = document.getElementById("iscrizioneForm"); 
    
   if (areAllValid) {
      document.iscrizioneForm.submit();
   }
   
}
