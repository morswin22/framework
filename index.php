<?php include('fw/main.php') ?><!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="X-UA-Compatible" content="ie=edge"> -->
    <title>INTRO-KSERO-CENTRUM</title>
    <?=$fw->component('3rd-party')?>
    <link href="css/main.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
</head>
<body>
    
    <?=$fw->component('navbar')?>

    <div class="container">

        <h1 id="brand">INTRO-KSERO-CENTRUM</h1>

        <a href="services.php" class="button" id="main">
            <i class="material-icons">&#xE85D;</i><span>USŁUGI</span>
        </a>
        <a href="location.php" class="button">
            <i class="material-icons">&#xE0C8;</i><span>LOKALIZACJA</span>
        </a>
        <a href="contact.php" class="button">
            <i class="material-icons">&#xE158;</i><span>KONTAKT</span>
        </a>

    </div>

</body>
</html>