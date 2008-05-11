


var citta=new LiveValidation('localita',{
   validMessage: ' ', onlyOnBlur: true});
var email=new LiveValidation('email',{
   validMessage: ' ', onlyOnBlur: true});
var user=new LiveValidation('userName',{
   validMessage: ' ', onlyOnBlur: true});


citta.add(Validate.Presence, {failureMessage:"Campo vuoto"});

email.add(Validate.Presence, {failureMessage:"Campo vuoto"});
email.add(Validate.Email, {failureMessage: "Indirizzo non valido"});

user.add(Validate.Presence, {failureMessage:"Campo vuoto"});




function validaProfilo() {
   var areAllValid = LiveValidation.massValidate( [  citta,email,user]);
   
   if (areAllValid) {
      profilo = document.getElementById("profiloForm"); 
      profilo.submit();
   }
   
}
