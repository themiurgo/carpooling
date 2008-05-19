var num = {minimum:1,maximum:5,
   notANumberMessage: "Errore", notAnIntegerMessage: "Errore",
   tooLowMessage:"Errore",tooHighMessage:"Errore"};


var voto = new LiveValidation('voto',{
   validMessage: ' ', onlyOnBlur: true,insertAfterWhatNode:'headT'});

voto.add( Validate.Numericality, num);

function validaVote() {
   var areAllValid = LiveValidation.massValidate( [  voto ] );
    
   if (areAllValid)
      document.getElementById('voteForm').submit();
}