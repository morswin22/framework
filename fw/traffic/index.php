<?php include('../main.php');$get = $_GET; if (isset($_POST['fw-traffic-name'], $_POST['fw-traffic-pass'])) {$fw->traffic_login($_POST['fw-traffic-name'], $_POST['fw-traffic-pass']);header('Location: ./'); exit();} if (isset($_SESSION['fw-traffic-user'])) {$d=$_SESSION['fw-traffic-user']; $fw->traffic_login($d['name'],$d['pass']);} if (isset($get["logout"])) {$fw->traffic_logout(); header('Location: ./'); exit();} ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    
    Traffic collecting is <?= ($fw->trafficEnabled) ? "enabled" : "disabled" ?>

    <pre><?=print_r($fw->getSortedTraffic('desc'),true)?></pre>

</body>
</html>