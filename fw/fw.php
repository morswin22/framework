<?php

function is_regex($str) {
    return !(@preg_match($str, null) === false);
}

class Framework {

    public function __construct($domainURL, $dir = '') {

        @session_start();

        $this->domainURL = $domainURL . $dir;
    
        $this->componets = array();

        $this->navbarActive = 'active';

        $this->db = array();
        $this->db_access = false;
        $this->db_users = array();

        $this->trafficEnabled = false;
        $this->traffic_access = false;
        $this->traffic_helper = $domainURL;
        $this->traffic_users = array();
    }

    // vanilla 

    function add_component($name, $path, $flags = array()) {
        $file = file_get_contents(str_replace('{{domain_url}}', $this->domainURL, $path));
        $file = str_replace('{{domain_url}}', $this->domainURL, $file);
        $this->components[$name] = $file;
    }

    function component($name, $flags = array()) {
        if (isset($this->components[$name])) {
            $res = $this->components[$name];
            if (count($flags) > 0) {
                foreach($flags as $fkey => $flag) {
                    switch ($fkey) {
                        case 'render':
                            foreach ($flag as $p => $value) {
                                switch ($p) {
                                    case 'title':
                                        $res = str_replace('{{title}}',$value,$res);
                                        break;
                                    case 'page':
                                        $res = str_replace('{{is:'.$value.'}}', $this->navbarActive, $res);
                                        break;
                                }
                            }
                            break;
                        case 'replace':
                            foreach ($flag as $p => $value) {
                                $res = str_replace('{{'.$p.'}}',$value,$res);
                            }
                            break;
                    }
                }
            }
            echo $res;
        } else {
            $this->error(500, 'This component does not exist.');
        }
    }

    // db exst

    function db_register($name, $pass) {
        $this->db_users[$name] = $pass;
    }
    function db_login($name, $pass) {
        if (isset($this->db_users[$name])) {
            if ($this->db_users[$name] === $pass) {
                $this->db_access = true;
                $_SESSION['fw-db-user'] = array('name'=>$name, 'pass'=>$pass);
            }
        }
    }
    function db_logout() {
        if (isset($_SESSION['fw-db-user'])) {
            unset($_SESSION['fw-db-user']);
        }
        $this->db_access = false;
    }

    function add_db($name, $params, $idp) {
        $this->db[$name] = new FrameworkDatabase($name, $params, $idp);
    }

    // login exst

    function register($data) {
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
        $checksum = true;
        if (!isset($data[$this->user_idp]))  $checksum = false;
        if (!isset($data[$this->user_chkp])) $checksum = false;

        if ($checksum == true) {
            if (is_file(__DIR__.'/users/'.$data[$this->user_idp].'.json')) {
                $user = $this->userConvert(json_decode(file_get_contents(__DIR__.'/users/'.$data[$this->user_idp].'.json'),true));
                if ($user[$this->user_chkp] === $data[$this->user_chkp]) {
                    $_SESSION['fw-user'] = $user;
                    $this->user = $user;
                } else {
                    $this->logout();
                    $this->error(500, 'Wrong chkp');
                }
            } else {
                $this->logout();
                $this->error(500, 'This user does not exist');
            }
        } else {
            $this->error(500, 'Login cheksum does not add up');
        }
    }

    function logout() {
        if (isset($this->user)) unset($this->user);
        if (isset($_SESSION['fw-user'])) unset($_SESSION['fw-user']);
    }

    function isLogged() {
        if (isset($this->user)) {
            return true;
        } else {
            if (isset($_SESSION['fw-user'])) {
                $this->login($_SESSION['fw-user']);
                return $this->isLogged();
            } else {
                return false;
            }
        }
    }

    private function login_cache() {
        if ($this->isLogged() == true) {
            $this->login($_SESSION['fw-user']);
        }
    }

    function edit_user($user_raw, $p_name, $p_value) {
        if ($p_name == $this->user_idp) {
            $this->error(500, 'Cannot change idp value in existing user');
        } else {
            $id = $user_raw[$this->user_idp];
            if (is_file(__DIR__.'/users/'.$id.'.json')) {
                $user = $this->userConvert(json_decode(file_get_contents(__DIR__.'/users/'.$id.'.json'),true));
                if (isset($user[$p_name])) {
                    $user[$p_name] = $p_value;
                    file_put_contents(__DIR__.'/users/'.$id.'.json',json_encode($this->userConvertRaw($user)));
                    if (isset($this->user)) {
                        if ($this->user[$this->user_idp] == $user[$this->user_idp]) {
                            $this->user = $user;
                            if ($this->user_chkp == $p_name) {
                                $this->login($user);
                            }
                        }
                    }
                } else {
                    $this->error(500, 'Undefined param in user structure -> '.$p_name);
                }
            } else {
                $this->error(500, 'Cannot find user from idp -> '.$id);
            }
        }
    }

    function edit($p_name, $p_value) {
        if ($this->isLogged()) {
            $this->edit_user($this->user, $p_name, $p_value);
        } else {
            $this->error(500, 'Currently there is no logged user');
        }
    }

    function rm_user($id) {
        if (is_file(__DIR__.'/users/'.$id.'.json')) {
            unlink(__DIR__.'/users/'.$id.'.json');
        }
    }

    private function userConvert($user_raw) {
        $user = array();
        foreach($this->user_params as $key => $param) {
            $user[$param] = $user_raw[$key];
        }
        return $user;
    }

    private function userConvertRaw($user) {
        $user_raw = array();
        foreach($this->user_params as $key => $param) {
            $user_raw[$key] = $user[$param];
        }
        return $user_raw;
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

    function prepareUsers($db_params,$id_param,$check_param) {
        $this->user_params = $db_params;
        $this->user_idp = $id_param;
        $this->user_chkp = $check_param;
        $this->login_cache();
    }

    // traffic exst

    function traffic($bool) {
        $this->trafficEnabled = $bool;
        if ($bool) {
            $viewingPath = $this->traffic_helper . $_SERVER['PHP_SELF'];
            $data = $this->getTraffic();
            if (isset($data['entries'][$viewingPath])) {
                $data['entries'][$viewingPath]++;
            } else {
                $data['entries'][$viewingPath] = 1;
            }
            file_put_contents(__DIR__.'/trafficdata/entries.json', json_encode($data));
        }
    }

    function getTraffic() {
        if (!is_file(__DIR__.'/trafficdata/entries.json')) { file_put_contents(__DIR__.'/trafficdata/entries.json', '{entries:{}}'); }
        return json_decode(file_get_contents(__DIR__.'/trafficdata/entries.json'),true);
    }

    function getSortedTraffic($flag = 'asc') {
        $data = $this->getTraffic();
        natsort($data['entries']);
        if ($flag == 'desc') {
            $data['entries'] = array_reverse($data['entries']);
        }
        return $data;
    }

    function traffic_register($name, $pass) {
        $this->traffic_users[$name] = $pass;
    }
    function traffic_login($name, $pass) {
        if (isset($this->traffic_users[$name])) {
            if ($this->traffic_users[$name] === $pass) {
                $this->traffic_access = true;
                $_SESSION['fw-traffic-user'] = array('name'=>$name, 'pass'=>$pass);
            }
        }
    }
    function traffic_logout() {
        if (isset($_SESSION['fw-traffic-user'])) {
            unset($_SESSION['fw-traffic-user']);
        }
        $this->traffic_access = false;
    }

    // error

    private function error($err = 500, $msg='Internal error.') {
        http_response_code($err);
        die('<pre><strong>Error:</strong> '.$msg.'<hr/></pre>');
    }

}

define('DB_PUSH', '');

class FrameworkDatabase {

    public function __construct($dbname, $cols, $idp) {

        $this->name = $dbname;
        $this->file = __DIR__.'/database/'.$dbname.'.json';

        $this->idp = $idp;

        if (!is_file($this->file)) {
            file_put_contents($this->file,'[]');
        }

        $this->setCols($cols);

    }

    function query($p = array(), $return = false) {
        return new FrameworkDatabaseQuery($this->file, $this->cols, $this->idp, $p);
    }

    function putData($id, $values) {
        foreach ($this->cols as $col) {
            if ($col == $this->idp) continue;
            if (!isset($values[$col])) {
                $values[$col] = "";
            }
        }
        $rows = json_decode(file_get_contents($this->file),true);
        if ($id === DB_PUSH) {
            $id = count($rows);
            $values[$this->idp] = $id;
            $rows[$id] = $this->convertRaw($values);
        } else {
            if (isset($rows[$id]) or ($id < count($rows))) {
                $values[$this->idp] = $id;
                $rows[$id] = $this->convertRaw($values);
            }
        }
        file_put_contents($this->file,json_encode($rows));
    }

    function editData($id, $p) {
        foreach ($p as $param=>$value) {
            if (!in_array($param,$this->cols)) {
                $this->error(500, 'Bad values structure while editing datebase -> '.$this->name);
            }
        }
        $rows = json_decode(file_get_contents($this->file),true);
        if (isset($rows[$id]) and $rows[$id] !== null) {
            $row = $this->convert($rows[$id]);
            foreach($p as $param=>$value){
                $row[$param] = $value;
            }
            $rows[$id] = $this->convertRaw($row);
            file_put_contents($this->file,json_encode($rows));
        }
    }

    function rmData($id) {
        $rows = json_decode(file_get_contents($this->file),true);
        if (isset($rows[$id])) {
            $rows[$id] = null;
            file_put_contents($this->file,json_encode($rows));
        }
    }

    private function setCols($cols) {
        $this->cols = $cols;
    }

    function convert($data_raw) {
        $data = array();
        foreach($this->cols as $key => $col) {
            $data[$col] = $data_raw[$key];
        }
        return $data;
    }

    function convertRaw($data) {
        $data_raw = array();
        foreach($this->cols as $key => $col) {
            $data_raw[$key] = $data[$col];
        }
        return $data_raw;
    }

    private function error($code =500, $msg='Undefined error') {
        http_response_code($code);
        die('<pre><strong>Database Error:</strong> '.$msg.'<hr></pre>');
    }

}

class FrameworkDatabaseQuery {

    public function __construct($file, $cols, $idp, $p) {

        $this->file = $file;
        $this->lastquery = '';

        $this->setCols($cols);
        $this->idp = $idp;

        $this->getData($p);

        $this->fetchCurrent = 0;

    }

    function getData($p = array()) {
        $this->lastquery = $p;
        $this->rows = json_decode(file_get_contents($this->file),true);
        if (is_array($p)) {
            $checksum = count($p);
            $new_rows = array();
            foreach($this->rows as $n => $row) {
                if ($row === null) continue;
                $row = $this->convert($row);
                $check = 0;
                foreach($p as $param => $value) {
                    if (is_regex($value)) {
                        if (@preg_match($value, $row[$param])) {
                            $check++;
                        }
                    } else {
                        if ($row[$param] == $value) {
                            $check++;
                        }
                    }
                }
                if ($check == $checksum) {
                    $new_rows[] = $this->rows[$n];
                } 
            }
            $this->rows = $new_rows;
        }
        $this->crows = count($this->rows);
        $n_rows = array();
        foreach($this->rows as $k=>$row) {
            $krow = $this->convert($row);
            if ($row !== null) {
                $n_rows[] = $krow;
            }
        }
        $this->rows = $n_rows;
    }

    function sort($param, $flag = 'asc') {

        $col = array();
        foreach ($this->rows as $row) {
            $col[] = $row[$param];
        }
        natsort($col);

        $rows = array();
        foreach($col as $k=>$v) {
            $rows[] = $this->rows[$k];
        }

        $this->rows = $rows;
        if ($flag == 'desc') {
            $this->rows = array_reverse($rows);
        }
    }

    function fetch() {
        while (isset($this->rows[$this->fetchCurrent])) {
            $this->fetchCurrent++;
            if ($this->rows[$this->fetchCurrent-1] !== null) {
                return $this->rows[$this->fetchCurrent-1];
            }
        }
        return false;
    }

    function putData($id, $values) {
        foreach ($this->cols as $col) {
            if ($col == $this->idp) continue;
            if (!isset($values[$col])) {
                $this->error(500, 'Bad values structure while putting into datebase -> '.$this->name);
            }
        }
        $rows = json_decode(file_get_contents($this->file),true);
        if ($id === DB_PUSH) {
            $id = count($rows);
            $values[$this->idp] = $id;
            $rows[$id] = $this->convertRaw($values);
        } else {
            if (isset($rows[$id]) or ($id < count($rows))) {
                $values[$this->idp] = $id;
                $rows[$id] = $this->convertRaw($values);
            }
        }
        file_put_contents($this->file,json_encode($rows));
        $this->getData($this->lastquery);
    }

    function editData($id, $p) {
        foreach ($p as $param=>$value) {
            if (!in_array($param,$this->cols)) {
                $this->error(500, 'Bad values structure while editing datebase -> '.$this->name);
            }
        }
        $rows = json_decode(file_get_contents($this->file),true);
        if (isset($rows[$id]) and $rows[$id] !== null) {
            $row = $this->convert($rows[$id]);
            foreach($p as $param=>$value){
                $row[$param] = $value;
            }
            $rows[$id] = $this->convertRaw($row);
            file_put_contents($this->file,json_encode($rows));
            $this->getData($this->lastquery);
        }
    }

    function rmData($id) {
        $rows = json_decode(file_get_contents($this->file),true);
        if (isset($rows[$id])) {
            $rows[$id] = null;
            file_put_contents($this->file,json_encode($rows));
            $this->getData($this->lastquery);
            $this->fetchCurrent--;
        }
    }

    private function setCols($cols) {
        $this->cols = $cols;
    }

    function convert($data_raw) {
        $data = array();
        foreach($this->cols as $key => $col) {
            $data[$col] = $data_raw[$key];
        }
        return $data;
    }

    function convertRaw($data) {
        $data_raw = array();
        foreach($this->cols as $key => $col) {
            $data_raw[$key] = $data[$col];
        }
        return $data_raw;
    }

    private function error($code =500, $msg='Undefined error') {
        http_response_code($code);
        die('<pre><strong>Database Error:</strong> '.$msg.'<hr></pre>');
    }

}

?>
