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
      echo $_POST['oraPart'];
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
      # Nella pagina non è presente la GMap
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
    
# Pagina visualizzata di default se non è specificata
# una pagina differente.
   if (!isset($_GET['p'])) {
      $_GET['p']="tragitti";
   }

   if (getUser()) {
      #Lista delle pagine consentite per un utente loggato
      $allowed = array("profilo","tragitti","cerca","nuovo","auto",
         "utenti","about");
   } else {
      #Lista delle pagine consentite per un utente NON loggato
      $allowed = array("tragitti","cerca","utenti","about","iscrizione");
   }

   $content = "";
   
   if (isset($_SESSION['wronglogin'])) {
      $output="Login errato";
      unset($_SESSION['wronglogin']);
   }

   // Controllo che la pag richiesta sia consentita...
   if (in_array($_GET['p'],$allowed)) {

      // ... la recupero ...
      $pagina = "template/".$_GET[p].".htm";
      $file = file($pagina)
         or die("Pagina non trovata");
      $file_content = implode ("",$file);
       # Ottieni il corretto contenuto da 'sostituire' all'interno della pagina
      $content = $content.prepare_content($file_content);
   } else {
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

   # Il contenuto è diverso a seconda della pagina selezionata.
   switch ($_GET['p']) {
        
      case 'tragitti':
            
         $q1 = "select Tragitto.*, userName from `Tragitto`
            join Utenti on idPropr=Utenti.ID
            order by `dataPart` desc,`oraPart` desc limit 5";
         $res = execQuery($q1);
  
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
               
               # Non capisco perchè non mi accetta $row['postiDisp'] dentro la heredoc :/
               // (Anto: non lo accettava perché lo devi scrivere senza virgolette)
               # CLAMOROSO BUG: anche se il metodo è 'post', si comporta come get ( che è quello che voglio).
               // Anto: in che senso?
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
               <form id="joinForm" action="index.php?p=tragitti&action=joinTrip&idTrip=$row[ID]&posti=$row[postiDisp]" method="post">
               <span class="join">
               <label for="joinButton">Vuoi Partecipare?</label><br/>
               <button id="registerAutoButton" type="submit">Conferma</button>
               <span>
               </form>
               </div>
TRIP;
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
	    echo $row[nome];
   	 $final_content=preg_replace("/\{\s(.*)\s\}/e","$1",$template);

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
            if ( $_GET['p'] == "nuovo" )
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
             
                # Pagina auto --> viene visualizzata la Selection insieme ad un form per 
                # dare la possibilità di modificare i dati dell' auto
            }
            
            elseif ($_GET['p']=="auto") {
               # La prima chiamata ha come parametro '$a'
               $output = eregi_replace("<!-- REGISTEREDAUTOS -->",'
                     Elenco delle auto gi&agrave; registrate :'.
                        cars_ofUser(getUserId()).'
                  <button id="modifyAutoButton" type="submit" onclick="disableText()" action="">
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


?>
