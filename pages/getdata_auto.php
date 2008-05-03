<?php
include("db_interface.php");

header( "content-type: text/xml" );

$q = "select *
   from Auto
   where ID='".$_POST[idAuto]."'";
   $r=mysql_fetch_array(execQuery($q));

   $data=explode("-",$r[annoImmatr]);
?>

<data>
<id><?php echo $r[ID]; ?></id>
   <targa><?php echo $r[targa]; ?></targa>
   <marca><?php echo $r[marca]; ?></marca>
   <modello><?php echo $r[modello]; ?></modello>
   <cilindrata><?php echo $r[cilindrata]; ?></cilindrata>
   <giorno.><?php echo $data[2]; ?></giorno.>
   <mese.><?php echo $data[1]; ?></mese.>
   <anno.><?php echo $data[0]; ?></anno.>
   <condizioni><?php echo $r[condizioni]; ?></condizioni>
   <note><?php echo $r[note]; ?></note>
</data>
