<?
include("./db_interface.php");

if (isset ($_GET['pa']) ) {

    $trip_query = "select idPropr,postiDisp,oraPart,dataPart,fumo from tragitto where 
                        partenza='".$_GET['pa']."' and destinaz='".$_GET['ar']."' order by `dataPart` desc,`oraPart`";
    $res = execQuery($trip_query);
    if ($res) {echo "Elenco dei tragitti disponibili";} 
    $out="";
    $counter=0;
    while ($row = mysql_fetch_array($res)) {
        
        $pro = $row['idPropr'];
        $name_query="select userName from utenti where ID=$pro";
        $res_name = execQuery($name_query);
        $row_name = mysql_fetch_array($res_name);
        $nome = $row_name['userName'];
        $disp = $row['postiDisp'];
        $ora = $row['oraPart'];
        $data = $row['dataPart'];
        $fumo = $row['fumo'];
        $counter++;
        
        $piece = <<<TRIP
            <p>Tragitto #$counter <br>Creato da: $nome    Posti: $disp <br>  Data : $ora  $data   Fumo :$fumo <p>   
TRIP;
        $out=$out.$piece;    
    }
    echo $out;
}



?>