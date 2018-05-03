<?php include('fw/main.php') ?><!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Usługi - INTRO-KSERO-CENTRUM</title>
    <?=$fw->component('3rd-party')?>
    <link href="css/main.css" rel="stylesheet">
    <link href="css/services.css" rel="stylesheet">
</head>
<body>
    
    <?=$fw->component('navbar')?>

    <div class="container mb-3">

        <?php
        
        $query = "";
        $queryInput = "";
        $queryName = "";
        $queryDesc = "";
        $queryKeys = "";
        if (isset($_GET['services'])) {
            $query = urldecode($_GET['services']);
            $queryInput = $query;
            // spreparować kwerende.
            $query = strtolower(trim($query));
            $queryP = explode(' ', $query);
            $query = array();
            foreach($queryP as $qP) {
                if ($qP != '') {
                    $query[] = '(?=.*'.$qP.')';
                }
            }
            $query = implode('', $query);
            $queryName = array('name'=>'/'.$query.'/i');
            $queryDesc = array('description'=>'/'.$query.'/i');
            $queryKeys = array('keywords'=>'/'.$query.'/i');
        }
        
        ?>

        <div class="row mt-4 mb-3">
            <div class="col-sm-12 col-md-2"></div>
            <div class="col-sm-12 col-md-8">
                <!-- query input -->
                <form>
                    <div class="input-group rounded" style="background: #fff">
                        <input type="text" class="form-control" name="services" placeholder="Wyszukaj usługę.." value="<?=$queryInput?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-danger">Szukaj</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col">
                <!-- query output -->
                <?php

                $sName = $fw->db['services']->query($queryName);
                $sDesc = $fw->db['services']->query($queryDesc);
                $sKeys = $fw->db['services']->query($queryKeys);

                $services = array();
                while ($s = $sName->fetch()) {
                    $services[$s['id']] = $s;
                }
                while ($s = $sDesc->fetch()) {
                    $services[$s['id']] = $s;
                }
                while ($s = $sKeys->fetch()) {
                    $services[$s['id']] = $s;
                }

                if (count($services) == 0) {
                    ?>
                    <div class="row my-3">
                        <div class="col-sm-12 col-md-1"></div>
                        <div class="col-sm-12 col-md-10">
                            <div class="alert alert-warning" role="alert">
                                <b>Przepraszamy</b>, podanej usługi "<i><?=$queryInput?></i>" nie udało się odnaleźć
                            </div>
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col-sm-12 col-md-3"></div>
                        <div class="col-sm-12 col-md-6 text-center">
                            <div class="list-group">
                                <a class="list-group-item list-group-item-info">
                                    Dostępne usługi do wyszukania:
                                </a>
                                <?php
                                $all = $fw->db['services']->query('');
                                while($service = $all->fetch()) { ?>
                                <a href="services.php?services=<?=urlencode($service['name'])?>" class="list-group-item list-group-item-action"><?=$service['name']?></a>
                                <?php } ?>
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
                                <?php if($areImages) { $id = hash('sha256', rand(1,100)*rand(1,100)); ?>
                                <div class="w-75 mx-auto" id="outer-<?=$id?>">
                                    <img id="img-<?=$id?>" class="w-100" src="images/<?=$images[0]?>">
                                    <?php if (count($images) > 1) { ?>
                                    <div id="hint-<?=$id?>"><img style="display: block; width: 26px; height: 26px; margin-left: auto;" src="img/click.png"></div>
                                    <div id="onhover-<?=$id?>" data-toggle="modal" data-target="#imgModal-<?=$id?>">Kliknij, aby zobaczyć więcej zdjęć</div>
                                    <div class="modal fade" id="imgModal-<?=$id?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><?=$service['name']?></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="imgSlider-<?=$id?>" class="carousel slide" data-ride="carousel">
                                                <div class="carousel-inner">
                                                    <?php foreach($images as $k => $image) { 
                                                        if ($k == 0) {$active = 'active';} else {$active = '';} ?>
                                                    <div class="carousel-item <?=$active?>">
                                                        <img class="d-block w-100" src="images/<?=$image?>" alt="Zdjęcie">
                                                    </div>
                                                    <?php } ?>
                                                </div>
                                                <a class="carousel-control-prev" href="#imgSlider-<?=$id?>" role="button" data-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                                <a class="carousel-control-next" href="#imgSlider-<?=$id?>" role="button" data-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </div>
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
                                    <?php } else { ?>
                                        <script>
                                            var div = $('#outer-<?=$id?>');
                                            $(window).on('resize', function(){
                                                setTimeout(function(){
                                                    var h = $(div.parent()).height();
                                                    div.css({'height': h+'px'});
                                                },50);
                                            });
                                            setTimeout(function(){
                                                var h = $(div.parent()).height();
                                                div.css({'height': h+'px'});
                                            },50); // TODO: ERROR: this sometimes does not catch up
                                        </script>
                                        <style>
                                            #outer-<?=$id?> {
                                                display: table-cell;
                                                vertical-align: middle;
                                                padding-left: 12.5%;
                                                padding-right: 12.5%;
                                            }
                                        </style>
                                    <?php } ?>
                                </div>
                                <?php } else { ?>
                                    <i class="material-icons" style="font-size: 150px; width: 150px; height:150px; display: block; color: #BFBFBF; cursor: default; ">&#xE251;</i>
                                <?php } ?>
                            </div>
                            <h4 class="service-name"><?=$service['name']?></h4>
                            <ul style="grid-area: desc">
                                <?php
                                $desc = explode('!', $service['description']);
                                foreach($desc as $li) {
                                    ?>
                                    <li><?=$li?></li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>

                    <?php
                }
                
                ?>


            </div>
        </div>

    </div>

</body>
</html>