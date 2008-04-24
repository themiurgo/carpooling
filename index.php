<?
session_start();
setlocale(LC_TIME,'ita','it_IT','it_IT.utf8');

include("./pages/db_interface.php");
include("./pages/functions.php");

// Gestione login
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
      registraAuto();
      break;

   case "registerTrip":
      registraTragitto();
      break;

   default:
      break;
}

$output = implode ("",file("template/index.htm"));

// Preparo i contenuti dinamici
$h = headType();
$b = bodyType();
$m = menu();
$c = content();

// li sostituisco nell'html statico
$output = eregi_replace ("<!-- MENU -->", $m ,$output);
$output = eregi_replace ("<!-- CONTENT -->", $c ,$output);
// li sostituisco nell'html statico
# Verifica se è il caso di importare la GMap
$output = eregi_replace ("<!-- HEADMAP -->", $h ,$output);

# Verifica se si deve inizializzare la GMap
$output = eregi_replace ("<!-- BODYMAP -->", $b ,$output);

// infine stampo
echo $output;

if ($db_conn)
   mysql_close ($db_conn);
?>
