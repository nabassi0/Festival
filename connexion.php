<?php
   $user='root'; 
   $pass='';
   $dsn='mysql:host=localhost;dbname=festival;port=3308';
   try{ 
      $dbh=new PDO($dsn,$user,$pass); 
   } catch(PDOException $e){ 
   print "Erreur ! :" . $e->getMessage() . "<br/>"; 
   die(); 
   }  
?>