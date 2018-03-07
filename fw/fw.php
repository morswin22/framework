<?php

class Framework {

    public function __construct($domainURL) {

        @session_start();

        $this->domainURL = $domainURL;

        $this->commonMeta = file_get_contents($this->domainURL.'fw/common/meta.html');

        $this->commonLink = file_get_contents($this->domainURL.'fw/common/link.html');
        $this->commonLink = str_replace('{{DOMAIN_URL}}',$this->domainURL,$this->commonLink);
        
        $this->commonScript = file_get_contents($this->domainURL.'fw/common/script.html');
        $this->commonScript = str_replace('{{DOMAIN_URL}}',$this->domainURL,$this->commonScript);
    
        $this->header = file_get_contents($this->domainURL.'fw/common/header.html');
        $this->header = str_replace('{{DOMAIN_URL}}',$this->domainURL,$this->header);
    
        $this->navbar = file_get_contents($this->domainURL.'fw/common/navbar.html');
        $this->navbar = str_replace('{{DOMAIN_URL}}',$this->domainURL,$this->navbar);

        $this->footer = file_get_contents($this->domainURL.'fw/common/footer.html');
        $this->footer = str_replace('{{DOMAIN_URL}}',$this->domainURL,$this->footer);
    
        $this->navbarCurrent = 'active';
    }

    // html

    function header() {
        echo $this->header;
    }

    function navbar($page = "?") {
        $page = $this->pageName($page);
        $navbar = str_replace('{{is:'.$page.'}}',$this->navbarCurrent,$this->navbar);
        echo $navbar;
    }

    function footer() {
        echo $this->footer;
    }

    // use

    function set($set) {
        if (in_array($set, $this->sets)) {
            $this->pageSet = $set;
        } else {
            $this->error(500, 'Unknown page set -> '.$set);
        }
    }

    function title($page = "?") {
        $page = $this->pageName($page);
        if (isset($this->titles[$page])) {
            echo '<title>'.$this->titles[$page].'</title>'.PHP_EOL;
        } else {
            $this->error(500, 'This page does not have a title -> '.$page);
        }
    }

    function commonMeta() {
        echo $this->commonMeta;
    }
    function commonLink() {
        echo $this->commonLink;
    }
    function commonScript() {
        echo $this->commonScript;
    }

    function fullMeta($page="?") {
        $page = $this->pageName($page);
        $this->commonMeta();
        if(isset($this->metas[$page])) {
            $meta = file_get_contents(str_replace('{{DOMAIN_URL}}',$this->domainURL,$this->metas[$page]));
            echo str_replace('{{DOMAIN_URL}}',$this->domainURL,$meta);
        } else {
            $this->error(500, 'This page does not have meta tags -> '.$page);
        }
    }
    function fullLink($page="?") {
        $page = $this->pageName($page);
        $this->commonLink();
        if(isset($this->links[$page])) {
            echo str_replace('{{DOMAIN_URL}}',$this->domainURL,$this->links[$page]);
        } else {
            $this->error(500, 'This page does not have any links -> '.$page);
        }
    }
    function fullScript($page="?") {
        $page = $this->pageName($page);
        $this->commonScript();
        if(isset($this->scripts[$page])) {
            echo str_replace('{{DOMAIN_URL}}',$this->domainURL,$this->scripts[$page]);
        } else {
            $this->error(500, 'This page does not have any scripts -> '.$page);
        }
    }

    function register($data) {
        // check for every param
        $checksum = true;
        foreach($this->user_params as $param) {
            if (!isset($data[$param])) {
                $checksum = false;
            }
        }

        if ($checksum == true) {
            if (!is_file(__DIR__.'/users/'.$data[$this->user_idp].'.json')) {
                $new_user = array();
                foreach($this->user_params as $param) {
                    $new_user[] = $data[$param];
                }
                file_put_contents(__DIR__.'/users/'.$data[$this->user_idp].'.json',json_encode($new_user));
            } else {
                $this->error(500, 'This idp is taken');
            }
        } else {
            $this->error(500, 'Register checksum does not add up');
        }
    }

    function login($data) {
        // check for params (2)
        $checksum = true;
        if (!isset($data[$this->user_idp]))  $checksum = false;
        if (!isset($data[$this->user_chkp])) $checksum = false;

        if ($checksum == true) {
            if (is_file(__DIR__.'/users/'.$data[$this->user_idp].'.json')) {
                $user = $this->userConvert(json_decode(file_get_contents(__DIR__.'/users/'.$data[$this->user_idp].'.json'),true));
                if ($user[$this->user_chkp] === $data[$this->user_chkp]) {
                    echo 'logged';
                } else {
                    $this->error(500, 'Wrong chkp');
                }
            } else {
                $this->error(500, 'This user does not exist');
            }
        } else {
            $this->error(500, 'Login cheksum does not add up');
        }
    }

    function userConvert($user_raw) {
        $user = array();
        foreach($this->user_params as $key => $param) {
            $user[$param] = $user_raw[$key];
        }
        return $user;
    }

    function getUsers() {
        $users = array_slice(scandir(__DIR__.'/users'),2);
        $gitkeep = array_search('.gitkeep',$users);
        unset($users[$gitkeep]);
        foreach($users as $fname) {
            $this->users[] = $this->userConvert(json_decode(file_get_contents(__DIR__.'/users/'.$fname),true));
        }
        return $this->users;
    }

    // set

    function prepareUsers($db_params,$id_param,$check_param) {
        $this->user_params = $db_params;
        $this->user_idp = $id_param;     // idp  => id param
        $this->user_chkp = $check_param; // chkp => check for login param
    }

    function setNavbarCurrent($new) {
        $this->navbarCurrent = $new;
    }

    function setMetas($x) {
        $this->metas = $x;
    }
    function setLinks($x) {
        $this->links = $x;
    }
    function setScripts($x) {
        $this->scripts = $x;
    }

    function setTitles($titles) {
        $this->titles = $titles;
    }

    function setSets($sets) {
        $this->sets = $sets;
    }

    // generic

    private function pageName($page) {
        if ($page == "?") {
            if (isset($this->pageSet)) {
                $page = $this->pageSet;
            } else {
                $page = basename(__FILE__); 
            }
        }
        return $page;
    }

    private function error($err = 500, $msg='Internal error.') {
        http_response_code($err);
        die('<pre><strong>Error:</strong> '.$msg.'<hr/></pre>');
    }

}

?>