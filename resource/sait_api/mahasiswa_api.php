<?php
require_once "config.php";
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
   case 'GET':
         if(!empty($_GET["id_mhs"]))
         {
            $id=intval($_GET["id_mhs"]);
            get_mhs($id);
         }
         else
         {
            get_mhss();
         }
         break;
   case 'POST':
         if(!empty($_GET["id_mhs"]))
         {
            $id=intval($_GET["id_mhs"]);
            update_mhs($id);
         }
         else
         {
            insert_mhs();
         }     
         break; 
   case 'DELETE':
          $id=intval($_GET["id_mhs"]);
            delete_mhs($id);
            break;
   default:
      // Invalid Request Method
         header("HTTP/1.0 405 Method Not Allowed");
         break;
      break;
 }



   function get_mhss()
   {
      global $mysqli;
      $query="SELECT * FROM mahasiswa";
      $data=array();
      $result=$mysqli->query($query);
      while($row=mysqli_fetch_object($result))
      {
         $data[]=$row;
      }
      $response=array(
                     'status' => 1,
                     'message' =>'Get List Mahasiswa Successfully.',
                     'data' => $data
                  );
      header('Content-Type: application/json');
      echo json_encode($response);
   }
 
   function get_mhs($id=0)
   {
      global $mysqli;
      $query="SELECT * FROM mahasiswa";
      if($id != 0)
      {
         $query.=" WHERE id_mhs=".$id." LIMIT 1";
      }
      $data=array();
      $result=$mysqli->query($query);
      while($row=mysqli_fetch_object($result))
      {
         $data[]=$row;
      }
      $response=array(
                     'status' => 1,
                     'message' =>'Get Mahasiswa Successfully.',
                     'data' => $data
                  );
      header('Content-Type: application/json');
      echo json_encode($response);
        
   }
 
   function insert_mhs()
      {
         global $mysqli;
         if(!empty($_POST["nama"])){
            $data=$_POST;
         }else{
            $data = json_decode(file_get_contents('php://input'), true);
         }

         $arrcheckpost = array('nama' => '','alamat' => '');
         $hitung = count(array_intersect_key($data, $arrcheckpost));
         if($hitung == count($arrcheckpost)){
          
               $result = mysqli_query($mysqli, "INSERT INTO mahasiswa SET
               nama = '$data[nama]',
               alamat = '$data[alamat]'");                
               if($result)
               {
                  $response=array(
                     'status' => 1,
                     'message' =>'Mahasiswa Added Successfully.'
                  );
               }
               else
               {
                  $response=array(
                     'status' => 0,
                     'message' =>'Mahasiswa Addition Failed.'
                  );
               }
         }else{
            $response=array(
                     'status' => 0,
                     'message' =>'Parameter Do Not Match'
                  );
         }
         header('Content-Type: application/json');
         echo json_encode($response);
      }
 
   function update_mhs($id)
      {
         global $mysqli;
         if(!empty($_POST["nama"])){
            $data=$_POST;
         }else{
            $data = json_decode(file_get_contents('php://input'), true);
         }

         $arrcheckpost = array('nama' => '','alamat' => '');
         $hitung = count(array_intersect_key($data, $arrcheckpost));
         if($hitung == count($arrcheckpost)){
          
              $result = mysqli_query($mysqli, "UPDATE mahasiswa SET
              nama = '$data[nama]',
              alamat = '$data[alamat]'
              WHERE id_mhs='$id'");
          
            if($result)
            {
               $response=array(
                  'status' => 1,
                  'message' =>'Mahasiswa Updated Successfully.'
               );
            }
            else
            {
               $response=array(
                  'status' => 0,
                  'message' =>'Mahasiswa Updation Failed.'
               );
            }
         }else{
            $response=array(
                     'status' => 0,
                     'message' =>'Parameter Do Not Match'
                  );
         }
         header('Content-Type: application/json');
         echo json_encode($response);
      }
 
   function delete_mhs($id)
   {
      global $mysqli;
      $query="DELETE FROM mahasiswa WHERE id_mhs=".$id;
      if(mysqli_query($mysqli, $query))
      {
         $response=array(
            'status' => 1,
            'message' =>'Mahasiswa Deleted Successfully.'
         );
      }
      else
      {
         $response=array(
            'status' => 0,
            'message' =>'Mahasiswa Deletion Failed.'
         );
      }
      header('Content-Type: application/json');
      echo json_encode($response);
   }

 
?> 
