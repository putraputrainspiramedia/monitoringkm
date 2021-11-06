<?
include("./inc/conn.php");
ini_set("mssql.textlimit",2147483647);
ini_set("mssql.textsize",2147483647);

$qry = mssql_query("select foto from fkaryawan..E_FotoKry where nik = '".$_REQUEST['nik']."'",$conn_ho);
#$dt = mssql_fetch_array($qry);
#$foto = $dt['foto'];
#$row = mssql_fetch_assoc($qry);
#header("Content-type: image/jpeg;");
#echo $row['foto'];


$content=mssql_result($qry, 0, 0);
header('Content-type: image/jpeg');
echo $content; 

?>