<?php
  

class CrowdLuvDBFactory {

        

  function getCrowdLuvDB() {

      //Open DB connection
      try {
          //echo "creating db conn";
          $CL_db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME .";port=" . DB_PORT . ";charset=utf8mb4", DB_USER, DB_PASS, array(PDO::ATTR_PERSISTENT => true));
          $CL_db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
          $CL_db->exec("SET NAMES 'utf8'");
          //var_dump($CL_db);
      } catch (Exception $e) {
          echo "Could not connect to the database." . $e;
          exit;
      }


      return $CL_db;
  }



}