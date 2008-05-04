<?php
 
/* ---------------------
 * IMPOSTAZIONI DATABASE
 * --------------------- */
$db_host="localhost";
$db_usr="carpooler";
$db_psw="";
$db_name="Carpooling";
$db_conn=null;

/*
 * Connetti al server MySQL e Seleziona il DB
 *
 */
function connectDb (&$dbconn) {
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
function execQuery ($query) {
   global $db_conn;

   if (!$db_conn)
      connectDb($db_conn);

   return mysql_query($query, $db_conn);
}

/*
 * Registrazione di un utente al sito
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
      
      execQuery($registerUser_query)  ;
    
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
 * Registrazione e modifica di un auto
 */
function gestioneAuto() {
   
   #Il campo hidden ci comunica che l'operazione è di registrazione auto;
   if ( $_POST['mecha'] == "new" ) {
      
      # Registrazione nella tabella Auto
    $q1 = "insert into Auto(targa,marca,modello,cilindrata,annoImmatr,condizioni,note) 
    values ('".$_POST['targa']."','".$_POST['marca']."','".$_POST['modello']."',
            ".$_POST['cilindrata'].",'".$_POST['annoImmatr']."',".$_POST['condizioni'].",'".$_POST['note']."')";

    execQuery($q1) or die("Query non valida1: " . mysql_error());
    
    # Ottiene l'id dell'auto:Si potrebbe ottimizzare
    $query="select ID from Auto where targa='".$_POST['targa']."'";
    $res=execQuery($query);
    $row=mysql_fetch_array($res); 
    
    # Questo flag indica il fatto che chi registra l'auto ne è anche il proprietario
    $prop = true;
    
    #Registrazione nella tabella AutoUtenti
    $registerAuto_query2 = "insert into AutoUtenti(idAuto,idUtente,valido) values('".$row['ID']."','".getUserId()."',$prop)";
    
    execQuery($registerAuto_query2) or die("Query non valida2: " . mysql_error());
    } 
   #Il campo hidden dichiara che è un'operazione di aggiornamento 
    else if ( $_POST['mecha'] == "update" ) {
     
      $q2="update auto set note='".$_POST['note']."',condizioni= '".$_POST['condizioni']."' where ID='".$_POST['idAuto']."'";
      execQuery($q2) or die("Query non validaU: " . mysql_error());
    
    }
}


function aggiornaProfilo() {
 
   $q1 = "update utenti set userName='".$_POST['userName']."',email='".$_POST['email']."',localita='".$_POST['localita']."' ,fumatore='".$_POST['fumatore']."' where ID='".getUserId()."'  ";
   execQuery($q1) or die("Query non valida1: " . mysql_error());

}

/*
 * Registrazione di un Tragitto ( Trip ) al sito
 * Data deve essere nel formato YYYY-MM-GG.
 */
function registerTrip() {
 if ( controllaData() ) {
   $dataPart =$_POST['anno']."-".$_POST['mese']."-".$_POST['giorno'];
   $oraPart = $_POST['ora'].":".$_POST['minuti'];
   $durata = $_POST['durataOre'].":".$_POST['durataMinuti'];
   $q1 = "insert into  
      Tragitto(idPropr,idAuto,partenza,destinaz,dataPart,
	 oraPart,durata,fumo,musica,postiDisp,spese,note)
      values('".getUserId()."','".$_POST['idAuto']."',
	 '".$_POST['partenza']."','".$_POST['destinaz']."','$dataPart',
	 '".$oraPart."','".$durata."',
	 ".$_POST['fumo'].",".$_POST['musica'].",
	 ".$_POST['postiDisp'].",".$_POST['spese'].",
	 '".$_POST['note']."')";
    
    echo $q1;
    execQuery($q1) or die("Query non valida1: " . mysql_error());
    
    $registerTrip_query = "insert 
      into UtentiTragitto(idTragitto,idUtente) 
      values('".mysql_insert_id()."','".getUserId()."')";
    
    execQuery($registerTrip_query) or die("Query non valida2: " . mysql_error());
    
    } else {
      echo "la data di partenza è nel passato";
    }
    
    
} 


function partecipaTragitto(){
   
   #Controllo se l'utente si può aggiungere
   if ( $check = controllaTrip() ) {
      
      #Decremento i posti disponibili
      $postiRes =  $_GET['posti'] - 1;
      echo $postiRes;
      $join_query = "insert into utentitragitto(idUtente,idTragitto) values('".$_SESSION['userId']."','".$_GET['idTrip']."')";
      execQuery($join_query);
      $join_query2 = "update tragitto set postiDisp=$postiRes where ID='".$_GET['idTrip']."'";
      execQuery($join_query2);
   }

}


function bloccaTragitto() {
   #Controllo contro i furbacchioni. 'sec' sta per security
   $sec_query="select idPropr from tragitto where ID='".$_GET['idTrip']."'";
    $res = execQuery($sec_query);
   $row = mysql_fetch_array($res);
   
   if ( $row['idPropr']== $_SESSION['userId'] ) {
      $block_query=" update tragitto set postiDisp=0 where ID='".$_GET['idTrip']."'";
      execQuery($block_query);
   } else {
      echo "Errore, furbetto!";
   
   }



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

function controllaData() {
   
   #Data Completa della partenza
   //echo $_POST['ora'].$_POST['minuti'].$_POST['mese'].$_POST['giorno'].$_POST['anno'];
   $start = mktime($_POST['ora'],$_POST['minuti'],0,$_POST['mese'],$_POST['giorno'],$_POST['anno']);
  
   $t = getdate(); 
   $now =mktime($t['hours'],$t['minutes'],0,$t['mon'],$t['mday'],$t['year']);

   $diff = $start - $now ;
   echo $diff;
   if  ( $diff < 0 ) {
      return false;
   }
   
   return true;
}

	
function users_recentSignup () {
   $query = "select userName from Utenti order by dataIscriz desc limit 5"; 
   $res = execQuery($query);
   while ($row=mysql_fetch_array($res,MYSQL_ASSOC)) {
      $line='<a href="">'.$row['userName'].'</a><br />';
      $output=$output.$line;
   }
   return $output;
}

function users_mostActive() {
   $query = "select userName,count(*) as nTragitti
      from Utenti join Tragitto on Utenti.ID = Tragitto.idPropr
      group by Tragitto.idPropr
      order by nTragitti desc limit 5"; 
   $res = execQuery($query);
   while ($row=mysql_fetch_array($res,MYSQL_ASSOC)) {
      $line='<a href="">'.$row['userName'].'</a>
         ('.$row['nTragitti'].'tragitti)<br />';
      $output=$output.$line;
   }
   return $output;
}

function search_userName($userName) {
   $query = "select userName
      from Utenti
      where userName=$userName";
   $res = execQuery($query);

   while ($row=mysql_fetch_array($res,MYSQL_ASSOC)) {
      $line='<a href="">'.$row['userName'].'</a><br />';
      $output=$output.$line;
   }

   return $output;
}

function cars_ofUser($userId) {
   /*<label for="selectAuto">Auto</label>*/

   $output='<select id="idAuto" name="idAuto">';
   $query = "select Auto.*
      from Auto join AutoUtenti on Auto.ID = AutoUtenti.idAuto
      where AutoUtenti.idUtente = '".getUserId()."'";
   $res = execQuery($query);
   
   while ($row=mysql_fetch_array($res,MYSQL_ASSOC)) {
      $auto= $row['marca']." ".$row['modello']." (".$row['targa'].")";

      $output=$output.'<option value="'.$row['ID'].'"
	 selected="selected">'.$auto.'</option>';
   }
   $output=$output.'</select>';

   return $output; 
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

/*
 * Ultimi tragitti di cui e' proprietario un l'utente con id specificato.
 * -Pagina profilo
 */
function trips_lastOrganized($id) {
   $q = "select * from Tragitto
      where idPropr ='$id'
      order by `dataPart` desc,`oraPart`";

   $res = execQuery($q);
   
   if (mysql_num_rows($res) != 0) {
      $out="<ol style=\"margin: 1em; list-style-position:outside\">";
            
      while ($r = mysql_fetch_array($res)) {
	 $piece = <<<TR
<li>
   <a href="index.php?p=tragitti&idTrip=$r[ID]">
      $r[oraPart] $r[dataPart]
   </a><br />
   Da <b>$r[partenza]</b> a <b>$r[destinaz]</b>
</li>
TR;
   	 $out=$out.$piece;
      }  
      $out=$out."</ol>";
   } 
	 
   else
      $out="<p>Non hai creato alcun tragitto finora</p>";

   return $out;
}

?>
