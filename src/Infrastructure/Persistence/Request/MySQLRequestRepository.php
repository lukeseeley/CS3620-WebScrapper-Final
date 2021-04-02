<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Request;

use Exception;
use PDO;

class MySQLRequestRepository {
    /**
     * @var PDO
     */
    private $db_connection;
    
    /**
     * MySQLItemRepository Constructor
     * @param string    $database_name
     * @param string    $ip
     * @param string    $username
     * @param string    $password
     */
    public function __construct(string $database_name, string $ip, string $username, string $password) {
        $dsn = 'mysql:dbname=' . $database_name . ';host=' . $ip;
        $this->db_connection = new PDO($dsn, $username, $password);
    }

    /**
     * @param string    $request
     * @return void
     */
    public function addRequest(string $request): void {
        $sql = "INSERT INTO Requests (Request_URL)
                VALUES ('" . $request . "')";
        
        try {
            $this->db_connection->exec($sql);
        }
        catch (\Exception $e) {
            print_r($e->getCode().': '.$e->getMessage());
        }
    }

    /**
     * @param string    $request
     * @return void
     */
    public function removeRequest(string $request): void {
        $sql = "DELETE FROM Requests WHERE Request_URL = '". $request . "'";
        try {
            $this->db_connection->exec($sql);
        }
        catch (\Exception $e) {
            print_r($e->getCode().': '.$e->getMessage());
        }
    }

    /**
     * @return string[]
     */
    public function findAll() {
        $sql = "SELECT * FROM Requests";
        $requests = [];

        try {
            $rows = $this->db_connection->query($sql)->fetchAll();
            foreach ($rows as $row) { //Builds an in memory array for returning
                $requests[] = $row['Request_URL'];
            }
    
            return $requests;
        }
        catch (\Exception $e) {
            throw new Exception($e->getCode().': '.$e->getMessage());
        }
    }
}