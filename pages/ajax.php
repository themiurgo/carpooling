<?
include("./db_interface.php");
include("./functions.php");

if (isset ($_GET['pa']) ) {
   $q1="select count(*) as numrows from Tragitto
      where partenza='".$_GET['pa']."'
      and destinaz='".$_GET['ar']."'
      and postiDisp>0";
   $r1=mysql_fetch_array(execQuery($q1));
   $numrows=$r1[numrows];
   $maxPage  = ceil($numrows/5);

   $trip_query = "select Tragitto.*,userName from Tragitto
      join Utenti on Tragitto.idPropr=Utenti.ID
      where partenza='".$_GET['pa']."'
      and destinaz='".$_GET['ar']."'
      and postiDisp>0
      order by `dataPart` desc,`oraPart`
      limit ".$_GET['count'].",5";
   $res = execQuery($trip_query);
   if (mysql_num_rows($res)==0)
      echo "Nessun tragitto trovato";
   else
      $out="Pagina $prev di $maxPage<br />".$out."</ol>";

   $out=$out."<ol>";
   while ($r = mysql_fetch_array($res)) {
      $data = parseDate($r[oraPart]." ".$r[dataPart]);
      $r[fumo] ? $r[fumo]="Si" : $r[fumo]="No";
      $r[musica] ? $r[musica]="Si" : $r[musica]="No";
      $piece = <<<TRIP
   <li>
      <a href="index.php?p=tragitto&idTrip=$r[ID]">$data</a>
      (<a href="index.php?p=profilo&u=$r[idPropr]">$r[userName]</a>)
      <br />$r[postiDisp] posti - $r[fumo] fumo - $r[musica] musica <p>
   </li>
TRIP;
      $out=$out.$piece;
   }
   $prev = $_GET['count']+1;
   $next = $prev-2;
   if ($next>=0) 
      $out = $out."
         <button type=\"button\" onClick=\"risultatiAjax($next);\">&lt;</button>";

   if ($prev<$maxPage)
      $out = $out."
         <button type=\"button\" onClick=\"risultatiAjax($prev)\">&gt;</button>";

   echo $out;

}

if (isset($_GET['q'])) {
   switch ($_GET['q']) {
      case "search_username":
	 echo users_searchUsername($_GET['username']);

	 break;

      default:

   }
}

?>
