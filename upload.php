<?php  
    header('Access-Control-Allow-Origin: *');  
     $filename = $_FILES['file']['name'];
     $allowed_extensions = array('jpg','jpeg','png','pdf');
      $extension = pathinfo($filename, PATHINFO_EXTENSION);
       if(in_array(strtolower($extension),$allowed_extensions) ) {     
          if(move_uploaded_file($_FILES['file']['tmp_name'], "uploads/".$filename)){
                echo 1;
          }else{
              echo 0;
          }
    }else{
        echo 0;
    } 
?>