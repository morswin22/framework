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
     
$fw->setNavbarCurrent('nav-active'); // sets navbar element current class name, default: active
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
* `getUsers` - returns an array of all users from `fw/users/`

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
