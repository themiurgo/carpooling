<?php

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
      
      # Attenzione al case-sensitive
      $_SESSION['userID'] = $a['ID'];
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
 
 
# Questa Funzione consente di modificare gli attributi di <head> qualora fossero necessarie
# operazioni particolari, ad es. il caricamento iniziale della GMap.
function headType() {
	
   if ( isMapPage() ) {
        # Queste istruzioni consentono di inviare la API KEY al server di Google.
        return "<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAApM5Cuio981ky_h5rXnr3uhT2yXp_ZAY8_ufC3CFXhHIE1NvwkxRczD8EFDGHM7KVLNJB1qP52_6uEg' type='text/javascript'>
            </script>";
    } else {
        
        # Nella pagina non è presente la GMap
        return "";
    }
	
}


# Questa Funzione consente di modificare gli attributi di <body> qualora fossero necessarie
# operazioni particolari, ad es. il caricamento iniziale della GMap.
function bodyType() {
	
    if ( isMapPage() ) {
                
        # Queste istruzioni consentono di centrare la mappa nella localita' preferita,
        # specificata in fase di registrazione
        if ( isset($_SESSION['user']) ) {
            
            $location_query = "select localita from Utenti where ID='".$_SESSION['userID']."' "; 
            $res = execQuery($location_query);
            $row = mysql_fetch_array($res);
            #print mysql_num_rows($res);
            return "<body onload=\"creaMappa('".$row['localita']."')\" onunload='GUnload()'> ";
        } else {
            # Centro di default della Mappa
            $default = 'Catania';
            return "<body onload=\"creaMappa('$default')\" onunload='GUnload()'> ";
        }
	} else {
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
      return <<<END
	 <a href="#" onclick="loginScript()">Login</a>&nbsp;&middot;
	 <a href="index.php?p=iscrizione">Iscriviti</a>&nbsp;&middot;
	 <a href="index.php?p=tragitti">Tragitti</a>&nbsp;&middot;
	 <a href="index.php?p=cerca">Cerca Tragitto</a>&nbsp;&middot;
	 <a href="index.php?p=utenti">Utenti</a>&nbsp;&middot;
	 <a href="index.php?p=about">About Us</a>&nbsp;&middot;
END;
   }
    
   # Utente LOGGATO
   else {
      $Utente = getUser();
      return <<<END
      <b>$Utente</b> - 
      <a href="index.php?p=profilo">Profilo</a>&nbsp;&middot;
      <a href="index.php?p=tragitti">Tragitti</a>&nbsp;&middot;
      <a href="index.php?p=cerca">Cerca Tragitto</a>&nbsp;&middot;
      <a href="index.php?p=nuovo">Nuovo Tragitto</a>&nbsp;&middot;
      <a href="index.php?p=auto">Auto</a>&nbsp;&middot;
      <a href="index.php?p=utenti">Utenti</a>&nbsp;&middot;
      <a href="index.php?p=about">About Us</a>&nbsp;&middot;
      <a href="index.php?action=logout">Logout</a>
END;
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

   // Lista delle pagine consentite
   $allowed = array("login","iscrizione","cerca","nuovo","utenti",
      "tragitti","profilo","about","auto","signUp");

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
   }

    # Ottieni il corretto contenuto da 'sostituire' all'interno della pagina
   $content = $content.prepare_content($file_content);
   return $content;
}


/* 
 * Effettua le opportuni sostituzioni di commenti html
 * con variabili PHP, a seconda della pagina richiesta.
 * '$a' rappresenta il contenuto del template della pagina corrente
 */
function prepare_content ($a) {

   # Il contenuto è diverso a seconda della pagina selezionata.
   switch ($_GET['p']) {
        
      # Pagina con la tabella dei Tragitti    
      case 'tragitti':
            
 	 # Estrazione dei tragitti
	 $trips_query = "select Tragitto.*, userName from `Tragitto`
   	    join Utenti on idPropr=Utenti.ID
	    order by `dataPart` desc,`oraPart` desc limit 5";
	 $res = execQuery($trips_query);
            
            while ($row = mysql_fetch_array($res)) {	       
                
		# La prima chiamata ha come parametro '$a'
	       $o = eregi_replace("<!-- PROPRIETARIO -->", $row['userName'],$a);
	       $o = eregi_replace("<!-- NPDISP -->", $row['numPostiDisp'],$o);
	       $o = eregi_replace("<!-- PARTENZA -->", $row['partenza'],$o);
	       $o = eregi_replace("<!-- DESTINAZ -->", $row['destinaz'],$o);
	       $o = eregi_replace("<!-- ORA -->",$row['dataPart']." ".$row['oraPart'],$o);
	       $o = eregi_replace("<!-- DURATA -->", $row['durata'],$o);
	       $o = eregi_replace("<!-- FUMO -->", $row['fumo'],$o);
	       $o = eregi_replace("<!-- MUSICA -->", $row['musica'],$o);
                
       	       $final_content = $final_content.$o;
	    }
	    break;

      case 'profilo':
         if (!isset($_GET['u']))
            $_GET['u']=getUser();

         $query="select *,UNIX_TIMESTAMP(`dataIscriz`) from `Utenti`
            where `userName`='".$_GET['u']."'";
         $res=execQuery($query);
         $row=mysql_fetch_array($res,MYSQL_ASSOC);
        
         if (getUser()!=$_GET['u']) {
            $feedback=feedback($row['ID']);
         }

         $search = array ("/{userName}/",
            "/{nome}/",
            "/{cognome}/",
            "/{eta}/",
            "/{email}/",
            "/{dataPatente}/",
            "/{localita}/",
            "/{dataIscriz}/",
            "/{feedback}/");

         $replace = array ($row['userName'],
            $row['nome'],
            $row['cognome'],
            age($row['dataNascita']),
            $row['email'],
            age($row['dataPatente']),
            $row['localita'],
            strftime("%e %B %Y",
               $row['UNIX_TIMESTAMP(`dataIscriz`)']),
            $feedback);

         ksort($search);
         ksort($replace);
         
         $final_content = preg_replace($search, $replace, $a);

         break;
	    
        
        # Nuovo Tragitto e inserimento Auto
      case 'nuovo':
      case 'auto':   
            
      # Estrai le informazioni sulle eventuali auto registrate dall' utente corrente
   	 $auto_query = "select targa,marca,modello from Auto join AutoUtenti on Auto.id=AutoUtenti.idAuto where AutoUtenti.idUtente='".$_SESSION['userID']."'"; 
	 $res = execQuery($auto_query);
	 
            # L' utente corrente non ha alcuna auto registrata
	 if ( ( mysql_num_rows($res) == 0) ) {
	    if ( $_GET['p'] == "nuovo" ) {
                
                    # La prima chiamata ha come parametro '$a'
	       $o = eregi_replace("<!-- AUTOS -->","Non hai auto! Provvedi subito a 
                        <a href='index.php?p=auto'>registrarne</a> una.",$a); 
	    }
	       
	    elseif ( $_GET['p'] == "auto" ) {
	       
	    # La prima chiamata ha come parametro '$a'
      	    $o = eregi_replace("<!-- REGISTEREDAUTOS -->","Attualmente, non hai nessuna auto registrata",$a); 
	    }
                
	 $final_content = $final_content.$o;
            
	 # L' utente ha almeno un'auto
	 } 
	       
	       else {
		  $row = mysql_fetch_array($res);
                
		  # Bisogna ancora prevedere il caso in cui il proprietario abbia più di una macchina.
                $auto1= $row['marca']." - ".$row['modello']." - ".$row['targa'];
                
                #Andrà a finire in un campo hidden, per facilitare query successive.
                $targa=$row['targa'];
            
                # Pagina nuovo tragitto --> viene visualizzato l' oggetto selection
                if ($_GET['p']=="nuovo") {
                
                    # La prima chiamata ha come parametro '$a'
                    $o = eregi_replace("<!-- AUTOS -->","<select id='selectAuto' name='selectAuto' > 
                    <option value='auto1' selected='selected'>'$auto1'</option></select>
                    <input type='hidden' name='targa' value='$targa'>",$a);
             
             
                # Pagina auto --> viene visualizzata la Selection insieme ad un form per 
                # dare la possibilità di modificare i dati dell' auto
                } elseif ($_GET['p']=="auto") {
                
                    # La prima chiamata ha come parametro '$a'
                    $o = eregi_replace("<!-- REGISTEREDAUTOS -->",<<<END
                        Elenco delle auto gi&agrave; registrate : 
                        <form class="registrazione" onsubmit="" method="post">
                            <select id='selectAuto' name='selectAuto' > 
                            <option value='auto1' selected='selected'>$auto1</option></select>
                            <input type="hidden" name="targa" value="$targa">
                            <input id="modifyAutoButton" type="button" value="Modifica" onclick="disableText()"/>
                        </form>		
END

                        ,$a); 
                }
                $final_content = $final_content.$o;
            }
            break;
        default:
        
            # Nulla da sostituire
            $final_content = $final_content.$a;
            break;
    }
   
    # Restituisce il contenuto
    return $final_content;
}




/* ---------------------
 * FUNZIONI DI SERVIZIO
 * --------------------- */
 
 /*
 * Restituisce gli anni di distanza da una data nel formato
 * YYYY-MM-DD.
 */
function age ($birthday) {
   list($year,$month,$day) = explode("-",$birthday);
   $year_diff  = date("Y") - $year;
   $month_diff = date("m") - $month;
   $day_diff   = date("d") - $day;
   if ($day_diff < 0 || $month_diff < 0)
      $year_diff--;
   
   return $year_diff;
}


/*
 * Affidabilita' dell'utente
 */
function trust ($username) {
   return "molto affidabile";
}


/*
 * Determina se una pagina deve creare un GMap
 */
function isMapPage() { 
   # Il caricamento della GMap e' richiesto per le pagine 'nuovo
   # tragitto' e 'cerca tragitto'

   if ( ($_GET['p'] == 'nuovo') || ($_GET['p'] == 'cerca') ) {
      return true;
   }
    
   return false;
}

?>
