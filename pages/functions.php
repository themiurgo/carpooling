<?php

/*
 * Gestisce le azioni
 */
function handle_action () {
   switch ($_GET['action']) {
      case "login":
	 checkUser($_POST['username'],$_POST['password']);
	 break;

      case "logout":
	 unset($_SESSION['user']);
	 break;

      case "register":
	 registraUtente();
	 break;

      case "registerAuto":
	 registerCar();
	 break;
      /* Funzione ancora incompleta*/
      case "modifyAuto":
	 modificaAuto();
	 break;

      case "registerTrip":
      #echo $_POST['oraPart'];
	 registerTrip($_POST['idAuto'],$_POST['partenza'],
	    $_POST['destinaz'],
	    $_POST['y'].'-'.$_POST['m'].'-'.$_POST['d'],
	    $_POST['oraPart'],$_POST['durata'],$_POST['fumo'],
	    $_POST['musica'],$_POST['postiDisp'],$_POST['spese'],
	    $_POST['note']);
	 break;
      
      case "joinTrip":
	 partecipaTragitto();
	 break;
     
     case "blockTrip":
      bloccaTragitto();
      break;
   }
}

function parseTemplate ($template) {
   /* return preg_replace ("/\{ ([\$]?)(\w+)(..)? \}/e","$1$2$3",
      $template); */
//   return preg_replace("/\{\s(.*)\s\}/e","$1",$template);

   return preg_replace("/\{\s(.*)\s\}/e","$1",$template);
}

/*
 * Effettua il login, variando opportunamente le variabili di
 * sessione 'user' e 'userId'. Ritorna l'username dell'utente in
 * caso di successo e null in caso di insuccesso.
 */
function checkUser ($username, $password) {
   
    $user_query = "select ID,psw from Utenti where `username`='".$username."'";
    $res = execQuery($user_query);
    $a = mysql_fetch_array($res);
    
    $psw = $a['psw'];
   // print "Confronto ".$password." con ".$psw;

    if ( $password == $psw ) { 
      $_SESSION['user'] = $username;
      $_SESSION['userId'] = $a['ID'];
    }
    
    else {
       $_SESSION['wronglogin']=true;
    }
    
    return getUser();
}


/*
 * Ritorna l'username dell'utente (se loggato) oppure
 * null (se non loggato).
 */
function getUser () {
   if (isset($_SESSION['user']))
        return $_SESSION['user'];
   else
        return null;
}

/*
 * Ritorna l'id dell'utente (se loggato) oppure
 * null (se non loggato).
 */
function getUserId () {
   if (isset($_SESSION['userId'])) 
        return $_SESSION['userId'];
   else
        return null;
}

/*
 * Restituisce i percorsi per cui si puo' scrivere un
 * feedback.
 */
function feedback ($targetUserId) {
   $feedback_query="select Tragitto.*
      from UtentiTragitto join Tragitto
      on UtentiTragitto.idTragitto=Tragitto.ID
      where Tragitto.idPropr='".$targetUserId."'
         and UtentiTragitto.idUtente='".getUserId()."'
      limit 5";
   $res=execQuery($feedback_query);
   while ($row2=mysql_fetch_array($res,MYSQL_ASSOC)) {
      $feedback=$feedback.$row2['ID'];
   }
   return $feedback;
}


/* ---------------------
 * FUNZIONI DI TEMPLATE
 * --------------------- */

/* 
 * Ritorna eventuali tag da aggiungere dentro <head></head>,
 * ad esempio nelle pagine dove sono presenti mappe.
 */
function headType() {
   switch ($_GET['p']) {
      case "nuovo":
      case "cerca":
        
      # Script per la gestione delle GMaps
      return <<<DH
<script type="text/javascript" src="script/gmaps.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAApM5Cuio981ky_h5rXnr3uhT2yXp_ZAY8_ufC3CFXhHIE1NvwkxRczD8EFDGHM7KVLNJB1qP52_6uEg"></script>
DH;

      default:
      # Nella pagina non � presente la GMap
      return null;
   }
}

/*
 * Ritorna <body> in versione "semplice" oppure arricchito di
 * operazioni da effettuare al caricamento della pagina.
 */
function bodyType() {
   switch ($_GET['p']) {
      case "nuovo":
      case "cerca":

         // Centro la mappa nella localita' preferita dell'utente
         if (isset($_SESSION['user'])) {
            $location_query = "select localita from Utenti where ID='".getUserId()."' "; 
            $res = execQuery($location_query);
            $row = mysql_fetch_array($res);
            return '<body onload="creaMappa(\''.$row['localita'].'\')" onunload="GUnload()">';
         }
        
         else {
            # Centro di default della Mappa
            $default = 'Catania';
            return '<body onload="creaMappa(\''.$default.'\')" onunload="GUnload()">';
         }
         break;

      default:
         #Se non vi sono particolari funzioni, viene restituito semplicemente il tag
         return "<body>";
   }
}
 

/*
 * Restituisce il menu.
 */
function menu () {


   # Utente NON LOGGATO
   if (!getUser()) {
      return <<<MNNL
      <a href="#" onclick="loginScript()">Login</a>&nbsp;&middot;
      <a href="index.php?p=iscrizione">Iscriviti</a>&nbsp;&middot;
      <a href="index.php?p=tragitti">Tragitti</a>&nbsp;&middot;
      <a href="index.php?p=cerca">Cerca Tragitto</a>&nbsp;&middot;
      <a href="index.php?p=utenti">Utenti</a>&nbsp;&middot;
      <a href="index.php?p=about">About Us</a>
MNNL;
   }
    
   # Utente LOGGATO
   else {
      $Utente = getUser();
      return <<<MNL
      <b>$Utente</b> - 
      <a href="index.php?p=profilo">Profilo</a>&nbsp;&middot;
      <a href="index.php?p=tragitti">Tragitti</a>&nbsp;&middot;
      <a href="index.php?p=cerca">Cerca Tragitto</a>&nbsp;&middot;
      <a href="index.php?p=nuovo">Nuovo Tragitto</a>&nbsp;&middot;
      <a href="index.php?p=auto">Auto</a>&nbsp;&middot;
      <a href="index.php?p=utenti">Utenti</a>&nbsp;&middot;
      <a href="index.php?p=about">About Us</a>&nbsp;&middot;
      <a href="index.php?action=logout">Logout</a>
MNL;
   }
}


/*
 * Ritorna il contenuto della pagina richiesta, che va
 * a sostituire <!-- CONTENT --> nel template index.htm
 */
function content () {
    
   // Pagina visualizzata di default
   if (!isset($_GET['p']))
      $_GET['p']="tragitti";

   // Pagine consentite per...
   getUser() ?
      // ...un utente loggato
      $allowed = array("profilo","tragitti","cerca","nuovo","auto",
         "utenti","about") :
      # ...un utente NON loggato
      $allowed = array("tragitti","cerca","utenti","about","iscrizione");

   $content = "";
   
   if (isset($_SESSION['wronglogin'])) {
      $content="<h1>Login errato</h1>";
      unset($_SESSION['wronglogin']);
   }

   // Controllo che la pag richiesta sia consentita...
   if (in_array($_GET['p'],$allowed)) {

      // ... recupero il template ...
      $file_content = implode ("",file("template/".$_GET[p].".htm"))
         or die("Pagina non trovata");
      
      // ... ed effettuo le opportune sostituzioni.
      $content = $content.prepare_content($file_content);
   }
   
   else {
      $error = accessDenied();
      $content = $content.$error;
   }

   
   return $content;
}


/* 
 * Effettua le opportuni sostituzioni di stringhe con variabili
 * PHP, a seconda della pagina richiesta.
 */
function prepare_content ($template) {

   // Devo effettuare sostituzioni diverse per ogni pagina
   switch ($_GET['p']) {
      case 'tragitto':
	 if (isset($_GET['idTrip'])) {
	    $q1="select * from Tragitto where ID='".$_GET['idTrip']."'";
               $r1=mysql_fetch_array($execQuery($q1));
               $idT= $row['ID'];
               $pro = $row['idPropr'];
               $name_query="select userName from Utenti where ID=$r1[idPropr]";
               $res_name = execQuery($name_query);
               $row_name = mysql_fetch_array($res_name);
               $ora = $row['oraPart'];
               $durata=$row['durata'];
               $data = $row['dataPart'];
              
               $row['fumo'] ? 
                  $fumo = "Per Fumatori" : $fumo = "NO Fumatori";
              
               $row['musica'] ?
                  $mus = "Con Musica" : $mus = "NO Musica"; 
	 }

      break;

        
      case 'tragitti':
            
         $q1 = "select Tragitto.*, userName from `Tragitto`
            join Utenti on idPropr=Utenti.ID
            order by `dataPart` desc,`oraPart` desc limit 5";
         $res = execQuery($q1);
  
	    // Stampo i tragitti
            while ($r1 = mysql_fetch_array($res)) {
	       $o=preg_replace("/\{\s(.+?)\s\}/e","$1",$template);
               $final_content = $final_content.$o;
            }
            
            #Eventuali dettagli sul tragitto selezionato
            if ( isset($_GET['idTrip'] ) ){
               $trip_query="select * from Tragitto where ID='".$_GET['idTrip']."'";
               $res = execQuery($trip_query);
               $row=mysql_fetch_array($res);
               $idT= $row['ID'];
               $pro = $row['idPropr'];
               $name_query="select userName from Utenti where ID=$pro";
               $res_name = execQuery($name_query);
               $row_name = mysql_fetch_array($res_name);
               $ora = $row['oraPart'];
               $durata=$row['durata'];
               $data = $row['dataPart'];
              
               $row['fumo'] ? 
                  $fumo = "Per Fumatori" : $fumo = "NO Fumatori";
              
               $row['musica'] ?
                  $mus = "Con Musica" : $mus = "NO Musica"; 
               
               
               # CLAMOROSO BUG: anche se il metodo � 'post', si comporta come get ( che � quello che voglio).
               $dettagli = <<<TRIP
               <div style='padding:0' class='bgRed'><h2>Dettagli Tragitto</h2>
               <span class="utenti">
                  Organizzatore:  <b>$row_name[userName]</b>
               </span>
               <span class="posti">
                  <b>$row[postiDisp]</b> posti disponibili
               </span>
               <p class="listatragitti">
               <span class="tragitto">
               Da <b>$row[partenza]</b> a <b>$row[destinaz]</b>
               </span>
               <span class="altro">
               <b>$fumo</b> - <b>$mus</b>
               </span>
               <span class="orario">
                  <b>$row[dataPart]</b>
                  <b>$row[oraPart]</b> (<b>$row[durata]</b>)
               </span>
               <br/> <br/>
TRIP;
               # L'utente corrente e' il proprietario del tragitto, quindi di sbloccano lel unzioni avanzate.
               if ( $pro == $_SESSION['userId'] ) {
               
                  if ( canModify() ) {
                     $mod= <<<MOD
                     <span class="tragitto">
                        <form id="joinForm" action="index.php?p=tragitti&action=modTrip&idTrip=$row[ID]" method="post">
                           <label for="joinButton">Sei il proprietario del tragitto</label><br/>
                           <button id="registerAutoButton" type="submit">Modifica Tragitto</button>
                        </form>
                     </span>
              
MOD;
                     $dettagli=$dettagli.$mod;
                  } 
                        $block= <<<BLOCK
                        <span class="tragitto">
                              <form id="blockForm" action="index.php?p=tragitti&action=blockTrip&idTrip=$row[ID]" method="post">
                              <label for="blockButton">Hai avuto un'imprevisto?</label><br/>
                              <button id="registerAutoButton" type="submit">Blocca Tragitto</button>
                           </form>
                        </span>
                        
                        </div>
BLOCK;
                     $dettagli=$dettagli.$block;
            # L'utente corrente nON e' il proprietario del tragitto, gli viene data la possibilit� di partecipare.
            } 
	    else
	       $extra= <<<FORM
   <form id=joinForm" action="index.php?p=tragitti&action=joinTrip&idTrip=$row[ID]&posti=$row[postiDisp]" method="post">
   <span class="join">
   <label for="joinButton">Vuoi Partecipare?</label><br/>
   <button id="registerAutoButton" type="submit">Conferma</button>
   </span>
   </form>
   </div>
FORM;
               $dettagli=$dettagli.$extra;
               $final_content = $dettagli.$final_content;
            }
            
            break;

      case 'utenti':
            $final_content=preg_replace("/\{\s(.*)\s\}/e","$1",$template);
         break;

      case 'profilo':
         if (!isset($_GET['u']))
            $_GET['u']=getUser();

         $q1="select *,UNIX_TIMESTAMP(`dataIscriz`) as dataisc,
	    DATE_FORMAT(NOW(), '%Y')-DATE_FORMAT(dataNascita, '%Y')-
   	       (DATE_FORMAT(NOW(), '00-%m-%d') <
	       DATE_FORMAT(dataNascita, '00-%m-%d')) as eta,
	    DATE_FORMAT(NOW(), '%Y')-DATE_FORMAT(dataPatente, '%Y')-
   	       (DATE_FORMAT(NOW(), '00-%m-%d') <
	       DATE_FORMAT(dataPatente, '00-%m-%d')) as patente,
	    DATE_FORMAT(dataIscriz,'%d.%m.%Y') as dataIscriz
   	    from `Utenti`
            where `userName`='".$_GET['u']."'";
         $res=execQuery($q1);
         $r1=mysql_fetch_array($res,MYSQL_ASSOC);
        
         if (getUser()!=$_GET['u'])
            $feedback=feedback($r1['ID']);

         $output=preg_replace("/\{\s(.*)\s\}/e","$1",$template);
         
         $trip_query = "select * from Tragitto
            where idPropr = $_SESSION[userId]
            order by `dataPart` desc,`oraPart`";
         $res = execQuery($trip_query);
         if (mysql_num_rows($res) != 0) {
            $out="<ol>";
            
            while ($row = mysql_fetch_array($res)) {
              
               $ora = $row['oraPart'];
               $data = $row['dataPart'];
               $fumo = $row['fumo'];
               $part = $row['partenza'];
               $dest = $row['destinaz'];
               $idTrip = $row['ID'];
              
              $piece = <<<TR
               <li>
                  <a href="index.php?p=tragitti&idTrip=$idTrip">$ora  $data</a>
                  <br />Da : <b>$part</b>  a : <b>$dest</b> <p>
               </li>
TR;
               $out=$out.$piece."</ol>";
            }  
             $out=$out."</ol>";
            $output=eregi_replace("<!-- LISTATRAGITTI -->",$out,$output);
            
         } 
	 
	 else 
            $output=eregi_replace("<!-- LISTATRAGITTI -->","<p>Non hai creato alcun tragitto finora</p>",$output);
         
         
     $final_content = $final_content.$output;

         break;
      
      case 'cerca':
	 if (getUser())
	    $welcome=<<<WLCM
	<p>Ciao <b>$_SESSION[user]</b>! Se hai dubbi sul funzionamento
	della ricerca puoi sempre consultare la pagine delle
	<a href='index.php?p=about'>istruzioni</a></p>.
WLCM;
	 else
	    $welcome=<<<WLCM
	 <p>Benvenuto su CarPooling, il portale fatto per viaggiare insieme!
      	 Leggi <a href='index.php?p=about'>come funziona</a> e <a href='index.php?p=iscrizione'>registrati subito!</a></p>
WLCM;

      $output = eregi_replace("<!-- WELCOME -->",$welcome,$template);
	 $final_content = $final_content.$output;
      	 break;
      
      # Nuovo Tragitto e inserimento Auto
      case 'nuovo':
      case 'auto':   
            
         # Estrago le informazioni sulle eventuali auto registrate dall' utente corrente
         $auto_query = "select targa,marca,modello
            from Auto join AutoUtenti on Auto.ID=AutoUtenti.idAuto
            where AutoUtenti.idUtente='".getUserId()."'";
            
         // BUG DA CORREGGERE: perche' se abilito l'echo qua sotto
         // mi stampa due volte? E' comunque un bug che risiede altrove.
         // echo $auto_query;
         $res = execQuery($auto_query);

         # Nessun auto registrata
         if (mysql_num_rows($res) == 0) {
            if ($_GET['p'] == "nuovo")
	       return noAuto();
               
            elseif ($_GET['p'] == "auto")
               $output = eregi_replace("<!-- REGISTEREDAUTOS -->",
                  "<label class='alertText'>Attualmente, non hai nessuna auto registrata</alert>",$template);
                
            $final_content = $final_content.$output;
         }
               
         # Almeno un'auto registrata
         else {
            $row = mysql_fetch_array($res);
                
            # Pagina nuovo tragitto --> viene visualizzato l' oggetto selection
            if ($_GET['p']=="nuovo") {
                
            $output = eregi_replace("<!-- AUTOS -->",cars_ofUser(getUserId()),$template);
             
            }
            
	    # Pagina auto --> viene visualizzata la Selection insieme ad un form per 
	    # dare la possibilit� di modificare i dati dell' auto
            elseif ($_GET['p']=="auto") {
               # La prima chiamata ha come parametro '$a'
               $output = eregi_replace("<!-- REGISTEREDAUTOS -->",'
                     Elenco delle auto gi&agrave; registrate :'.
                        cars_ofUser(getUserId()).'
                  <button id="modifyAutoButton" type="button" onclick="dofill()">
                     Modifica Auto
                  </button>',$template); 
            }
            $final_content = $final_content.$output;
         }
         
         break;
         default:
        
            # Nulla da sostituire
            $final_content = $final_content.$template;
            break;
    }
   
    # Restituisce il contenuto
    return $final_content;
}

/* ---------------------
 * FUNZIONI DI SERVIZIO
 * --------------------- */
 
/*
 * Affidabilita' dell'utente
 */
function trust ($username) {
   return "molto affidabile";
}

/* Messaggio di errore: non puoi accedere alla pagina*/
function accessDenied() {
return <<<ERR
<div style="padding:0" class="bgAzure">
   <p>Non hai i permessi per visitare questa pagina!</p>
         <a href="./index.php">home</a>
</div>
ERR;

}

function noAuto() {
return <<<ERR
<div style="padding:0" class="bgGold">
   <p>Non hai auto! Provvedi subito a 
      <a href='index.php?p=auto'>registrarne</a> una.
   <p>
</div>
ERR;

}

function canModify() {
   
   $check_query = "select count(idUtente) as num from UtentiTragitto where idTragitto = '".$_GET['idTrip']."'";
   $res = execQuery($check_query) or die("Query non valida1: " . mysql_error());
   $row = mysql_fetch_array($res);
   
   if ( $row['num']  > 1 ) {
      return false;
   }
   return true;


}


?>
