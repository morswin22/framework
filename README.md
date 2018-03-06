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

Initialize framework in file with:

     include('path_to_fw/main.php'); 
     $fw->set('pagename');

 Output default head elements: 
 
     $fw->commonMeta()
     $fw->title()
     $fw->commonLink()
     $fw->commonScript()

 Output specific head elements for a pagename:
 
     $fw->fullMeta()
     $fw->title()
     $fw->fullLink()
     $fw->fullScript()

 Output html code:
 
     $fw->header()
     $fw->navbar()
     $fw->footer()
