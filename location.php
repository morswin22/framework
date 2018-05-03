<?php include('fw/main.php') ?><!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <meta http-equiv="X-UA-Compatible" content="ie=edge"> -->
    <title>Lokalizacja - INTRO-KSERO-CENTRUM</title>
    <?=$fw->component('3rd-party')?>
    <link href="css/main.css" rel="stylesheet">
    <link href="css/location.css" rel="stylesheet">
</head>
<body>
    
    <?=$fw->component('navbar')?>

    <div class="container mb-3">

        <iframe class="w-100 rounded my-4 mb-sm-3" height="450" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=place_id:ChIJ-VoR6j5bBEcRG-q27Hy0_98&key=AIzaSyCZ2wiBgJrBKeOT0zgzQG_Kf1qjKOEQjPk" allowfullscreen></iframe>

        <div class="centered">
            <i class="material-icons">&#xE0C8;</i><span>Punkt zlokalizowany jest w centrum Poznania, przy ulicy Zielonej 1</span>
        </div>

    </div>

</body>
</html>