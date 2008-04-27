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
function registraUtente(){
    
    $dataNascita=$_POST['aNascita']."-".$_POST['mNascita']."-".$_POST['gNascita'];
    $dataPatente=$_POST['aPatente']."-".$_POST['mPatente']."-".$_POST['gPatente'];
    
    # Data corrente per l'iscrizione
    $today = getdate(); 
    $dataIscriz=$today['year']."-".$today['mon']."-".$today['mday'];
    
    $registerUser_query = "insert into 
        Utenti(userName,psw,nome,cognome,dataNascita,email,dataPatente,fumatore,dataIscriz,localita) 
        values('".$_POST['user']."','".$_POST['psw']."','".$_POST['nome']."','".$_POST['cognome']."',
        '$dataNascita','".$_POST['mail']."','$dataPatente',".$_POST['fumatore'].",'$dataIscriz','".$_POST['citta']."')";
    
    #if ( execQuery($registerUser_query) )
    #echo "La query è stata eseguita correttamente";
    #else
    #echo "Errore durante l'inserimento".mysql_error();
    
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
 * Registrazione di un auto al sito
 */
function registraAuto() {
    
    $annoImm = $_POST['aAuto']."-".$_POST['mAuto']."-".$_POST['gAuto'];
   
    # Registrazione nella tabella Auto
    $registerAuto_query1 = "insert into Auto(targa,marca,modello,cilindrata,annoImmatr,condizioni,note) 
    values ('".$_POST['targa']."','".$_POST['marca']."','".$_POST['modello']."',
            ".$_POST['cilindrata'].",'$annoImm',".$_POST['voto'].",'".$_POST['noteAuto']."')";

    execQuery($registerAuto_query1);
    
    # Ottiene l'id dell'auto :Si potrebbe ottimizzare
    $query="select ID from Auto where targa='".$_POST['targa']."'";
    $res=execQuery($query);
    $row=mysql_fetch_array($res); 
    
    # Questo flag indica il fatto che chi registra l'auto ne è anche il proprietario
    $prop = true;
    
    #Registrazione nella tabella AutoUtenti
    $registerAuto_query2 = "insert into AutoUtenti(idAuto,idUtente,valido) values('".$row['ID']."','".getUserId()."',$prop)";
    
    execQuery($registerAuto_query2);
    
}


/*
 * Registrazione di un Tragitto ( Trip ) al sito
 */
function registraTragitto() {

    # ATTENZIONE : $_POST['targa'] è il campo di un HIDDEN all'interno del form
    
   $data = $_POST['aPartenza']."-".$_POST['mPartenza']."-".$_POST['gPartenza'];
   
   # ATTENZIONE : $_POST['targa'] è il campo di un HIDDEN all'interno del form
   $targa = $datiTragitto['targa'];

    # Ottiene l'id dell'auto :Si potrebbe ottimizzare
    $query="select ID from Auto where targa='".$_POST['targa']."'";
    $res=execQuery($query);
    $row=mysql_fetch_array($res); 

    $registerTrip_query1 = "insert into  
    Percorso(idProprietario,idAuto,partenza,destinazione,dataPart,oraPart,durata,
                fumo,musica,numPostiDisp,spese,note) 
    values('".$_SESSION['userID']."',".$row['ID'].",'".$_POST['partenzaText']."',
            '".$_POST['arrivoText']."','$data','".$_POST['oraPartenza']."','".$_POST['durata']."',
            ".$_POST['fumatore'].",".$_POST['musica'].",".$_POST['passeggeri'].",".$_POST['spese'].",'".$_POST['note']."')";
    
    execQuery($registerTrip_query1);
    
    # Ottiene l'id del percorso :Si potrebbe ottimizzare
    $tripID_query = "select max(ID) as max from Tragitto";
    $res = execQuery($tripID_query);
    $row = mysql_fetch_array($res); 
   
    $registerTrip_query2 = "insert into UtentiTragitto(idPercorso,idUtente) 
      values('".$row['max']."','".$_SESSION['userID']."')";
    
    execQuery($registerTrip_query2);
} 


function partecipaTragitto(){
      $postiRes = $_GET['posti'] - 1;
      echo $idTrip;
      
      $join_query = "insert into utentitragitto(idUtente,idTragitto) values('".$_SESSION['userId']."','".$_GET['idTrip']."')";
      
      execQuery($join_query);
      
      $join_query2 = "update tragitto set postiDisp=$postiRes where ID='".$_GET['idTrip']."'";
      
   execQuery($join_query2);

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

function search_username() {
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
   echo "ciao";
   return $output;
}
?>
