# framework
Made With ♥ By Patryk Janiak (https://morswin22.github.io/)

# how to use
Configurate fw/main.php:

     $fw = new Framework(YourDomainURL);

     $fw->setSets(array(
          // sets names
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

Initialize framework in file with:

    include('path_to_fw/main.php'); 
    $fw->set('setname');

Output default head elements: 
 
    $fw->commonMeta();   // outputs fw/common/meta.html
    $fw->title();        // outputs title
    $fw->commonLink();   // outputs fw/common/link.html
    $fw->commonScript(); // outputs fw/common/script.html

Output specific head elements for a pagename:
 
    $fw->fullMeta();   // outputs commonMeta() and file from setMetas()
    $fw->title();      // outputs title
    $fw->fullLink();   // outputs commonLink() and file from setLinks()
    $fw->fullScript(); // outputs commonScript() and file from setScripts()

Output html code:
 
    $fw->header(); // outputs fw/common/header.html file
    $fw->navbar(); // outputs fw/common/navbar.html file
    $fw->footer(); // outputs fw/common/footer.html file
