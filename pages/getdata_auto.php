<?php
include("db_interface.php");

header( "content-type: text/xml" );

$q = "select *
   from Auto
   where ID='".$_POST[idAuto]."'";
   $r=mysql_fetch_array(execQuery($q));

?>
<data>
<id><?php echo $r[ID]; ?></id>
   <targa><?php echo $r[targa]; ?></targa>
   <marca><?php echo $r[marca]; ?></marca>
   <modello><?php echo $r[modello]; ?></modello>
   <cilindrata><?php echo $r[cilindrata]; ?></cilindrata>
   <annoImmatr><?php echo $r[annoImmatr]; ?></annoImmatr>
   <condizioni><?php echo $r[condizioni]; ?></condizioni>
   <note><?php echo $r[note]; ?></note>
</data>
