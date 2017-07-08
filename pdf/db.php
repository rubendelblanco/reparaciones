<?php

class DB {

    private $c = null;

    public function __construct ($host, $user, $pass, $db, $charset) {
        $this->c = new mysqli($host, $user, $pass, $db);
        if (! ($this->c->set_charset($charset))) {
            die("Error loading $charset");
        }
        
        if (mysqli_connect_errno()) {
            die(mysqli_connect_error());
        }
    }

    public function num_rows () {
        return $this->c->affected_rows;
    }

    public function one ($sql) {
        $res = $this->c->query($sql);
        if ($this->c->affected_rows > 0) {
            return $res->fetch_array()[0];
        }
        return false;
    }

    public function row ($sql, $assoc = true) {

        $res = $this->c->query($sql);
        if ($this->c->affected_rows > 0) {
            return mysqli_fetch_array($res, $assoc);
        }

        return false;
    }

    public function matrix ($sql, $assoc = true) {

        $res = $this->c->query($sql);
        if ($res->num_rows > 0) {
            $result = [];
            while (($row = $res->fetch_array($assoc))) {
                array_push($result, $row);
            }
            return $result;
        }

        echo mysqli_error($this->c);

        return false;
    }

    public function insert ($sql) {
        $this->c->query($sql);
        return $this->c->insert_id;
    }
/* TODO
    public function update ($sql) {
        
    }

    public function delete ($sql) {

    }
*/
    function __detruct () {
        $this->c->close();
    }

}

?>