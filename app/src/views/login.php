<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?=$title?></title>
</head>
<body>

  <div id="app">

      <h1>LogIn</h1>

        <form action="/login" method="post">
        <label><input type="text" name="un" placeholder="Username" value="<?= $oldUserName ?>" /></label><br>
          <label><input type="password" name="pw" placeholder="Password"/></label><br>
          <input type="submit" id="submit" value="LOGIN" />
        </form>

    <?php if ($errorMessage) {
        echo "<b>$errorMessage</b>";
        }
    ?>
  </div>

</body>
</html>
