<?
session_start();
setlocale(LC_TIME,'ita','it_IT','it_IT.utf8');

include("./pages/db_interface.php");
include("./pages/functions.php");


// Gestione login, logout e registrazioni
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

$output = implode ("",file("template/index.htm"));

// Preparo i contenuti dinamici
$h = headType();
$b = bodyType();
$m = menu();
$c = content();

// li sostituisco nell'html statico
$search = array (
   "<!-- MENU -->",
   "<!-- CONTENT -->",
   "<!-- HEADMAP -->",
   "<body>");

$replace = array (
   menu(),
   content(),
   headType(),
   bodyType());

# Verifica se si deve inizializzare la GMap
$output = str_replace ($search,$replace,$output);

// infine stampo
echo $output;

if ($db_conn)
   mysql_close ($db_conn);
?>
