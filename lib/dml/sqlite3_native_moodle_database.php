<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 18/03/2019
 * Time: 22:10
 */

class sqlite3_native_moodle_database extends \moodle_database {

    /** @var SQLite3 the database object */
    protected $sqlite;

    protected $database_file_extension = '.sq3.php';

    public function driver_installed() {
        if (!extension_loaded('sqlite3')){
            return get_string('sqliteextensionisnotpresentinphp', 'install');
        }
        return true;
    }

    public function get_dbfamily() {
        return 'sqlite';
    }

    protected function get_dbtype() {
        return 'sqlite3';
    }

    protected function get_dblibrary() {
        return 'native';
    }

    public function get_name() {
        // TODO: Use language string.
        return 'SQLite';
    }

    public function get_configuration_help() {
        // TODO: Implement get_configuration_help() method.
    }

    /**
     * @inheritdoc
     * @throws dml_exception
     */
    public function connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions = null) {
        $driverstatus = $this->driver_installed();

        if ($driverstatus !== true) {
            throw new dml_exception('dbdriverproblem', $driverstatus);
        }

        $this->store_settings($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);

        try{
            // TODO: Enable flags / encryption key to be passed?
            $this->sqlite = new SQLite3($this->get_dbfilepath());
            return true;
        } catch (\Exception $ex) {
            throw new dml_connection_exception($ex->getMessage());
        }
    }

    public function get_server_info() {
        // TODO: Check this.
        return [
            'description' => 'SQLite',
            'version' => SQLite3::version()
        ];
    }

    protected function allowed_param_types() {
        // TODO: Check this.
        return SQL_PARAMS_QM | SQL_PARAMS_NAMED;
    }

    public function get_last_error() {
        return $this->sqlite->lastErrorMsg();
    }

    public function get_tables($usecache = true) {
        $tables = array();

        $sql = 'SELECT name FROM sqlite_master WHERE type="table" UNION ALL SELECT name FROM sqlite_temp_master WHERE type="table" ORDER BY name';
        /*if ($this->debug) {
            $this->debug_query($sql);
        }*/
        $rstables = $this->sqlite->query($sql);
        foreach ($rstables as $table) {
            $table = $table['name'];
            $table = strtolower($table);
            if ($this->prefix !== false && $this->prefix !== '') {
                if (strpos($table, $this->prefix) !== 0) {
                    continue;
                }
                $table = substr($table, strlen($this->prefix));
            }
            $tables[$table] = $table;
        }
        return $tables;
    }

    public function get_indexes($table) {
        $indexes = array();
        $sql = 'PRAGMA index_list('.$this->prefix.$table.')';
        /*if ($this->debug) {
            $this->debug_query($sql);
        }*/
        $rsindexes = $this->sqlite->query($sql);
        foreach($rsindexes as $index) {
            $unique = (boolean)$index['unique'];
            $index = $index['name'];
            $sql = 'PRAGMA index_info("'.$index.'")';
            /*if ($this->debug) {
                $this->debug_query($sql);
            }*/
            $rscolumns = $this->sqlite->query($sql);
            $columns = array();
            foreach($rscolumns as $row) {
                $columns[] = strtolower($row['name']);
            }
            $index = strtolower($index);
            $indexes[$index]['unique'] = $unique;
            $indexes[$index]['columns'] = $columns;
        }
        return $indexes;
    }

    public function get_columns($table, $usecache = true) {
        if ($usecache) {
            // TODO: Remove false and implement temptables.
            if (false && $this->temptables->is_temptable($table)) {
                if ($data = $this->get_temp_tables_cache()->get($table)) {
                    return $data;
                }
            } else {
                if ($data = $this->get_metacache()->get($table)) {
                    return $data;
                }
            }
        }

        $structure = array();

        // get table's CREATE TABLE command (we'll need it for autoincrement fields)
        $sql = 'SELECT sql FROM sqlite_master WHERE type="table" AND tbl_name="'.$this->prefix.$table.'"';
        /*if ($this->debug) {
            $this->debug_query($sql);
        }*/
        $createsql = $this->sqlite->querySingle($sql, true);
        if (!$createsql) {
            return false;
        }
        $createsql = $createsql['sql'];

        $sql = 'PRAGMA table_info("'. $this->prefix.$table.'")';
        /*if ($this->debug) {
            $this->debug_query($sql);
        }*/
        $rscolumns = $this->sqlite->query($sql);
        foreach ($rscolumns as $row) {
            $columninfo = array(
                'name' => strtolower($row['name']), // colum names must be lowercase
                'not_null' =>(boolean)$row['notnull'],
                'primary_key' => (boolean)$row['pk'],
                'has_default' => !is_null($row['dflt_value']),
                'default_value' => $row['dflt_value'],
                'auto_increment' => false,
                'binary' => false,
                //'unsigned' => false,
            );
            $type = explode('(', $row['type']);
            $columninfo['type'] = strtolower($type[0]);
            if (count($type) > 1) {
                $size = explode(',', trim($type[1], ')'));
                $columninfo['max_length'] = $size[0];
                if (count($size) > 1) {
                    $columninfo['scale'] = $size[1];
                }
            }
            // SQLite does not have a fixed set of datatypes (ie. it accepts any string as
            // datatype in the CREATE TABLE command. We try to guess which type is used here
            switch(substr($columninfo['type'], 0, 3)) {
                case 'int': // int integer
                    if ($columninfo['primary_key'] && preg_match('/'.$columninfo['name'].'\W+integer\W+primary\W+key\W+autoincrement/im', $createsql)) {
                        $columninfo['meta_type'] = 'R';
                        $columninfo['auto_increment'] = true;
                    } else {
                        $columninfo['meta_type'] = 'I';
                    }
                    break;
                case 'num': // number numeric
                case 'rea': // real
                case 'dou': // double
                case 'flo': // float
                    $columninfo['meta_type'] = 'N';
                    break;
                case 'var': // varchar
                case 'cha': // char
                    $columninfo['meta_type'] = 'C';
                    break;
                case 'enu': // enums
                    $columninfo['meta_type'] = 'C';
                    break;
                case 'tex': // text
                case 'clo': // clob
                    $columninfo['meta_type'] = 'X';
                    break;
                case 'blo': // blob
                case 'non': // none
                    $columninfo['meta_type'] = 'B';
                    $columninfo['binary'] = true;
                    break;
                case 'boo': // boolean
                case 'bit': // bit
                case 'log': // logical
                    $columninfo['meta_type'] = 'L';
                    $columninfo['max_length'] = 1;
                    break;
                case 'tim': // timestamp
                    $columninfo['meta_type'] = 'T';
                    break;
                case 'dat': // date datetime
                    $columninfo['meta_type'] = 'D';
                    break;
            }
            if ($columninfo['has_default'] && ($columninfo['meta_type'] == 'X' || $columninfo['meta_type']== 'C')) {
                // trim extra quotes from text default values
                $columninfo['default_value'] = substr($columninfo['default_value'], 1, -1);
            }
            $structure[$columninfo['name']] = new database_column_info($columninfo);
        }

        if ($usecache) {
            if ($this->temptables->is_temptable($table)) {
                $this->get_temp_tables_cache()->set($table, $structure);
            } else {
                $this->get_metacache()->set($table, $structure);
            }
        }

        return $structure;
    }

    protected function normalise_value($column, $value) {
        return $value;
    }

    public function change_database_structure($sql, $tablenames = null) {
        $this->get_manager(); // Includes DDL exceptions classes ;-)
        $sqls = (array)$sql;

        try {
            foreach ($sqls as $sql) {
                $result = true;
                $this->query_start($sql, null, SQL_QUERY_STRUCTURE);

                try {
                    $this->sqlite->exec($sql);
                } catch (\Exception $ex) {
                    $result = false;
                }
                $this->query_end($result);
            }
        } catch (ddl_change_structure_exception $e) {
            $this->reset_caches($tablenames);
            throw $e;
        }

        $this->reset_caches($tablenames);
        return true;
    }

    public function execute($sql, array $params = null) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);

        $result = true;
        $this->query_start($sql, $params, SQL_QUERY_UPDATE);

        try {
            $sth = $this->sqlite->prepare($sql);
            foreach($params as $name => $param) {
                $sth->bindParam($name, $param);
            }
            $sth->execute();
        } catch (\Exception $ex) {
            $result = false;
        }

        $this->query_end($result);
        return $result;
    }

    public function get_recordset_sql($sql, array $params = null, $limitfrom = 0, $limitnum = 0) {
        $result = true;

        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        $sql = $this->get_limit_clauses($sql, $limitfrom, $limitnum);
        $this->query_start($sql, $params, SQL_QUERY_SELECT);

        try {
            $sth = @$this->sqlite->prepare($sql);
            if ($sth === false) {
                $result = false;
            } else {
                foreach($params as $name => $param) {
                    $sth->bindParam($name, $param);
                }
                $res = $sth->execute();
                $result = $this->create_recordset($res);
            }

        } catch (\Exception $ex) {
            $result = false;
        }

        $this->query_end($result);
        return $result;
    }

    public function get_records_sql($sql, array $params = null, $limitfrom = 0, $limitnum = 0) {
        global $CFG;

        $rs = $this->get_recordset_sql($sql, $params, $limitfrom, $limitnum);
        if (!$rs->valid()) {
            $rs->close(); // Not going to iterate (but exit), close rs
            return false;
        }
        $objects = array();
        foreach($rs as $value) {
            $key = reset($value);
            if ($CFG->debugdeveloper && array_key_exists($key, $objects)) {
                debugging("Did you remember to make the first column something unique in your call to get_records? Duplicate value '$key' found in column first column of '$sql'.", DEBUG_DEVELOPER);
            }
            $objects[$key] = (object)$value;
        }
        $rs->close();
        return $objects;
    }

    public function get_fieldset_sql($sql, array $params = null) {
        $rs = $this->get_recordset_sql($sql, $params);
        if (!$rs->valid()) {
            $rs->close(); // Not going to iterate (but exit), close rs
            return false;
        }
        $result = array();
        foreach($rs as $value) {
            $result[] = reset($value);
        }
        $rs->close();
        return $result;
    }

    public function insert_record_raw($table, $params, $returnid = true, $bulk = false, $customsequence = false) {
        if (!is_array($params)) {
            $params = (array)$params;
        }

        if ($customsequence) {
            if (!isset($params['id'])) {
                throw new coding_exception('moodle_database::insert_record_raw() id field must be specified if custom sequences used.');
            }
            $returnid = false;
        } else {
            unset($params['id']);
        }

        if (empty($params)) {
            throw new coding_exception('moodle_database::insert_record_raw() no fields found.');
        }

        $fields = implode(',', array_keys($params));
        $qms    = array_fill(0, count($params), '?');
        $qms    = implode(',', $qms);

        $sql = "INSERT INTO {{$table}} ($fields) VALUES($qms)";
        if (!$this->execute($sql, $params)) {
            return false;
        }
        if (!$returnid) {
            return true;
        }
        if ($id = $this->sqlite->lastInsertRowID()) {
            return (int)$id;
        }
        return false;
    }

    public function insert_record($table, $dataobject, $returnid = true, $bulk = false) {
        $dataobject = (array)$dataobject;

        $columns = $this->get_columns($table);
        if (empty($columns)) {
            throw new dml_exception('ddltablenotexist', $table);
        }

        $cleaned = array();

        foreach ($dataobject as $field=>$value) {
            if ($field === 'id') {
                continue;
            }
            if (!isset($columns[$field])) {
                continue;
            }
            $column = $columns[$field];
            if (is_bool($value)) {
                $value = (int)$value; // prevent "false" problems
            }
            $cleaned[$field] = $value;
        }

        if (empty($cleaned)) {
            return false;
        }

        return $this->insert_record_raw($table, $cleaned, $returnid, $bulk);
    }

    public function import_record($table, $dataobject) {
        $dataobject = (object)$dataobject;

        $columns = $this->get_columns($table);
        $cleaned = array();
        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) {
                continue;
            }
            $cleaned[$field] = $value;
        }

        return $this->insert_record_raw($table, $cleaned, false, true, true);
    }

    public function update_record_raw($table, $params, $bulk = false) {
        $params = (array)$params;

        if (!isset($params['id'])) {
            throw new coding_exception('moodle_database::update_record_raw() id field must be specified.');
        }
        $id = $params['id'];
        unset($params['id']);

        if (empty($params)) {
            throw new coding_exception('moodle_database::update_record_raw() no fields found.');
        }

        $sets = array();
        foreach ($params as $field=>$value) {
            $sets[] = "$field = ?";
        }

        $params[] = $id; // last ? in WHERE condition

        $sets = implode(',', $sets);
        $sql = "UPDATE {{$table}} SET $sets WHERE id=?";
        return $this->execute($sql, $params);
    }

    public function update_record($table, $dataobject, $bulk = false) {
        $dataobject = (array)$dataobject;

        $columns = $this->get_columns($table);
        $cleaned = array();

        foreach ($dataobject as $field=>$value) {
            if (!isset($columns[$field])) {
                continue;
            }
            if (is_bool($value)) {
                $value = (int)$value; // prevent "false" problems
            }
            $cleaned[$field] = $value;
        }

        return $this->update_record_raw($table, $cleaned, $bulk);
    }

    public function set_field_select($table, $newfield, $newvalue, $select, array $params = null) {
        if ($select) {
            $select = "WHERE $select";
        }
        if (is_null($params)) {
            $params = array();
        }
        list($select, $params, $type) = $this->fix_sql_params($select, $params);

        if (is_bool($newvalue)) {
            $newvalue = (int)$newvalue; // prevent "false" problems
        }
        if (is_null($newvalue)) {
            $newfield = "$newfield = NULL";
        } else {
            // make sure SET and WHERE clauses use the same type of parameters,
            // because we don't support different types in the same query
            switch($type) {
                case SQL_PARAMS_NAMED:
                    $newfield = "$newfield = :newvalueforupdate";
                    $params['newvalueforupdate'] = $newvalue;
                    break;
                case SQL_PARAMS_QM:
                    $newfield = "$newfield = ?";
                    array_unshift($params, $newvalue);
                    break;
                default:
                    print_error('unknowparamtype', 'error', '');
            }
        }
        $sql = "UPDATE {{$table}} SET $newfield $select";
        return $this->execute($sql, $params);
    }

    public function delete_records_select($table, $select, array $params = null) {
        $sql = "DELETE FROM {{$table}}";
        if ($select) {
            $sql .= " WHERE $select";
        }
        return $this->execute($sql, $params);
    }

    public function sql_concat() {
        $arr = func_get_args();
        $s = implode(' || ', $arr);
        if ($s === '') {
            return " '' ";
        }
        // Add always empty string element so integer-exclusive concats
        // will work without needing to cast each element explicitly
        return " '' || $s ";
    }

    public function sql_concat_join($separator="' '", $elements=array()) {
        for ($n=count($elements)-1; $n > 0 ; $n--) {
            array_splice($elements, $n, 0, $separator);
        }
        $s = implode(' || ', $elements);
        if ($s === '') {
            return " '' ";
        }
        return " $s ";
    }

    protected function begin_transaction() {
        // TODO: Implement begin_transaction() method.
    }

    protected function commit_transaction() {
        // TODO: Implement commit_transaction() method.
    }

    protected function rollback_transaction() {
        // TODO: Implement rollback_transaction() method.
    }

    /**
     * Factory method that creates a recordset for return by a query. The generic pdo_moodle_recordset
     * class should fit most cases, but pdo_moodle_database subclasses can override this method to return
     * a subclass of pdo_moodle_recordset.
     * @param object $sth instance of PDOStatement
     * @return object instance of pdo_moodle_recordset
     */
    protected function create_recordset(SQLite3Result $result) {
        return new sqlite3_native_moodle_recordset($result);
    }

    /**
     * Returns the sql statement with clauses to append used to limit a recordset range.
     * @param string $sql the SQL statement to limit.
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return string the SQL statement with limiting clauses
     */
    protected function get_limit_clauses($sql, $limitfrom=0, $limitnum=0) {
        if ($limitnum) {
            $sql .= ' LIMIT '.$limitnum;
            if ($limitfrom) {
                $sql .= ' OFFSET '.$limitfrom;
            }
        }
        return $sql;
    }

    /**
     * Returns the file path for the database file, computed from dbname and/or dboptions.
     * If dboptions['file'] is set, then it is used (use :memory: for in memory database);
     * else if dboptions['path'] is set, then the file will be <dboptions path>/<dbname>.sq3.php;
     * else if dbhost is set and not localhost, then the file will be <dbhost>/<dbname>.sq3.php;
     * else the file will be <moodle data path>/<dbname>.sq3.php
     * @return string file path to the SQLite database;
     */
    protected function get_dbfilepath() {
        global $CFG;
        if (!empty($this->dboptions['file'])) {
            return $this->dboptions['file'];
        }
        if ($this->dbhost && $this->dbhost != 'localhost') {
            $path = $this->dbhost;
        } else {
            $path = $CFG->dataroot;
        }
        $path = rtrim($path, '\\/').'/';
        if (!empty($this->dbuser)) {
            $path .= $this->dbuser.'_';
        }
        $path .= $this->dbname.'_'.md5($this->dbpass).$this->database_file_extension;
        return $path;
    }
}