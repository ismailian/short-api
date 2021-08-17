<?php

namespace Core;

class Database
{

    /**
     * @var String $dbHostname The database hostname.
     */
    private $dbHostname = "localhost"; 

    /**
     * @var String $dbUsername The database username. 
     */
    private $dbUsername = ""; 

    /**
     * @var String $dbPassword The database password.
     */
    private $dbPassword = ""; 

    /**
     * @var String $dbDatabase The database name.
     */
    private $dbDatabase = ""; 

    /**
     * @var Mixed $dbResource The database resource. 
     */
    private $dbResource;

    /**
     * @var String $dbQuery The last stored query.
     */
    public $dbQuery;

    /**
     * @var Array $keyContainer Contains keys.
     */
    private $keyContainer = [];

    /**
     * @var Array $valueContainer Contains values.
     */
    private $valueContainer = [];

    /* */
    public function __construct($hostname, $username, $password, $database)
    {
        /* set the properties */
        $this->dbHostname = $hostname;
        $this->dbUsername = $username;
        $this->dbPassword = $password;
        $this->dbDatabase = $database;

        /* connect to database */
        $this->dbResource = new \mysqli(
            $this->dbHostname, $this->dbUsername, $this->dbPassword, $this->dbDatabase,
        );

        /* check if there is any errors */
        if (!$this->dbResource) {
            die($this->dbResource->connect_error);
        }
    }

    /**
     * Insert new resource into the database.
     * @param String $table The table to insert the resource into.
     * @param Array $keyValuePair The values to be inserted.
     * @return Object $this return the instance of this object.
     */
    public function insert(String $table, Array $keyValuePair)
    {
        $this->dbQuery = "INSERT INTO " . $table;
        $keys = array_keys($keyValuePair);
        $values = array_values($keyValuePair);
        $this->dbQuery .= " (" . implode(', ', $keys) . ") VALUES";
        for ($i=0; $i < count($values); $i++) 
        { 
            $values[$i] = "?";
        }
        $this->dbQuery .= " (" . implode(', ', $values) . ")";

        array_push($this->keyContainer, ...$keys);
        array_push($this->valueContainer, ...array_values($keyValuePair));
        return $this;
    }

    /**
     * Select fields from a table
     * @param Array $fields The fields to select.
     * @return Object $this Returns this instance of database. 
     */
    public function select(String $table, Array $fields = null)
    {
        $this->dbQuery = "SELECT * FROM " . $table;
        if (!is_null($fields) && count($fields) > 0) {
            $this->dbQuery = str_replace('*', implode(',', $fields) , $this->dbQuery);
        }
        return $this;
    }

    /**
     * Update resource on a table.
     * @param String $table The table to update from.
     * @param Array $fields The fields to update.
     * @return Object $this Returns this instance of database.
     */
    public function update(String $table, Array $keyValuePair)
    {
        $this->dbQuery = "UPDATE " . $table . " SET";
        $keys = array_keys($keyValuePair);

        foreach ($keyValuePair as $key => $value) {
            $this->dbQuery .= " " . $key . "=?, ";
        }
        $this->dbQuery = substr($this->dbQuery, 0, strrpos($this->dbQuery, ", "));

        array_push($this->keyContainer, ...$keys);
        array_push($this->valueContainer, ...array_values($keyValuePair));
        return $this;
    }

    /**
     * Delete a resource from database.
     * @param String $table The table to delete from.
     * @return Object $this returns this instance of database.
     */
    public function delete(String $table)
    {
        $this->dbQuery = "DELETE FROM " . $table;
        return $this;
    }

    /**
     * Truncate a table.
     * @param String $table The table to truncate.
     */
    public function truncate(String $table)
    {
        return $this->query("TRUNCATE " . $table);
    }

    /**
     * Select/Update/Delete the corresponding elements based on the given values.
     * @param Array $keyValuePair The values with which to find elements.
     */
    public function where(Array $keyValuePair)
    {
        if (strlen($this->dbQuery) == 0) return $this;
        if (count($keyValuePair) > 0) {
            $keys = array_keys($keyValuePair);
            $where = " where ";

            for ($i=0; $i < count($keyValuePair); $i++) { 
                $where .= $keys[$i] . " = ? AND ";
            }
            $this->dbQuery .= substr($where, 0, strrpos($where, " AND "));
            $this->keyContainer = [];
            $this->valueContainer = [];
            array_push($this->keyContainer, ...$keys);
            array_push($this->valueContainer, ...array_values($keyValuePair));
            return $this;
        }
    }

    /**
     * Submit a query and return results
     */
    public function submit()
    {
        if ($this->bind()) {
            $this->currentStatement->execute();
            $result = $this->currentStatement->get_result();
            return is_bool($result) ? $result : $result->fetch_all(MYSQLI_ASSOC);
        }
        if ($result = $this->dbResource->query($this->dbQuery)) {
            return (is_bool($result)) ? $result : $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    /**
     * Bind params to their corresponding values.
     * @param Array $keys The keys to bind.
     * @param Array $values The values to bind.
     * @return Object|False $this Returns the instance of this object.
     */
    private function bind()
    {
        if (count($this->keyContainer) > 0 && count($this->valueContainer)) {
            $this->currentStatement = $this->dbResource->prepare($this->dbQuery);
            if ($this->currentStatement != false) {
                for ($i=0; $i < count($this->keyContainer); $i++) { 
                    $this->keyContainer[$i] = "s";
                }
                $this->currentStatement->bind_param(implode('', $this->keyContainer), ...$this->valueContainer);
                return $this;
            }
        }
        return false;
    }

    /**
     * Submits a query to the database.
     * @param String $query The query to submit.
     * @param Mixed $bindKeys The keys to bind to.
     * @param Mixed $bindValues The values to bind with.
     */
    public function query(String $query, Array $bindKeys = null, Array $bindValues = null)
    {
        if (!is_null($bindKeys) && !is_null($bindValues)) {
            array_push($this->keyContainer, ...$bindKeys);
            array_push($this->valueContainer, ...$bindValues);
            $this->dbQuery = $query;
            return $this->submit();
        }
        return $this->dbResource->query($query);
    }

    /**
    * 
    */
    public function __destruct()
    {
        $this->keyContainer = [];
        $this->valueContainer = [];
        $this->dbQuery = "";
        unset($this->currentStatement);
    }

}
