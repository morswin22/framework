# framework
wordpress like framework

# how to use
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

 Made With ♥ By Patryk Janiak (https://morswin22.github.io/)
