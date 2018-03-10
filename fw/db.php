<?php include('main.php');$get = $_GET;?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Database Access</title>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.35.0/codemirror.min.css" />

    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.35.0/codemirror.min.js"></script>

</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">    
            <a class="navbar-brand" href="./db.php">Database Access</a>
        </div>
    </nav>

    <div class="container">

        <div class="row my-4">

            <div class="col-sm-12 col-md-3">
                <ul class="list-group">
                    <li class="list-group-item list-group-item-secondary">Loaded databases</li>
                    <?php
                        if (isset($get['dbname'])) {
                            $current = $get['dbname'];
                        } else {
                            $current = '';
                        }
                        foreach($fw->db as $db) {
                            if ($db->name == $current) {
                                $rep = 'active';
                            } else {
                                $rep = '';
                            }
                            $str = '<a href="./db.php?dbname={{DB_NAME}}" class="list-group-item list-group-item-action {{is:active}}">{{DB_NAME}}</a>';
                            $str = str_replace('{{DB_NAME}}',$db->name,$str);
                            $str = str_replace('{{is:active}}',$rep,$str);
                            echo $str;
                        }
                    ?>
                </ul>
            </div>

            <div class="col-sm-12 col-md-9" id="panel" style="visibility: hidden;">
                <?php
                if (isset($get['dbname'])) {
                    $dbname = $get['dbname'];
                    if (isset($fw->db[$dbname])) {
                        if (isset($get['query'])) {
                            $q = $get['query'];
                        } else {
                            $q = '[{}]';
                        }
                        $op  = 'getdata';
                        $s_0 = '';
                        $s_1 = '';
                        $s_2 = '';
                        $s_3 = '';
                        if (isset($get['operation'])) {
                            $op = $get['operation'];
                            switch ($op) {
                                case 'getdata': $s_0='selected'; break;
                                case 'putdata': $s_1='selected'; break;
                                case 'rmdata': $s_2='selected'; break;
                                case 'editdata': $s_3='selected'; break;
                            }
                        }
                        ?>
                        
                        <div class="input-group mb-2">
                            <form class="w-100">
                                <input type="text" value="<?=$dbname?>" name="dbname" hidden="hidden">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="operation">Operation</label>
                                    </div>
                                    <select class="custom-select" name="operation" id="operation">
                                        <option <?=$s_0?> value="getdata">Get data</option>
                                        <option <?=$s_1?> value="putdata">Put data</option>
                                        <option <?=$s_2?> value="rmdata">Remove data</option>
                                        <option <?=$s_3?> value="editdata">Edit data</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control rounded" style="height: 70px!important" id="query" rows="3" name="query"><?=$q?></textarea>
                                    <button class="btn btn-outline-primary w-100 mt-2" type="submit">Send query</button>
                                </div>
                            </form>
                        </div>
                        <?php
                        
                        ?>
                        <table class="table table-bordered table-hover table-striped"><thead><?php
                        foreach($fw->db[$dbname]->cols as $col) {
                            ?><th><?=$col?></th><?php
                        }
                        ?></thead><?php

                        $q = json_decode(urldecode($q), true);
                        try {
                            switch($op) {
                                case "getdata":
                                    if (is_array($q)) {
                                        if (isset($q[0]) and is_array($q[0])) {
                                            $q = $q[0];
                                        }
                                    }
                                    break;
                                case "putdata":
                                    if (is_array($q)) {
                                        if (count($q) == 2 and is_array($q[1])) {
                                            $fw->db[$dbname]->putData($q[0], $q[1]);
                                        } else {
                                            throw new Exception('query syntax error');
                                        }
                                    } else {
                                        throw new Exception('query syntax error');
                                    }
                                    $q = '';
                                    break;
                                case "rmdata":
                                    if (is_array($q)) {
                                        if (isset($q[0]) and !is_array($q[0])) {
                                            $fw->db[$dbname]->rmData($q[0]);
                                        } else {
                                            throw new Exception('query syntax error');
                                        }
                                    } else {
                                        throw new Exception('query syntax error');
                                    }
                                    $q = '';
                                    break;
                                case "editdata":
                                    if (is_array($q)) {
                                        if (count($q) == 2 and is_array($q[1])) {
                                            $fw->db[$dbname]->editData($q[0], $q[1]);
                                        } else {
                                            throw new Exception('query syntax error');
                                        }
                                    } else {
                                        throw new Exception('query syntax error');
                                    }
                                    $q = '';
                                    break;
                            }
                        } catch (Exception $e) {
                            print($e->getMessage());
                        }


                        $query = $fw->db[$dbname]->query($q);
                        ?><tbody><?php
                        while($row = $query->fetch()) {
                            ?><tr><?php
                            foreach($row as $value) {
                                ?><td><?=$value?></td><?php
                            }
                            ?></tr><?php
                        }
                        ?></tbody></table><?php
                    }
                }
                
                ?>
            </div>
            
        </div>

    </div>

    <script>
    var query = CodeMirror.fromTextArea(document.getElementById("query"), {
        lineNumbers: true,
        matchBrackets: true
    });
    query.display.wrapper.style.height = "100px";
    query.display.wrapper.className += " rounded border";
    $("#panel").css("visibility", "visible");
    </script>

</body>
</html>
