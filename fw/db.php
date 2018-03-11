<?php include('main.php');$get = $_GET;include('lib/Parsedown.php');$mdparser =new Parsedown(); if (isset($_POST['name'], $_POST['pass'])) {$fw->db_login($_POST['name'], $_POST['pass']);} if (isset($_SESSION['fw-db-user'])) {$d=$_SESSION['fw-db-user']; $fw->db_login($d['name'],$d['pass']);} if (isset($get["logout"])) {$fw->db_logout(); header('Location: ./db.php'); exit();} ?><!DOCTYPE html>
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

    <style>
        .sort-control > .sort-icon {
            visibility: hidden;
        }
        .sort-control > .sort-icon > img {
            width: 20px;
            height: 20px;
        }
        .sort-control:hover > .sort-icon {
            visibility: visible;
        }
    </style>

</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">    
            <a class="navbar-brand" href="./db.php">Database Access</a>
            <?php if ($fw->db_access == true) { ?>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="./db.php?logout">Logout</a>
                    </li>
                </ul>
            </div>
            <?php } ?>
        </div>
    </nav>

    <div class="container">

        <?php
        if ($fw->db_access == true) {
        ?>

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
                        if (isset($fw)) {
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
                            ?><th class="sort-control"><a href="?dbname=<?=$dbname?>&query=<?=urlencode($q)?>&sort=<?=$col?>,asc" class="sort-icon"><img src="lib/img/sort_up.png"></a>
                                <?=$col?>
                                <a href="?dbname=<?=$dbname?>&query=<?=urlencode($q)?>&sort=<?=$col?>,desc" class="sort-icon"><img src="lib/img/sort_down.png"></a></th><?php
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

                        if (isset($get['sort'])) {
                            $sortData = explode(',',$get['sort']);
                            if (count($sortData) == 2) {
                                 $query->sort($sortData[0], $sortData[1]);
                            }
                        }

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
                } else {
                    ?>
                    <script>$("#panel").css("visibility", "visible");</script>
                    <div class="row mb-4">
                        <div class="col">
                            <h3>How to use</h3>
                            <p class="lead">A quick quide about using the database GUI</p>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col">
                            <?php
                            
echo $mdparser->text('
#### Adding new databases
Framework comes with a function called `add_db` which takes three arguments: 

1. database name
1. columns
1. id column name

```php
// code example
$fw->add_db(\'family\', array(\'id\',\'name\',\'birthday\'), \'id\');
```
');
                            
                            ?>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col">
                            <?php
                            
echo $mdparser->text('
#### Accessing databases from php
Your added databases are stored inside of `$fw->db` array. Every database object comes with functions:

* `query` - returns a new query object ([more about that](#db-query))
* `putData` - takes as an argument id and values (all columns must be filled except for id)
* `rmData` - takes as an argument id to delete
* `editData` - takes as an argument id and values to change

```php
// code example

// pass empty string as id to push new row at the back
$fw->db[\'family\']->putData(\'\', array(\'name\'=>\'Ana\', \'birthday\'=>\'11.02.1985\'));
// you can pass DB_PUSH constant instead of the empty string

// simply removes row from database
$fw->db[\'family\']->rmData(5);

// first argument is an id and the second is an array with values to change
$fw->db[\'family\']->editData(2, array(\'birthday\'=>\'19.10.1983\'));

```
');
                            
                            ?>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col"><a name="db-query"></a>
                            <?php
                            
echo $mdparser->text('
#### Database query object
When function `query` is fired, it returns new query object which has got some functions:

* `getData` - gets data from database 
* `fetch` - returns following rows or `false` when there is nothing to return
* `putData` - takes as an argument id and values (all columns must be filled except for id)
* `rmData` - takes as an argument id to delete
* `editData` - takes as an argument id and values to change

```php
// code example

// fetches for every row in the database
$query = $fw->db[\'family\']->query();

// fetches for specific rows in the database
$query = $fw->db[\'family\']->query(array(\'name\'=>\'Patrick\')); 

// you can pass a regular expression
$query = $fw->db[\'family\']->query(array(\'name\'=>\'/patrick/i\')); 

// use fetch in if and while
if ($row = $query->fetch()) {
    echo \'Patrick\'s birthday is on \' . $row[\'birthday\'];
}

// functions like putData, rmData, editData work same as on database object 
```
');
                            
                            ?>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col">
                            <?php
                            
echo $mdparser->text('
#### Accessing databases from the GUI
Your added databases are listed at the left side of screen. After selecting a database you will see a table where the columns and rows are displayed. Above that there is an input textarea and operation selector. You can select one out of four functions:

* Get data
* Put data
* Remove data
* Edit data

You write queries in json format. You have to pass the same arguments as you would pass in php. Store the arguments in a json array in the correct order.

Example: (edit data operation is selected)
```
[
    3,
    {
        "name": "Martha"
    }
]
```
');
                            
                            ?>
                        </div>
                    </div>
                    <?php
                }
                
                ?>
            </div>
            
        </div>

        <?php
        } else {

        ?>

        <div class="row my-4">
            <div class="col-sm-12 col-md-2"></div>
            <div class="col-sm-12 col-md-8">
                <div class="alert alert-danger mb-3" role="alert">
                    <b>This is a resticted area!</b> You have to be logged in as an administrator of this network
                </div>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-sm-12 col-md-3"></div>
            <div class="col-sm-12 col-md-6">
                <form method="post">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Enter name">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="pass" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100 mt-2">Submit</button>
                </form>
            </div>
        </div>

        <?php
        }
        ?>

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
