<?php
    include("env.php");
     try{
          
     $conn = mysqli_connect( DB_HOST,
                             DB_USER,
                             DB_PASSWORD,
                             DB_NAME);
     }  
     catch(mysqli_sql_exception){
          echo "Could not Connect to the server! <br>";
     }                    
?>