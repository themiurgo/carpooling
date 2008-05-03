var presenzaTesto = {validMessage : " ",onlyOnBlur:true};


//~ var ora = {minimum:0,maximum:23,
   //~ notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   //~ tooLowMessage:"Errore",tooHighMessage:"Errore"};
//~ var minuto = {minimum:0,maximum:59,
   //~ notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
    //~ tooLowMessage:"Errore",tooHighMessage:"Errore"};
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

var partenza = new LiveValidation('partenza',presenzaTesto);
var destinaz = new LiveValidation('destinaz',presenzaTesto);
var passeggeri=new LiveValidation('postiDisp',presenzaTesto);
var spese=new LiveValidation('spese',presenzaTesto);
var gPartenza=new LiveValidation('giornoPartenza',presenzaTesto);
var mPartenza=new LiveValidation('mesePartenza',presenzaTesto);
var aPartenza=new LiveValidation('annoPartenza',presenzaTesto);
var oraP=new LiveValidation('ora',presenzaTesto);
var minP=new LiveValidation('minuti',presenzaTesto);
var durataO=new LiveValidation('durataOre',presenzaTesto);
var durataM=new LiveValidation('durataMinuti',presenzaTesto);


partenza.add(Validate.Presence, {failureMessage:"Vuoto"});
destinaz.add(Validate.Presence, {failureMessage:"Vuoto"});

passeggeri.add(Validate.Presence, {failureMessage:"Vuoto"});
passeggeri.add(Validate.Numericality, numPass);

spese.add(Validate.Presence, {failureMessage:"Vuoto"});
spese.add(Validate.Numericality, soldi);


gPartenza.add(Validate.Presence, {failureMessage:"Vuoto"});
gPartenza.add( Validate.Numericality, giorno);
mPartenza.add(Validate.Presence, {failureMessage:"Vuoto"});
mPartenza.add( Validate.Numericality, mese);
aPartenza.add(Validate.Presence, {failureMessage:"Vuoto"});
aPartenza.add( Validate.Numericality, anno);

oraP.add(Validate.Presence, {failureMessage:"Vuoto"});
//~ oraP.add( Validate.Numericality, ora);

minP.add(Validate.Presence, {failureMessage:"Vuoto"});
//~ minP.add( Validate.Numericality, minuto);

durataO.add(Validate.Presence, {failureMessage:"Vuoto"});
//~ durataO.add( Validate.Numericality, ora);
durataM.add(Validate.Presence, {failureMessage:"Vuoto"});
//~ durataM.add( Validate.Numericality, minuto);







function validaTrip() {
   var areAllValid = LiveValidation.massValidate( [  partenza, destinaz,spese,gPartenza,mPartenza,aPartenza,oraP,minP,durataO,durataM ] );
    
    
   if (areAllValid) {
      document.mapForm.submit();
   }
   
}