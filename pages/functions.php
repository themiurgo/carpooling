<?php

/*
 * Gestisce le azioni.
 */
function handle_action () {
   if (isset($_GET['action'])) {
   
      switch ($_GET['action']) {
         case "login":
            checkUser($_POST['username'],$_POST['password']);
            break;

         case "logout":
            unset($_SESSION['user']);
            unset($_SESSION['userId']);
            break;

         case "register":
            registraUtente();
            break;

         case "manageAuto":
            gestioneAuto();
            break;
         
         case "manageProfilo":
            if ($_POST['hiddenProfilo'] == "updateProfilo")
               aggiornaProfilo();
            break;

         case "registerTrip":
            registerTrip();
            break;

         case "joinTrip":
            partecipaTragitto();
            break;

         case "leaveTrip":
            abbandonaTragitto();
            break;

         case "blockTrip":
            bloccaTragitto();
            break;

         case "unblockTrip":
            sbloccaTragitto();
            break;

         case "voteTrip":
            feedback_new(getUserId(),$_GET['idTrip'],$_POST['idValutato'],
               $_POST['voto'],$_POST['note']);
      }
   }
}


/* --------------
 * GESTIONE LOGIN
 * -------------- */

/*
 * Effettua il login. Ritorna l'username (successo) o null (insuccesso)
 *
 * $username (string) nome utente dell'utente
 * $password (string) password da verificare
 */
function checkUser ($username, $password) {
    $q = "select ID,psw from Utenti where `username`='".$username."'";
    $r = mysql_fetch_array(execQuery($q));

    if ($password == $r['psw']) { 
      $_SESSION['user'] = $username;
      $_SESSION['userId'] = $r['ID'];
    }
    else $_SESSION['wronglogin']=true;
    return getUser();
}

/*
 * Ritorna l'username (se loggato) o null (se non loggato).
 */
function getUser () {
   if (isset($_SESSION['user']))
      return $_SESSION['user'];
   return null;
}

/*
 * Ritorna l'id utente (se loggato) o null (se non loggato).
 */
function getUserId () {
   if (isset($_SESSION['userId']))
      return $_SESSION['userId'];
   return null;
}


/* --------
 * TEMPLATE
 * -------- */
 
 /*
  * Effettua le opportune sostituzioni all'interno del template.
  */
function parseTemplate ($template) {
  return preg_replace("/\{\s(.*)\s\}/e","$1",$template);
}


/* 
 * Ritorna eventuali tag da aggiungere dentro <head></head>
 * (ad es. nelle pagine dove sono presenti mappe).
 */
function headType() {
// Pagina visualizzata di default
   if (!isset($_GET['p']))
      $_GET['p']="cerca";
      
   switch ($_GET['p']) {
      /* Tutte le seguenti sono pagine che hanno bisogno della GMap */
      case "nuovo":
      case "cerca":
      case "tragitto":
#      break; # FIXME
        
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
 * Ritorna <body> o aggiunge opportunamente azioni onLoad per la GMap.
 */
function bodyType() {
// Pagina visualizzata di default
   if (!isset($_GET['p']))
      $_GET['p']="cerca";
   

   switch ($_GET['p']) {
      case "nuovo":
      case "cerca":

         // Centro la mappa nella localita' preferita dell'utente
         if (isset($_SESSION['user'])) {
            $q = "select localita from Utenti where ID='".getUserId()."' "; 
            $r = mysql_fetch_array(execQuery($q));
         }
        
         else $r['localita']="Catania";

         return '<body onload="creaMappa(\''.$r['localita'].'\')" onunload="GUnload()">';
         break;

      case "tragitto":
         $q = "select partenza,destinaz from Tragitto where ID='$_GET[idTrip]'";
         $r = mysql_fetch_array(execQuery($q));
         return "<body onload=\"creaMappa('$r[partenza]');
            creaPercorso('$r[partenza]','$r[destinaz]');\" onunload=\"GUnload()\">";
         break;

      default:
         return "<body>";
   }
}
 
 
/*
 * Restituisce il menu
 * (index.htm)
 */
function menu () {
   # Utente NON  loggato
   if (!getUser()) return <<<MNNL
      <a href="#" onclick="loginScript()">Login</a>&nbsp;&middot;
      <a href="index.php?p=iscrizione">Iscriviti</a>&nbsp;&middot;
      <a href="index.php?p=tragitti">Tragitti</a>&nbsp;&middot;
      <a href="index.php?p=cerca">Cerca Tragitto</a>&nbsp;&middot;
      <a href="index.php?p=utenti">Utenti</a>&nbsp;&middot;
      <a href="index.php?p=about">About Us</a>
MNNL;
    
   # Utente loggato
   else return "
   <b>".getUser()."</b> - ".<<<MNL
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


/*
 * Ritorna il contenuto della pagina richiesta
 * (index.htm)
 */
function content () {
   // Pagina visualizzata di default
   if (!isset($_GET['p']))
      $_GET['p']="cerca";

   // Pagine consentite per...
   getUser() ?
      // ...un utente loggato
      $allowed = array("profilo","tragitti","cerca","nuovo","auto",
         "utenti","about","tragitto") :
      // ...un utente NON loggato
      $allowed = array("tragitti","tragitto","cerca","utenti","about","iscrizione","profilo");

   $content = "";
   
   /*
     *  I successivi controlli di flusso indicano la visualizzazione di
     *  messaggi particolari come condizioni di errore, messaggi di conferma, ecc
     */
   if (isset($_SESSION['wronglogin'])) {
      $content="<h1>Login errato</h1>";
      unset($_SESSION['wronglogin']);
   }
   
   if (($_GET['p'] == 'nuovo') && !hasAuto()) {
      $error = noAuto();
      $content = $content.$error;
      return $content;
   }
   
   if (($_GET['p'] == 'error') ) {
      $error = dataError();
      $content = $content.$error;
      return $content;
   }
   
   if ($_GET['p'] == 'success' ) {
      $conf = showConfirm();
      $content = $content.$conf;
      return $content;
   }

   /**
     *  La pagina da visualizzare appartiene al flusso 'regolare' del sito, e ne
     *  viene estratto il template. 
     */
   if (in_array($_GET['p'],$allowed)) {
      $file_content = implode ("",file("template/$_GET[p].htm"))
         or die("Pagina non trovata");
      $content = $content.prepare_content($file_content);
   }else {
      /* Non si hanno i permessi per visitare la pagina */
      $error = accessDenied();
      $content = $content.$error;
   }

   return $content;
}

/* 
 * Sostituisce le stringhe con variabili, a seconda della pagina richiesta.
 *
 * $template (stringa) contiene il template
 */
function prepare_content ($template) {
   // Devo effettuare sostituzioni diverse per ogni pagina
   switch ($_GET['p']) {
      case 'tragitto':
         if (isset($_GET['idTrip'])) {
            // Informazioni sul tragitto
            $q="select Tragitto.*,Utenti.userName as proprietario,
                  Utenti.userName='".getUser()."' as controllo,
                  postiDisp-COUNT(*) as postiAdesso,
                  concat(dataPart,' ',oraPart)<now() as passed
               from Tragitto join Utenti on Utenti.ID=Tragitto.idPropr
               join UtentiTragitto on UtentiTragitto.idTragitto = Tragitto.ID
               where Tragitto.ID='".$_GET['idTrip']."'
               group by UtentiTragitto.idTragitto";
            $r=mysql_fetch_array(execQuery($q));
              
            $r['fumo'] ? 
               $fumo = "Per Fumatori" : $fumo = "NO Fumatori";
            $r['musica'] ?
               $mus = "Con Musica" : $mus = "NO Musica";

            // Informazioni su tutti i partecipanti
            $q2="select Utenti.userName
               from Tragitto
               join UtentiTragitto on Tragitto.ID = UtentiTragitto.idTragitto
               join Utenti on UtentiTragitto.idUtente = Utenti.ID
               where Tragitto.ID=$_GET[idTrip]";
            $res2=execQuery($q2);

            $users="";
            if (mysql_num_rows($res2) != 0) {
               while ($r2=mysql_fetch_array($res2)) 
                  $users=$users."<a href=\"index.php?p=profilo&amp;u=$r2[userName]\">$r2[userName]</a> ";
               $users=$users."<br />";
            }

            // Informazioni su tutti i partecipanti            
            if (getUserId()) {
            $q3="select UtentiTragitto.idUtente=".getUserId()." as partecipo
               from Tragitto
               join UtentiTragitto on Tragitto.ID=UtentiTragitto.idTragitto
               where Tragitto.ID=".$_GET[idTrip]."
               and UtentiTragitto.idUtente=".getUserId();
               //echo $q3;
            $r3=mysql_fetch_array(execQuery($q3));
            }
         }

         $final_content=preg_replace("/\{\s(.+?)\s\}/e","$1",$template);
         break;

      case 'tragitti':
         $q1 = "select Tragitto.*, userName,postiDisp-count(*) as postiAdesso
            from `Tragitto`
            join Utenti on idPropr=Utenti.ID
            join UtentiTragitto on Tragitto.ID=UtentiTragitto.idTragitto
            group by UtentiTragitto.idTragitto
            order by `dataPart` desc,`oraPart` desc limit 5";
         $res = execQuery($q1);
  
         $o=preg_replace("/\{\s(.+?)\s\}/e","$1",$template);
         $final_content = $final_content.$o;

         break;

      case 'utenti':
         $final_content=preg_replace("/\{\s(.*)\s\}/e","$1",$template);
         break;

      case 'profilo':
         // Di default visualizzo il profilo dell'utente corrente
         if (!isset($_GET['u']))
            $_GET['u']=getUser() or die();

         // Estraggo le informazioni sul profilo
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

         $r1=mysql_fetch_array(execQuery($q1));
          
         ($r1['fumatore'] == 0) ?
            $r1['fumatore']="No" : $r1['fumatore']="Si";

         /*Varia il contenuto della pagina a seconda che si è in modalita 'leggi profilo' o 'modifica profilo' */
         if (getUser() == $_GET['u']) {
            if ( $_POST['hiddenProfilo']=="modifyProfilo" ) {
               //$r1['userName']="<input id='userName' name='userName' class='modificatori' value='$r1[userName]'/>";
               $r1['email']="<input id='email' name='email' class='modificatori' value='$r1[email]'/>";
               $r1['fumatore']="<select name ='fumatore' id='fumatore' class='modificatori' style='width: auto'> <option value='0'>No</option><option value='1'>SI</option></select>";
               $r1['localita']="<input id='localita'  name='localita' class='modificatori'  value='$r1[localita]'/>";
               $button = "<button id='updateProfiloButton' type='button' onclick='updateProfilo()'>Aggiorna</button>";
            }   else
               $button="<button type='submit' name='modifica' value='modifica'>Modifica</button>";
         }else 
            $button="";
         
         $final_content=preg_replace("/\{\s(.*)\s\}/e","$1",$template);

          // Se visualizzo un profilo non mio, estraggo i tragitti
         // su cui posso fare feedback
         //    if (getUser()!=$_GET['u'])
         //            $feedback=feedback($r1['ID']); 

         break;

      case 'cerca':
      case 'nuovo':
      case 'auto':   
      case 'iscrizione':
         $final_content=preg_replace("/\{\s(.+?)\s\}/e","$1",$template);
         break;

      default:
         // Nulla da sostituire
         $final_content = $final_content.$template;
         break;
    }
   
    // Restituisco il contenuto
    return $final_content;
}


/* ---------------------
 * FUNZIONI DI SERVIZIO
 * --------------------- */
 
 
 /*
  * Imposta la variabile per poter visualizzare un messaggio
  * di conferma.
  */
 function success() {
   $_GET['p']="success";
 }
 
 
 /*
  * Imposta i parametri per il messaggio di errore.
  *
  * $source -- è il parametro che ha causato l'errore
  * $page -- è la pagina verso la quale viene offerto di andare.
  *
  */
 function setErrorParam($source,$page) {
    $_GET['p']="error";
    $_SESSION['source'] = $source;
    $_SESSION['redirect'] = $page;
 }
 
 
 /* 
 * Visualizza un messaggio di errore dovuto al fatto che
 * l'utente non ha ancora registrato un' auto.
 */
function noAuto() {
 return <<<ERR
<div style="padding:0" class="bgGold">
   <p>Non hai auto! Provvedi subito a 
      <a href='index.php?p=auto'>registrarne</a> una.
   </p>
</div>
ERR;
}
 
 
/** 
 *  Visualizza un messaggio di errore dovuto al fatto che
 *  l'utente ha inserito input non validi.
 */
function dataError() {
return <<<ERR
<div style="padding:0" class="bgRed">
   <p>$_SESSION[source] ! Impossibile effettuare la registrazione.</p>
         <a href="./index.php?p=$_SESSION[redirect]">Ritenta</a>
</div>
ERR;

}


/**
 *  Visualizza un messaggio di conferma per il corretto inserimento 
 *  dei dati nel Database.
 */
function showConfirm() {
return <<<CON
<div style="padding:0" class="bgGreen">
      <p>Dati inseriti con successo!</p>
      Torna alla <a href="./index.php">home</a>
</div>
CON;

}


/** 
 *  Visualizza un messaggio di errore dovuto al fatto che
 *  l'utente non ha i permessi per visualizzare la pagina indicata.
 */
function accessDenied() {
return <<<ERR
<div style="padding:0" class="bgAzure">
   <p>Non hai i permessi per visitare questa pagina!</p>
         <a href="./index.php">home</a>
</div>
ERR;

}

/*
 * Messaggio di benvenuto
 * (Pagina cerca)
 */
function welcome () {
   if (getUser())
      return <<<WLCM
      <p>Ciao <b>$_SESSION[user]</b>! Se hai dubbi sul funzionamento
      della ricerca puoi sempre consultare la pagina delle
      <a href='index.php?p=about'>istruzioni</a>.</p>
WLCM;
   else
      return <<<WLCM
	 <p>Benvenuto su CarPooling, il portale fatto per viaggiare insieme!
      Leggi <a href='index.php?p=about'>come funziona</a> e <a href='index.php?p=iscrizione'>registrati subito!</a>.</p>
WLCM;
}


/*
 * Ritorna una select da cui selezionare la data (in italiano)
 */
function italianDate ($firstY,$lastY,$suffix=null) {
   $monthName = array(1=> "Gen","Feb","Mar","Apr","Mag",
     "Giu","Lug","Ago","Set","Ott","Nov","Dic");

   return numericDropDown("giorno$suffix",1,31,"GG").' - '.
      numericDropDown("mese$suffix",1,12,"MM",$monthName).' - '.
      numericDropDown("anno$suffix",$firstY,$lastY,"AAAA");
}


/*
 * Ritorna una select da cui selezionare l'ora e i minuti.
 */
function timeSelect () {
   return numericDropDown("ora",0,23,"HH").' : '.
      numericDropDown("minuti",0,59,"MM");
}


/*
 * Ritorna una select da cui selezionare la durata (in ora e minuti)
 */
function durataSelect () {
   return numericDropDown("durataOre",0,23,"HH").' ore e '.
      numericDropDown("durataMinuti",0,59,"MM").' minuti';
}


/*
 * Ritorna una select numerica (utile per date, anni, ...)
 *
 * $id -- id della select
 * $start -- numero da cui partire
 * $stop -- numero a cui finire
 * $first -- primo elemento (opzionale)
 * $names -- array di nomi (opzionale)
 * (nuovo.htm, auto.htm)
 */
function numericDropDown($id,$start,$stop,$first=null,$names=null) {
   if ($useDate == 0)
      $useDate = Time();
   $a="<select id=\"$id\" name=\"$id\">
      <option value=\"-2\">$first</option>
      <option value=\"-1\"> </option>\n";
   if ($names)
      for($i = $start; $i <= $stop; $i++)
         $a=$a."<option value=\"$i\">".
            "$names[$i]</option>\n";
   else
      for($i = $start; $i <= $stop; $i++)
         $a=$a."<option value=\"$i\">".
            "$i</option>\n";
   $a=$a.'</select>';

   return $a;
}


/*
 * Ritorna una select che permette di selezionare un'auto fra quelle che
 * l'utente ha registrato.
 * (nuovo.htm, auto.htm)
 */
function carSelect () {
   // Prendo informazioni su eventuali auto gia' registrate
   $auto_query = "select targa,marca,modello
      from Auto join AutoUtenti on Auto.ID=AutoUtenti.idAuto
      where AutoUtenti.idUtente='".getUserId()."'";
   $res = execQuery($auto_query);

   // Se non c'e' nessun'auto mostro un avviso...
   if ((mysql_num_rows($res) == 0) && ($_GET['p'] == "nuovo"))
      return <<<ERR
<div style="padding:0" class="bgGold">
   <p>Non hai auto! Provvedi subito a 
      <a href='index.php?p=auto'>registrarne</a> una.
   <p>
</div>
ERR;

   // ...o consento di registrare una nuova auto
   elseif (mysql_num_rows($res)==0 && $_GET['p'] == "auto") {
      return $output.<<<FRM
   <button id="newAuto" type="button" onclick="showForm()">
      Inserisci nuova
   </button>
FRM;
   }
               
   // Se c'e' qualche auto consento di selezionarla
   else {
      $row = mysql_fetch_array($res);
      $output=$output.cars_ofUser(getUserId());
      
      // Pulsante di modifica
      if ($_GET['p']=="auto") 
         $output=$output.<<<MOD
   <button id="modifyAutoButton" type="button" onclick="doFill()">
      Modifica
   </button>
   <button id="newAuto" type="button" onclick="showForm()">
      Inserisci nuova
   </button>
MOD;
      return $output;
   }
}


/*
 * Visualiza un pulsante che consente di partecipare al tragitto.
 *
 * owner (booleano) indica se e' il proprietario
 * hasJoint (booleano) indica se partecipa al tragitto
 * postiDisp (intero) posti disponibili (fissi) oltre al guidatore
 * postiAdesso (intero) posti disponibili adesso (variabili)
 */
function controlTrip ($owner,$hasJoint,$postiDisp,$postiAdesso,$inThePast,
      $blocked) {
      //echo $inThePast;
      //echo now();
   if ($inThePast)
      return null;

   if ($blocked && $owner)
      return <<<UNBLOCK
      <button type="button" onclick="location.href='index.php?p=tragitto&amp;idTrip=$_GET[idTrip]&amp;action=unblockTrip'">
      Sblocca il tragitto
      </button>
UNBLOCK;

   if ($owner && $postiDisp-1 == $postiAdesso)
      return <<<BLOCK
      <button type="button" onclick="location.href='index.php?p=tragitto&amp;idTrip=$_GET[idTrip]&amp;action=blockTrip'">
      Blocca il tragitto
      </button>
BLOCK;
   elseif ($hasJoint && !$owner)
      return <<<LEAVE
      <button type="button" onclick="location.href='index.php?p=tragitto&amp;idTrip=$_GET[idTrip]&amp;action=leaveTrip'">
    Abbandona il tragitto
      </button>
LEAVE;

   elseif ($postiAdesso > 0 && !$owner && !$hasJoint && !$blocked)
      return <<<JOIN
      <button type="button" onclick="location.href='index.php?p=tragitto&amp;idTrip=$_GET[idTrip]&amp;action=joinTrip';">
    Partecipa al tragitto
      </button>
JOIN;

   else return null;
}

/*
 * Stampa un tragitto
 * $r -- una riga di Tragitto + userName
 */
function printTrip ($r){
    $r[data]=parseDate($r[oraPart]." ".$r[dataPart]);

    return "<a href=\"index.php?p=tragitto&amp;idTrip=$r[ID]\">
      $r[data]</a>
      (<a href=\"index.php?p=profilo&amp;u=$r[userName]\">$r[userName]</a>)<br />
   Da <b>$r[partenza]</b> a <b>$r[destinaz]</b>";
}

/*
 * Accetta un timedate (HH:MM:SS YYYY:MM:DD) e ritorna
 * DD.MM.YYYY HH:MM
 */
function parseDate ($timedate) {
   list($time,$date)=explode(" ",$timedate);
   list($hours,$minutes,$seconds)=explode(":",$time);
   list($year,$month,$day)=explode("-",$date);
   return "$day.$month.$year $hours:$minutes";
}

/*
 * Visualizza un form per l'invio di feedback su altri utenti.
 * (tragitto.htm)
 *
 * $id (int)
 * $partecipo (boolean)
 * $inThePast (boolean)
 */
function voteTrip ($id,$partecipo,$inThePast) {
   if (!$partecipo || !$inThePast)
      return null;

   $q="select autore,valutato,tragittoAut,data as tragitto,Utenti.userName,Utenti.ID as idValutato,valutazione
      from FeedbackPossibili
      left join Feedback 
         using (autore,tragittoAut,valutato,tragittoVal)
      join Utenti
         on valutato=Utenti.ID
      join Tragitto
         on Tragitto.ID = tragittoAut
      where (autore,tragittoAut) = (".getUserId().", $_GET[idTrip])
         and valutazione is null
         and dataPart<now()";
         //echo $q;
   $res=execQuery($q);
   if (mysql_num_rows($res)!=0) {

   $utenti="<select id=\"idValutato\" name=\"idValutato\">";
   while ($r=mysql_fetch_array($res)) {
      if ($r['ID'] != getUserId())
         $utenti=$utenti."<option value=\"$r[idValutato]\">$r[userName]</option>";
   }
   $utenti=$utenti."</select>";
   }

   // Mettere controllo data nel passato
   return "
   
   <div class=\"bgGreen\">
      <h4>Dai un giudizio sui tuoi compagni di viaggio</h4>
      <form action=\"index.php?p=tragitto&amp;action=voteTrip&amp;idTrip=$_GET[idTrip]\" method=\"post\" class=\"center\">
   <label for=\"utente\">Utente</label>$utenti
      <label for=\"voto\">Voto</label>".
      numericDropDown("voto",1,5).
      "<label for=\"note\">Note</label>
      <input type=\"text\" id=\"note\" name=\"note\" size=30></input>".
      " <button>Vota!</button>
      </form>".
      viewVotes($_GET[idTrip])."
      </div>";
}

/*
 * Vede se si può modificare.
 */
/*function canModify() {
   $q = "select count(idUtente) as num
      from UtentiTragitto
      where idTragitto = '".$_GET['idTrip']."'";
   $res = execQuery($q);
   $r = mysql_fetch_array($res);
   
   if ($r['num'] > 1) 
      return false;

   return true;
} */



?>
