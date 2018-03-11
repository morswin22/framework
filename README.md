# Framework
Made With ♥ By Patryk Janiak (https://morswin22.github.io/)

## How to use
Create new project with framework:

    git clone https://github.com/morswin22/framework.git your_project_dir

Configurate `fw/main.php`:

```php
$fw = new Framework(YourDomainURL);

$fw->setSets(array(
    // sets' names
    // ex. 'page.php'
));

$fw->setTitles(array(
    // titles for sets
    // ex. 'page.php' => 'Title'
));

$fw->setMetas(array(
    // custom meta tags files for sets
    // ex. 'page.php' => '{{DOMAIN_URL}}fw/uncommon/page_meta.html'
));
$fw->setLinks(array(
    // custom stylesheets for sets
    // ex. 'page.php' => '{{DOMAIN_URL}}fw/uncommon/page_link.html'
));
$fw->setScripts(array(
    // custom scripts links for sets
    // ex. 'page.php' => '{{DOMAIN_URL}}fw/uncommon/page_script.html'
));
```

Initialize framework in file with:

```php
include('path_to_fw/main.php'); 
$fw->set('setname');
```

Output default head elements: 
 
```php
$fw->commonMeta();   // outputs fw/common/meta.html
$fw->title();        // outputs title
$fw->commonLink();   // outputs fw/common/link.html
$fw->commonScript(); // outputs fw/common/script.html
```

Output specific head elements for a pagename:
 
```php
$fw->fullMeta();   // outputs commonMeta() and file from setMetas()
$fw->title();      // outputs title
$fw->fullLink();   // outputs commonLink() and file from setLinks()
$fw->fullScript(); // outputs commonScript() and file from setScripts()
```

Output html code:

```php
$fw->header(); // outputs fw/common/header.html file
$fw->navbar(); // outputs fw/common/navbar.html file
$fw->footer(); // outputs fw/common/footer.html file
```

## Using different set's name
You can pass set's name into framework functions like: 

* `fullMeta`
* `title`
* `fullLink`
* `fullScript`

in order to get specific title, meta tags, links or scripts for given set's name

```php
// example:
$fw->set('page1.php');

$fw->title();            // will output title for set with name 'page1.php'
$fw->title('page2.php'); // will output title for set with name 'page2.php' 
```

## Login extension
Put `prepareUsers` framework function in your `fw/main.php`

```php
// example user database structure
$fw->prepareUsers(
array('name', 'pass', 'email', 'email-confirmed'), // list of all datebase cols' names
    'name',                                        // unique id col's name
    'pass'                                         // col's name used to verifying login
);
```

Login extension functions:
* `register` - accepts an array with all filled database cols
* `login`    - accepts an array with id and checking parameter 
* `logout`   - does not take any arguments
* `isLogged` - returns true if user is logged in
* `getUsers` - returns an array of all users
* `edit`     - takes as argument param's name and value to change

Tricks with `edit` and `edit_user` functions:

```php
$fw->edit('pass','1234'); // changes pass value in currently logged user

// $user_object = array('name'=>'test','pass'=>'4321','desc'=>'Lorem ipsum');
$fw->edit_user($user_object,'pass','1234'); // changes pass value in given user
```

## Database extension
#### Adding new databases
Framework comes with a function called `add_db` which takes three arguments: 

1. database name
1. columns
1. id column name

```php
// code example
$fw->add_db('family', array('id','name','birthday'), 'id');
```

#### Accessing databases from php
Your added databases are stored inside of `$fw->db` array. Every database object comes with functions:

* `query` - returns a new query object ([more about that](#database-query-object))
* `putData` - takes as an argument id and values (all columns must be filled except for id)
* `rmData` - takes as an argument id to delete
* `editData` - takes as an argument id and values to change

```php
// code example

// pass empty string as id to push new row at the back
$fw->db['family']->putData('', array('name'=>'Ana', 'birthday'=>'11.02.1985'));
// you can pass DB_PUSH constant instead of the empty string

// simply removes row from database
$fw->db['family']->rmData(5);

// first argument is an id and the second is an array with values to change
$fw->db['family']->editData(2, array('birthday'=>'19.10.1983'));

```

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
$query = $fw->db['family']->query();

// fetches for specific rows in the database
$query = $fw->db['family']->query(array('name'=>'Patrick')); 

// you can pass a regular expression
$query = $fw->db['family']->query(array('name'=>'/patrick/i')); 

// use fetch in if and while
if ($row = $query->fetch()) {
    echo 'Patrick's birthday is on ' . $row['birthday'];
}

// functions like putData, rmData, editData work same as on database object 
```

## Navbar tricks
In your `fw/common/navbar.html` you can place in class special element `{{is:setname}}`

```html
<!-- example -->
<div class="nav">
    <div class="nav-item {{is:page1.php}}">
        <a href="page1.php">Page 1</a>
    </div>
    <div class="nav-item {{is:page2.php}}">
        <a href="page2.php">Page 2</a>
    </div>
</div>
```

When `$fw->navbar()` is triggered, it will output navbar.html and change this special element into `active` if current set's name matches with the `{{is:setname}}`

```html
<!-- result (current set's name is 'page1.php') -->
<div class="nav">
    <div class="nav-item active">
        <a href="page1.php">Page 1</a>
    </div>
    <div class="nav-item {{is:page2.php}}">
        <a href="page2.php">Page 2</a>
    </div>
</div>
```

If you have run `$fw->setNavbarCurrent('classNameForActiveElement')` then it will change the `{{is:setname}}` into a new given class name

```php
<?php $fw->setNavbarCurrent('nav-active'); ?>
<!-- result (current set's name is 'page1.php') -->
...
    <div class="nav-item nav-active">
...
    <div class="nav-item {{is:page2.php}}">
...
```

You can run `$fw->navbar('setname')` so that it will try to match given set's name with the `{{is:setname}}` special tag
