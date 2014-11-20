<?php
namespace Comments;

class MyPDO extends \PDO {
    /**
     * @param $statement String
     * @param $args Array
     * @return \PDOStatement
     */
    public function q($statement, $args){
        $st = $this->prepare($statement);
        $st->execute($args);
        return $st;
    }

    public function __construct($dsn, $username = null, $password = null, array $driver_options = null) {
        parent :: __construct($dsn, $username, $password, $driver_options);
        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
}
?>
