<?php
// Routes

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->post('/create/user', function($request, $response, $args) {
  //Connexion to database
  try {
    $db = new db();
    $db = $db->connect();
  } catch (Exception $e) {
      echo 'Error : '. $e->getMessage();
  }

  //Creation of token
  $adress = bin2hex(random_bytes(16));
  echo $adress;
  //Verify if the token doesn't already exists
  $sql = 'SELECT COUNT(adresse) as count FROM users WHERE adresse ="'.$adress.'"';
  $query = $db->query($sql);
  $value = $query->fetch();

  if($value['count'] == 0){
    $sql = 'INSERT INTO users(adresse) VALUES ("'.$adress.'")';
    $query = $db->exec($sql);
  }
});

$app->post('/send/{id}', function($request, $response, $args) {
  $user = $id;
});
