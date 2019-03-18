<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 18/03/2019
 * Time: 23:14
 */

class sqlite3_native_moodle_recordset extends \moodle_recordset {

    /** @var SQLite3Result */
    private $result;
    protected $current;

    public function __construct($result) {
        $this->result = $result;
        $this->current = $this->fetch_next();
    }

    public function __destruct() {
        $this->close();
    }

    private function fetch_next() {
        if ($row = $this->result->fetchArray()) {
            $row = array_change_key_case($row, CASE_LOWER);
        }
        return $row;
    }

    public function current() {
        return (object)$this->current;
    }

    public function key() {
        // return first column value as key
        if (!$this->current) {
            return false;
        }
        $key = reset($this->current);
        return $key;
    }

    public function next() {
        $this->current = $this->fetch_next();
    }

    public function valid() {
        return !empty($this->current);
    }

    public function close() {
        if ($this->result) {
            $this->result->finalize();
            $this->result = null;
        }
        $this->current = null;
    }


}