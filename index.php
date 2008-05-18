<?
session_start();
setlocale(LC_TIME,'ita','it_IT','it_IT.utf8');

include("./pages/db_interface.php");
include("./pages/functions.php");

// Gestisco evenutali azioni di login/logout/registrazione,...
handle_action();

$template = implode ("",file("template/index.htm"));
echo parseTemplate($template);

// Chiudo le connessioni
if ($db_conn)
   mysql_close ($db_conn);
?>
