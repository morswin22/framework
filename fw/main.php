<?php

include_once('fw.php');

$fw = new Framework('http://localhost/ikc_30-04-2018');

$fw->add_component('navbar', '{{domain_url}}/fw/components/navbar.html');

?>
