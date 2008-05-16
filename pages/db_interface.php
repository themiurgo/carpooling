<?php
 
/* ---------------------
 * IMPOSTAZIONI DATABASE
 * --------------------- */
$db_host="localhost";
$db_usr="carpooler";
$db_psw="";
$db_name="Carpooling";
$db_conn=null;

/* --------------------
 * GESTIONE GENERALE DB
 * -------------------- */

/*
 * Connette al server MySQL e seleziona il DB
 */
function connectDb(&$dbconn) {
   global $db_host,$db_usr,$db_psw,$db_name,$db_conn;

   $db_conn = mysql_connect($db_host, $db_usr, $db_psw)
      or die ("DB Connection Error");
   mysql_select_db($db_name,$db_conn)
      or die ("DB Selection Error");
}

/*
 * Esegue una query e ritorna una variabile risorsa.
 * Invocata per l'esecuzione di tutte le query.
 */
function execQuery($query) {
   global $db_conn;

   if (!$db_conn)
      connectDb($db_conn);

   $res=mysql_query($query, $db_conn)
      or die("Query non valida".mysql_error());
 
   return $res;
}

/* ------
 * AZIONI
 * ------ */

/*
 * Effettua la registrazione di un utente.
 */
function registraUtente() { 
    $dataNascita=$_POST['annoNascita']."-".$_POST['meseNascita']."-".$_POST['giornoNascita'];
    $dataPatente=$_POST['annoPatente']."-".$_POST['mesePatente']."-".$_POST['giornoPatente'];
    
    # Data corrente per l'iscrizione
    $today = getdate(); 
    $dataIscriz=$today['year']."-".$today['mon']."-".$today['mday'];
    
    $registerUser_query = "insert into 
        Utenti(userName,psw,nome,cognome,dataNascita,email,dataPatente,fumatore,dataIscriz,localita,sesso) 
        values('".$_POST['user']."','".$_POST['psw']."','".$_POST['nome']."','".$_POST['cognome']."',
        '$dataNascita','".$_POST['email']."','$dataPatente',".$_POST['fumatore'].",'$dataIscriz','".$_POST['citta']."','".$_POST['sesso']."')";
      
      execQuery($registerUser_query);
}
	
# INCOMPLETA        
function modificaAuto() {
    #$targa = $datiAuto['targa'];	
    #non è logicamnete corretto: si dovrebbe usare sempre e cmq l 'ID;
    $selectAuto_query = "select * from auto where targa='$targa'";
    $res = execQuery($selectAuto_query);
    $row = mysql_fetch_array($res); 
} 
	
/*
 * Effettua la registrazione o la modifica di un auto
 */
function gestioneAuto() {
   #Il campo hidden ci comunica che l'operazione è di registrazione auto;
   if ($_POST['mecha'] == "new") {
      
      # Registrazione nella tabella Auto
      $q1 = "insert into Auto(targa,marca,modello,cilindrata,annoImmatr,condizioni,note) 
       values ('".strtoupper($_POST['targa'])."','".$_POST['marca']."','".$_POST['modello']."',
	       ".$_POST['cilindrata'].",'".$_POST['annoImmatr']."',".$_POST['condizioni'].",'".$_POST['note']."')";

      execQuery($q1);

      # Ottiene l'id dell'auto:Si potrebbe ottimizzare
      $query="select ID from Auto where targa='".$_POST['targa']."'";
      $res=execQuery($query);
      $row=mysql_fetch_array($res); 

      # Questo flag indica il fatto che chi registra l'auto ne è anche il proprietario
      $prop = true;
       
      #Registrazione nella tabella AutoUtenti
      $registerAuto_query2 = "insert into AutoUtenti(idAuto,idUtente,valido) values('".$row['ID']."','".getUserId()."',$prop)";

      execQuery($registerAuto_query2);
   } 

   #Il campo hidden dichiara che è un'operazione di aggiornamento 
   else if ( $_POST['mecha'] == "update" ) {     
      $q2="update Auto set note='".$_POST['note']."',condizioni= '".$_POST['condizioni']."' where ID='".$_POST['idAuto']."'";
      execQuery($q2);
    }
}

function aggiornaProfilo() {
   $q1 = "update Utenti set userName='".$_POST['userName']."',
      email='".$_POST['email']."',localita='".$_POST['localita']."' ,
      fumatore='".$_POST['fumatore']."' where ID='".getUserId()."'  ";
   execQuery($q1);
}

/*
 * Registrazione di un Tragitto ( Trip ) al sito
 * Data deve essere nel formato YYYY-MM-GG.
 */
function registerTrip() {
   $posti=$_POST['postiDisp']+1;

   if (controllaData($_POST['ora'],$_POST['minuti'],$_POST['mesePartenza'],
      $_POST['giornoPartenza'],$_POST['annoPartenza']) ) {
   $dataPart =$_POST['annoPartenza']."-".$_POST['mesePartenza']."-".$_POST['giornoPartenza'];
   $oraPart = $_POST['ora'].":".$_POST['minuti'];
   $durata = $_POST['durataOre'].":".$_POST['durataMinuti'];
   $q1 = "insert into  
      Tragitto(idPropr,idAuto,partenza,destinaz,dataPart,
	 oraPart,durata,fumo,musica,postiDisp,spese,note)
      values('".getUserId()."','".$_POST['idAuto']."',
	 '".$_POST['partenza']."','".$_POST['destinaz']."','$dataPart',
	 '".$oraPart."','".$durata."',
	 ".$_POST['fumo'].",".$_POST['musica'].",
	 ".$posti.",".$_POST['spese'].",
	 '".$_POST['note']."')";
    
    execQuery($q1);
    
    $registerTrip_query = "insert 
      into UtentiTragitto(idTragitto,idUtente) 
      values('".mysql_insert_id()."','".getUserId()."')";
    
    execQuery($registerTrip_query);
    
    } else {
      echo "la data di partenza è nel passato";
    }
    
    
} 

function partecipaTragitto(){
   #Controllo se l'utente si può aggiungere
   if (controllaTrip()) {
      $join_query = "insert into UtentiTragitto(idUtente,idTragitto) values('".$_SESSION['userId']."','".$_GET['idTrip']."')";
      execQuery($join_query);
   }

}

function abbandonaTragitto() {
   $join_query = "delete from UtentiTragitto where idUtente=".getUserId()." and idTragitto=$_GET[idTrip]";
      execQuery($join_query);
}

function bloccaTragitto() {
   $q=" update Tragitto set bloccato=1
         where ID='".$_GET['idTrip']."'
            and idPropr='".getUserId()."'";
   execQuery($q);
   if (mysql_affected_rows() == 0)
      echo "Errore!";
}

function sbloccaTragitto() {
   $q=" update Tragitto set bloccato=0
         where ID='".$_GET['idTrip']."'
            and idPropr='".getUserId()."'";
   execQuery($q);
   if (mysql_affected_rows() == 0)
      echo "Errore!";
}

function controllaTrip() {
   
   # Controllo se ci sono posti disponibili
   $trip_query = "select postiDisp from Tragitto where ID = '".$_GET['idTrip']."' ";
   $res = execQuery($trip_query);
   $row = mysql_fetch_array($res);
   
   if ( !$row['postiDisp'] ) {
      return false;
   }
   # Controllo se l'utente è già presente nel percorso
   $trip_query2 = "select idUtente from UtentiTragitto where IdTragitto= '".$_GET['idTrip']."' ";
   $res = execQuery($trip_query2);
   $row = mysql_fetch_array($res);
   
   if (in_array($_SESSION['userId'],$row)) { 
      echo "Non puoi!";
      return false;
   }

   return true;
}

function controllaData($ora,$minuti,$mese,$giorno,$anno) {
   
   #Data Completa della partenza
   //echo $_POST['ora'].$_POST['minuti'].$_POST['mese'].$_POST['giorno'].$_POST['anno'];
   $start = mktime($ora,$minuti,0,$mese,$giorno,$anno);
  
   $t = getdate(); 
   $now =mktime($t['hours'],$t['minutes'],0,$t['mon'],$t['mday'],$t['year']);

   $diff = $start - $now ;
   echo $diff;
   if  ( $diff < 0 ) {
      return false;
   }
   
   return true;
}

	
/*
 * Restituisce gli ultimi utenti iscritti
 */
function users_recentSignup () {
   $query = "select userName from Utenti order by dataIscriz desc limit 20"; 
   $res = execQuery($query);
   $o="";
   while ($r=mysql_fetch_array($res,MYSQL_ASSOC)) {
      $line="<a href=\"index.php?p=profilo&amp;u=$r[userName]\">$r[userName]</a> ";
      $o=$o.$line;
   }
   return $o;
}

/*
 * Restituisce gli utenti piu' attivi
 */
function users_mostActive() {
   $query = "select userName,count(*) as nTragitti
      from Utenti join Tragitto on Utenti.ID = Tragitto.idPropr
      group by Tragitto.idPropr
      order by nTragitti desc limit 5"; 
   $res = execQuery($query);
   $o="";
   while ($r=mysql_fetch_array($res,MYSQL_ASSOC)) {
      $line="<a href=\"index.php?p=profilo&amp;u=$r[userName]\">$r[userName]
	 ($r[nTragitti])</a> ";
      $o=$o.$line;
   }
   return $o;
}

/** Controlla se l'utente ha almeno un auto */
function hasAuto() {
   $query = "select idAuto as num from AutoUtenti where idUtente = '".getUserId()."'";
   $res = execQuery($query);
   if ((mysql_num_rows($res) == 0) ) {
      return false;
   }
   return true;
}


function cars_ofUser($userId) {
   $o='<select id="idAuto" name="idAuto">';
   $query = "select Auto.*
      from Auto join AutoUtenti on Auto.ID = AutoUtenti.idAuto
      where AutoUtenti.idUtente = '".getUserId()."'";
   $res = execQuery($query);
   
  while ($row=mysql_fetch_array($res,MYSQL_ASSOC)) {
   
      $auto= $row['marca']." ".$row['modello']." (".$row['targa'].")";

      $o=$o.'<option value="'.$row['ID'].'"
	 selected="selected">'.$auto.'</option>';
   }
   $o=$o.'</select>';

   return $o; 
}

/*
 * Messaggio di benvenuto
 * -Pagina cerca
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

function trips_lastJoined($id) {
   $q = "select Tragitto.*,userName from Tragitto
      join UtentiTragitto on Tragitto.ID = UtentiTragitto.idTragitto
      join Utenti on Tragitto.idPropr = Utenti.ID
      where idUtente ='$id'
      order by `dataPart` desc,`oraPart`";
   $res = execQuery($q);
   if (mysql_num_rows($res) != 0) {
      $out="<ol style=\"margin: 1em; list-style-position:outside\">";
            
      while ($r = mysql_fetch_array($res)) {
	 $piece = <<<TR
<li>
</li>
TR;
   	 $out=$out."<li>".
         printTrip($r).
         "</li>";
      }  
      $out=$out."</ol>";
   } 
	 
   else
      $out="<p>L'utente non ha partecipato ad alcun tragitto.</p>";

   return $out;
}
/*
 * Ultimi tragitti di cui e' proprietario un l'utente con id specificato.
 * -Pagina profilo
 */
function trips_lastOrganized($userName) {
   $q = "select Tragitto.*,userName from Tragitto
      join Utenti on Tragitto.idPropr=Utenti.ID
      where Utenti.userName ='$userName'
      order by `dataPart` desc,`oraPart`";
   $res = execQuery($q);
   
   if (mysql_num_rows($res) != 0) {
      $out="<ol style=\"margin: 1em; list-style-position:outside\">";
            
      while ($r = mysql_fetch_array($res)) {
         $out=$out."<li>".
            printTrip($r).
            "</li>";
      }
      $out=$out."</ol>";
   } 
	 
   else
      $out="<p>L'utente non ha organizzato alcun tragitto.</p>";

   return $out;
}

function users_searchUsername($userName) {
   $q = "select userName from Utenti
      where userName like '%$userName%' limit 10";
   $res = execQuery($q);
   $o="";
   while ($r=mysql_fetch_array($res,MYSQL_ASSOC))
      $o=$o.$line="<a href=\"index.php?p=profilo&amp;u=$r[userName]\">$r[userName]</a><br />";

   if ($o == "")
      return "Nessun utente trovato";

   return $o;
}


/* -------------------
 * TRUSTING / FEEDBACK
 * ------------------- */

function newFeedback ($authorId,$trip,$objectId,$vote,$notes) {
   // Mettere controllo
   $q="insert into Feedback (autore,tragittoAut,valutato,
      tragittoVal,valutazione,data,note)
      values ('$authorId','$trip','$objectId','$trip','$vote',NOW(),'$notes')";
   execQuery($q);
}

/*
 * Restituisce i percorsi per cui si puo' scrivere un
 * feedback.
 */
function feedback ($targetUserId) {
   if ($targetUserId == getUserId())
      return null;

   $q="select Tragitto.*,Utenti.userName
      from UtentiTragitto
      join Tragitto on UtentiTragitto.idTragitto=Tragitto.ID
      join Utenti on Utenti.ID = Tragitto.idPropr
      where 
         (UtentiTragitto.idUtente = '$targetUserId'
         or Tragitto.idPropr = '$targetUserId') and
         (UtentiTragitto.idUtente = '".getUserId()."'
         or Tragitto.idPropr = '".getUserId()."')
         and dataPart<now()
      limit 5";
   $res=execQuery($q);
   
   if (mysql_num_rows($res) == 0)
      return null;

   $feedback="";
   while ($r2=mysql_fetch_array($res,MYSQL_ASSOC)) {
      $feedback=$feedback.printTrip($r2);
   }

   return "<div class=\"bgGold little\">
      <h4>Valuta $r1[userName]</h4>".$feedback."</div>";
}

/*
 * Affidabilita' dell'utente
 */
function trust ($id) {
   $q = "select valutato,avg(valutazione) as votoMedio,count(*) as nVoti
      from Feedback where valutato=$id group by valutato";

   $res=execQuery($q);

   if (mysql_num_rows($res) == 0)
      return "Nessuna valutazione";

   $r=mysql_fetch_array($res);

   return $r['votoMedio']." (".$r["nVoti"]." voti)";
}

function viewVotes ($id) {
   $q = "select userName,valutazione,note
      from Feedback
      join Utenti on valutato = Utenti.ID
      where autore=".getUserId()."
      and tragittoAut=$_GET[idTrip]";
   $res=execQuery($q);

   if (mysql_num_rows($res) == 0)
      return null;
   
   $o="<br />Hai gi&agrave; valutato<br />
      <ul class=\"disco\">";
   while ($r=mysql_fetch_array($res)) {
      $o=$o."<li>".$r['userName']." Voto ".$r['valutazione']." ".
      $r['note']."</li>";
   }
   return $o."</ul>";
}

?>
