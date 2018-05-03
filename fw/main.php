<?php

include_once('fw.php');

$fw = new Framework('http://'.$_SERVER['HTTP_HOST'].'/ikc_30-04-2018');

$fw->db_register('admin','admin'); // change this later ;)
$fw->add_db('services', array('id', 'name', 'description', 'keywords', 'images'), 'id');

$fw->prepareUsers(array('name', 'pass'), 'name', 'pass');

$fw->add_component('3rd-party', '{{domain_url}}/fw/components/3rd-party.html');
$fw->add_component('navbar', '{{domain_url}}/fw/components/navbar.html');

?>
