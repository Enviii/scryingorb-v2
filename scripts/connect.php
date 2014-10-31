<?php
   $host     ="localhost";
   $username ="root";
   $password ="";
   $database ="lolsales";

   $mysqli = new mysqli($host, $username, $password, $database); //Connect to database
   	
   if (!$mysqli){
   	die("Can't connect to MySQL: ".mysqli_connect_error());
   }
?>