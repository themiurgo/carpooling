var presenzaTesto = {validMessage : " ",onlyOnBlur:true};

var numPass = {minimum:1,maximum:10,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Non valido",tooHighMessage:"Sei Sicuro?"};
var soldi = {minimum:0,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Non valido"};

var partenza = new LiveValidation('partenza',presenzaTesto);
var destinaz = new LiveValidation('destinaz',presenzaTesto);
var passeggeri=new LiveValidation('postiDisp',presenzaTesto);
var spese=new LiveValidation('spese',presenzaTesto);

partenza.add(Validate.Presence, {failureMessage:"Vuoto"});
destinaz.add(Validate.Presence, {failureMessage:"Vuoto"});

passeggeri.add(Validate.Presence, {failureMessage:"Vuoto"});
passeggeri.add(Validate.Numericality, numPass);

spese.add(Validate.Presence, {failureMessage:"Vuoto"});
spese.add(Validate.Numericality, soldi);


function validaTrip() {
   var areAllValid = LiveValidation.massValidate( [  partenza, destinaz,spese ] );
    
    
   if (areAllValid) {
      document.mapForm.submit();
   }
   
}