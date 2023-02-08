<?php
include('discord.php');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Basic Template</title>
<meta content="Basic Template" property="og:title">
<meta content="Basic Template" property="og:description">
<meta content="https://example.com" property="og:url">
<meta content="#43B581" data-react-helmet="true" name="theme-color">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
<link rel="stylesheet" type="text/css" href="https://unpkg.com/bulma-prefers-dark"/>
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/light.png">
<link rel="icon" type="image/png" sizes="16x16" href="/light.png">

</head>
<body>
    
    
<?php
if(session('access_token')) {
  $user = apiRequest($apiURLBase);
  $_SESSION['id'] = $user->id;
  $_SESSION['username'] = $user->username;
  $_SESSION['email'] = $user->email;
  $_SESSION['tag'] = $user->discriminator;
  ?>

  <br>
  <section class="section">
    <div class="container">
      <h1 class="title">Logged In!</h1>
      <br>
      <p id="txt" class="subtitle">Your username: <?php echo $_SESSION['username']; ?></p>
    </div>
  </section>

  <?php

// if not logged in
} else { 

echo'<div class="center">';
    echo '<br>';
  echo '<h1 class="title">Not logged in</h1>';
  echo '<br>';
  echo '<p><a class="button is-primary" role="button" href="?action=login">Login with Discord</a></p>';
echo '</div>';
}

?>

</body>
</html>
