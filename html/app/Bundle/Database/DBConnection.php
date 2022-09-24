<?php
namespace App\Bundle\Database;

use Symfony\Component\Yaml\Exception\ParseException;

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
     * @throws \Exception
     */
    public function insert(string $table, array $values)
    {
        $selectFields = $this->selectInformationSchemaFields($table);
        $idName = null;

        $fields = [];
        foreach ($selectFields as $field) {
            if (isset($field['COLUMN_NAME'])) {
                $fields[$field['COLUMN_NAME']] = $field;
                if ($field['COLUMN_KEY'] == 'PRI') {
                    $idName = $field['COLUMN_NAME'];
                }
            }
        }

        $vals = [];
        foreach ($values as $key => $value) {
            if (in_array(strtoupper($fields[$key]['DATA_TYPE']), ['INTEGER', 'INT', 'SMALLINT', 'TINYINT', 'MEDIUMINT', 'BIGINT', 'FLOAT', 'DOUBLE'])) {
                $vals[] = mysqli_real_escape_string($this->db, $value);
            } else {
                $vals[] = '\'' . mysqli_real_escape_string($this->db, $value) . '\'';
            }
        }

        $this->dbQuery('INSERT INTO '.$table.' ('.implode(',', array_keys($values)).') VALUES ('.implode(',', $vals).');');

        $id = null;
        if ($idName) {
            $result = $this->dbQuery('SELECT MAX('.$idName.') AS maxdata FROM '.$table.';');
            if (mysqli_num_rows($result) > 0) {
                $fetch = mysqli_fetch_assoc($result);
                $id = $fetch['maxdata'];
            }
        } else {
            $result = $this->dbQuery('SELECT LAST_INSERT_ID() AS last_id;');
            if (mysqli_num_rows($result) > 0) {
                $fetch = mysqli_fetch_assoc($result);
                $id = $fetch['last_id'];
            }
        }

        return $id;
    }

    /**
     * @param string $table
     * @param string|array $fields
     * @param string|array $conditions
     * @return array
     * @throws \Exception
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
     * @return bool|\mysqli_result
     * @throws \Exception
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
     * @param string $table
     * @param string|array $fields
     * @return array
     * @throws \Exception
     */
    public function selectInformationSchemaFields(string $table, $fields = '*')
    {
        $selectFields = $this->select('information_schema.columns', $fields, ['table_schema' => $this->getDatabaseName(), 'table_name' => $table ]);
        if (! $selectFields) {
            throw new \Exception(sprintf('Not found fields in table `%s`', $table));
        }
        return $selectFields;
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