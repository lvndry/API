<?php

// Routes
$app->post('/create/user', function($request, $response, $args) {
  //Connexion to database
  try {
    $db = new db();
    $db = $db->connect();

    //Creation of token
    $adress = bin2hex(random_bytes(16));
    echo $adress;

    //Verify if the token doesn't already exists
    $sql = 'SELECT COUNT(adresse) as count FROM users WHERE adresse ="'.$adress.'"';
    $query = $db->query($sql);
    $value = $query->fetch();

    //If the token doesn't exist it is added to the database
    if($value['count'] == 0){
      $time = time();
      $sql = 'INSERT INTO users(adresse, expire) VALUES ("'.$adress.'", "'.$time.'")';
      $query = $db->exec($sql);
    }
  } catch (Exception $e) {
      echo 'Error : '. $e->getMessage();
  }
});

$app->post('/send/{id}/{message}', function($request, $response, $args) {

  //Connexion to database
  try {
    $db = new db();
    $db = $db->connect();

    $invalid_characters = array("$", "%", "#", "<", ">", "|", "\\");

    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');
    //Verify if the token doesn't already exists
    $sql = 'SELECT COUNT(adresse) as count FROM users WHERE adresse ="'.$id.'"';
    $query = $db->query($sql);
    $value = $query->fetch();

    //If the token doesn't exist it is added to the database
    if($value['count'] != 0){
      $message = $route->getArgument('message');
      $message = str_replace($invalid_characters, "", $message);

      //$sql = 'UPDATE users set message ="'.$message.'" WHERE adresse="'.$id.'" AND message is NULL';
      $time = time();
      $sql = 'INSERT INTO users(adresse, message, expire) VALUES ("'.$id.'", "'.$message.'", ".'$time.'")';
      $query = $db->exec($sql);
    }
  } catch (Exception $e) {
      echo 'Error : '. $e->getMessage();
  }
});

$app->get('/messages/{id}', function($request, $response, $args) {
  //connection to database
  try {
    $db = new db();
    $db = $db->connect();

    $route = $request->getAttribute('route');
    $id = $route->getArgument('id');

    $sql = 'SELECT * FROM users WHERE adresse ="'.$id.'" AND message IS NOT NULL';
    $query = $db->query($sql);
    $messages = $query->fetchall(PDO::FETCH_OBJ);
    echo json_encode($messages);
  } catch (Exception $e) {
      echo 'Error : '. $e->getMessage();
  }
});

$app->get('/{id}/status', function($request, $response, $args) {
  $db = new db();
  $db = $db->connect();

  //Creation of token
  $adress = bin2hex(random_bytes(16));
  echo $adress;

  //Verify if the token doesn't already exists
  $sql = 'SELECT COUNT(expire) as expire FROM users WHERE adresse ="'.$adress.'"';
  $query = $db->query($sql);
  $token = $query->fetch();
  if(time() - $token['expire'] > 30){
    echo "offline";
  }
  else echo "online";
})
