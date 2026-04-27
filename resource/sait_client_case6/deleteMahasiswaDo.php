<?php
include_once 'config.php';
$id_mhs = $_GET['id_mhs'];

//delete data di database local
$sql = "delete from mahasiswa where id_mhs=$id_mhs";
if (mysqli_query($link, $sql)) {
   echo "<center>Record has been deleted successfully in local database!<br>";
} else {
   echo "Error: " . $sql . ":-" . mysqli_error($conn);
}
mysqli_close($link);


// delete data di OS ubuntu
//Pastikan sesuai dengan alamat endpoint dari REST API di ubuntu
$url='http://10.33.35.96/sait_api/mahasiswa_api.php?id_mhs='.$id_mhs.'';


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
// pastikan method nya adalah delete
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
$result = json_decode($result, true);

curl_close($ch);

//var_dump($result);
// tampilkan return statusnya, apakah sukses atau tidak
print("<center><br>status :  {$result["status"]} "); 
print("<br>");
print("message :  {$result["message"]} "); 
 //
echo "<br>Sukses menghapus data di ubuntu server !";
echo "<br><a href=mahasiswa.php> OK </a>";

?>