<?php
ob_start();

try
{
  $con = new PDO("mysql:dbname=ishjoot;host=localhost","root","");
  $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
}
catch(PDOException $e)
{
  echo "Connection failed". $e->getMessage();
}
?>
