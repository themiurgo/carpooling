<?
include("./db_interface.php");

if (isset ($_GET['pa']) ) {

   $trip_query = "select * from Tragitto
      where partenza='".$_GET['pa']."'
      and destinaz='".$_GET['ar']."'
      and postiDisp>0
      order by `dataPart` desc,`oraPart`";
   $res = execQuery($trip_query);
   if ($res)
      echo "Elenco dei tragitti disponibili";

   $out="<ol>";

   while ($row = mysql_fetch_array($res)) {
     
      $pro = $row['idPropr'];
      $name_query="select userName from Utenti where ID=$pro";
      $res_name = execQuery($name_query);
      $row_name = mysql_fetch_array($res_name);
      $nome = $row_name['userName'];
      $disp = $row['postiDisp'];
      $ora = $row['oraPart'];
      $data = $row['dataPart'];
      $fumo = $row['fumo'];
      $idTrip = $row['ID'];
	 
	 $piece = <<<TRIP
   <li>
      <a href="index.php?p=tragitti&idTrip=$idTrip">$ora  $data</a>
      <br />$disp posti - Fumo :$fumo ($nome) <p>
   </li>
TRIP;
	 $out=$out.$piece;
   }
   $out=$out."</ol>";
   echo $out;
}

?>
