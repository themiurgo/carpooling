var citta=new LiveValidation('localita',{
   validMessage: ' ', onlyOnBlur: true});
var email=new LiveValidation('email',{
   validMessage: ' ', onlyOnBlur: true});



citta.add(Validate.Presence, {failureMessage:"Campo vuoto"});

email.add(Validate.Presence, {failureMessage:"Campo vuoto"});
email.add(Validate.Email, {failureMessage: "Indirizzo non valido"});





function validaProfilo() {
   var areAllValid = LiveValidation.massValidate( [citta,email]);
   
   if (areAllValid) {
      profilo = document.getElementById("profiloForm"); 
      profilo.submit();
     
   }
   
}
