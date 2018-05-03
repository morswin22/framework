<?php include('../fw/main.php') ?><!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>INTRO-KSERO-CENTRUM</title>
    <?=$fw->component('3rd-party')?>
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/admin-index.css" rel="stylesheet">
    <link href="../css/services.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../">INTRO-KSERO-CENTRUM</a>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item d-sm-block d-none"><a>Panel administracyjny</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mb-3">

        <div class="mt-3 mt-sm-4">
            <span class="hint">Wybierz sekcję do edytowania</span>
            <div class="selection">
                <a class="item">USŁUGI</a>
                <a class="item">KONTAKT</a>
                <a class="item">GODZ. OTWARCIA</a>
            </div>
        </div>

        <div class="pages">
            <div class="mt-4" style="display: none" id="services">
                <!-- list all services -->
        <div class="row mt-3">
            <div class="col">
                <!-- query output -->
                <?php

                    $query = $fw->db['services']->query();

                    $services = array();
                    while ($s = $query->fetch()) {
                        $services[$s['id']] = $s;
                    }

                    if (count($services) == 0) {
                        ?>
                        <div class="row my-3">
                            <div class="col-sm-12 col-md-1"></div>
                            <div class="col-sm-12 col-md-10">
                                <div class="alert alert-warning" role="alert">
                                    Brak usług
                                </div>
                            </div>
                        </div>
                        <?php
                    }

                    foreach ($services as $service) {
                            $areImages = false;
                            $images = explode('!', $service['images']);
                            if (count($images) > 0 && $images[0] !== '') {
                                $areImages = true;
                            }
                            ?>
                            <div class="service-container mt-2">
                                <div class="d-md-block d-none text-center" style="grid-area: imgs">
                                    <?php $id = hash('sha256', rand(1,100)*rand(1,100)); ?>
                                    <div class="w-100" id="outer-<?=$id?>">
                                        <?php if($areImages) {  ?>
                                            <i class="material-icons" style="font-size: 150px; width: 150px; height:150px; display: block; color: #BFBFBF; cursor: default; " id="img-<?=$id?>">&#xE413;</i>
                                        <?php } else { ?>
                                            <i class="material-icons" style="font-size: 150px; width: 150px; height:150px; display: block; color: #BFBFBF; cursor: default; " id="img-<?=$id?>">&#xE439;</i>
                                        <?php } ?>
                                        <div id="onhover-<?=$id?>" data-toggle="modal" data-target="#imgModal-<?=$id?>">Kliknij, aby edytować zdjęcia</div>
                                        <div class="modal fade" id="imgModal-<?=$id?>" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><?=$service['name']?></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                AND HERE ARE YOUr PHOTOS :)
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                        <script>
                                            var outer = $('#outer-<?=$id?>');
                                            var img = $('#img-<?=$id?>');
                                            var div = $('#onhover-<?=$id?>');
                                            var hint =$('#hint-<?=$id?>');
                                            var imgh = img.height();
                                            var imgw = img.width();
                                            var divh = div.height();
                                            $(window).on('resize', function(){
                                                var imgh = img.height();
                                                div.css({
                                                    bottom: imgh+'px',
                                                    height: imgh+'px',
                                                });
                                                outer.css({
                                                    overflow: 'hidden',
                                                    height: imgh+'px',
                                                });
                                                hint.css({
                                                    bottom: imgh+'px',
                                                    height: imgh+'px',
                                                    width: imgw+'px',
                                                })
                                            });
                                            div.css({
                                                bottom: imgh+'px',
                                                height: imgh+'px',
                                            });
                                            outer.css({
                                                overflow: 'hidden',
                                                height: imgh+'px',
                                            });
                                            hint.css({
                                                bottom: imgh+'px',
                                                height: imgh+'px',
                                                width: imgw+'px',
                                            })
                                        </script>
                                        <style>
                                            #hint-<?=$id?> {
                                                text-align: left;
                                                position: relative;
                                                display: table-cell;
                                                vertical-align: bottom;
                                            }
                                            #onhover-<?=$id?> {
                                                display: none;
                                                position: relative;
                                                vertical-align: middle;
                                                text-align: center;
                                                cursor: pointer;
                                                padding: 3px;
                                                -webkit-transition: background-color 0.5s; /* Safari */
                                                transition: background-color 0.5s;
                                            }
                                            #onhover-<?=$id?>:hover {
                                                background-color: #00000019;
                                            }
                                            #outer-<?=$id?>:hover > #onhover-<?=$id?> {
                                                display: table-cell;
                                            }
                                            #outer-<?=$id?>:hover > #hint-<?=$id?> {
                                                display: none;
                                            }
                                        </style>
                                    </div>
                                </div>
                                <h4 class="service-name"><?=$service['name']?></h4>
                                <ul class="service-list" style="grid-area: desc">
                                    <?php
                                    $desc = explode('!', $service['description']);
                                    foreach($desc as $li) {
                                        ?>
                                        <li><?=$li?></li>
                                        <?php
                                    }
                                    ?>
                                    <div class="input-group mt-3">
                                        <input type="text" class="form-control" placeholder="Opis usługi">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button">Dodaj</button>
                                        </div>
                                    </div>
                                </ul>
                            </div>

                        <?php
                    }
                    
                    ?>


                </div>
            </div>
            </div>
            <div class="mt-4" style="display: none" id="contact">
                kontakt
            </div>
            <div class="mt-4" style="display: none" id="schedule">
                godziny otwarcia
            </div>
        </div>

    </div>

    <script src="../js/router.min.js"></script>
    <script>
        'use strict';
        (function () {
                var router = new Router();
                router.setPages([$('#services'), $('#contact'), $('#schedule')]).setDOM($($('div.selection')[0]));
        })();
    </script>

</body>
</html>