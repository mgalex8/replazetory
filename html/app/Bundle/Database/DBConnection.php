<?php
namespace App\Bundle\Database;

/**
 * Class DBConnection
 */
class DBConnection
{

    /**
     * @var string|null
     */
    protected $hostname;

    /**
     * @var string|null
     */
    protected $username;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var string|null
     */
    protected $database;

    /**
     * @var int|null
     */
    protected $port;

    /**
     * @var string|null
     */
    protected $socket;

    /**
     * @var false|\mysqli
     */
    protected $db;

    /**
     * @param string|null $hostname
     * @param string|null $username
     * @param string|null $password
     * @param string|null $database
     * @param int|null $port
     * @param string|null $socket
     */
    public function __construct(?string $hostname = null, ?string $username = null, ?string $password = null, ?string $database = null, ?int $port = null, ?string $socket = null)
    {
        $this->connect($hostname, $username, $password, $database, $port, $socket);
    }

    /**
     * @param string|null $hostname
     * @param string|null $username
     * @param string|null $password
     * @param string|null $database
     * @param int|null $port
     * @param string|null $socket
     * @return void
     */
    public function connect(?string $hostname = null, ?string $username = null, ?string $password = null, ?string $database = null, ?int $port = null, ?string $socket = null)
    {
        $this->hostname = $hostname;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;
        $this->socket = $socket;
        $this->db = mysqli_connect($hostname, $username, $password, $database, $port, $socket);
    }


    /**
     * @return void
     */
    public function disconnect()
    {
        mysqli_close($this->db);
    }

    /**
     * @param string $sql
     * @return bool|\mysqli_result
     * @throws \Exception
     */
    public function dbQuery(string $sql)
    {
        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            throw new \Exception('DATABASE ERROR: '.mysqli_error($this->db));
        }
        return $result;
    }

    /**
     * @param string $sql
     * @return bool|\mysqli_result
     * @throws \Exception
     */
    public function query(string $sql)
    {
        return $this->dbQuery($sql);
    }

    /**
     * @param string $table
     * @param array $values
     * @return string|int|null
     */
    public function insert(string $table, array $values)
    {
        array_walk($values, function (&$item, $val) {
            $item = mysqli_real_escape_string($this->db, $val);
        });
        $this->dbQuery('INSERT INTO '.$table.' ('.implode(',', array_keys($values)).') VALUES ('.implode(',', $values).');');

        $id = $this->dbQuery('SELECT LAST_INSERT_ID();');
        return $id;
    }

    /**
     * @param string|array $table
     * @param string|array $conditions
     * @return array
     */
    public function select(string $table, $fields = '*', $conditions = '')
    {
        if (is_array($fields)) {
            $fields = implode(', ', $fields);
        }
        if (is_array($conditions)) {
            $prepared = [];
            foreach($conditions as $name => $value) {
                $prepared[] = "`".$name."` = '".mysqli_real_escape_string($this->db, $value)."'";
            }
            $conditions = implode(' AND ', $prepared);
        }
        $result = $this->dbQuery('SELECT '.$fields.' FROM '.$table. ($conditions ? " WHERE ". $conditions : '') . ';');

        $data = [];
        if (mysqli_num_rows($result) > 0) {
            while($data[] = mysqli_fetch_assoc($result)) {}
        }
        return $data;
    }

    /**
     * @param string $table
     * @param string|array $conditions
     * @return bool
     */
    public function delete(string $table, $conditions = '')
    {
        if (is_array($conditions)) {
            $conditions = implode(' AND ', $conditions);
        }
        $result = $this->dbQuery('DELETE FROM '.$table. ($conditions ? " WHERE ". $conditions : '') . ';');
        return $result;
    }

    /**
     * @return string|null
     */
    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    /**
     * @param string|null $hostname
     */
    public function setHostname(?string $hostname): void
    {
        $this->hostname = $hostname;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getDatabaseName(): ?string
    {
        return $this->database;
    }

    /**
     * @param string|null $database
     */
    public function setDatabaseName(?string $database): void
    {
        $this->database = $database;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @param int|null $port
     */
    public function setPort(?int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return string|null
     */
    public function getSocket(): ?string
    {
        return $this->socket;
    }

    /**
     * @param string|null $socket
     */
    public function setSocket(?string $socket): void
    {
        $this->socket = $socket;
    }

}