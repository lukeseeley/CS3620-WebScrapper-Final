<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Item;

use App\Domain\Item\Item;
use App\Domain\Item\ItemNotFoundException;
use Exception;
use App\Domain\Item\ItemRepository;
use PDO;

class MySQLItemRepository implements ItemRepository {
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
     * @param Item  $item
     * @return void
     */
    public function addItem($item): void {
        $id = $item->getId();
        $name = $item->getName();
        $cost = $item->getPrice();
        $desc = $item->getDescription();

        $sql = "INSERT INTO Items (item_id, Name, Cost, Description)
                VALUES (". $id .", '". $name . "', ". $cost . ", '" . $desc . "')";
        
        try {
            $this->db_connection->exec($sql);
        }
        catch (\Exception $e) {
            print_r($e->getCode().': '.$e->getMessage());
        }
    }

    /**
     * @param Item   $item
     * @throws ItemNotFoundException
     * @return void
     */
    public function updateById($item): void {
        $id = $item->getId();
        $name = $item->getName();
        $cost = $item->getPrice();
        $desc = $item->getDescription();

        $sql = "UPDATE Items SET Name = '" . $name . "', Cost = " . $cost . ", Description = '" . $desc . "'
                WHERE item_id = " . $id;
        try {
            $this->db_connection->exec($sql);
        }
        catch (\Exception $e) {
            print_r($e->getCode().': '.$e->getMessage());
        } 
    }

    /**
     * @param int   $id
     * @return void
     */
    public function removeById($id): void {
        $sql = "DELETE FROM Items WHERE item_id = ". $id;
        try {
            $this->db_connection->exec($sql);
        }
        catch (\Exception $e) {
            print_r($e->getCode().': '.$e->getMessage());
        }
    }

    /**
     * @return Item[]
     * @throws Exception
     */
    public function findAll(): array {
        $sql = "SELECT item_id, Name, FORMAT(Cost, 2) AS Cost, Description FROM Items";
        $items = [];

        try {
            $rows = $this->db_connection->query($sql)->fetchAll();
            foreach ($rows as $row) { //Builds an in memory array for returning
                $items[] = new Item((int)$row['item_id'], $row['Name'], (float)$row['Cost'], $row['Description']);
            }
    
            return array_values($items);
        }
        catch (\Exception $e) {
            throw new Exception($e->getCode().': '.$e->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Item
     * @throws ItemNotFoundException
     * @throws Exception
     */
    public function findItemOfId(int $id): Item {
        $sql = "SELECT item_id, Name, FORMAT(Cost, 2) AS Cost, Description FROM Items WHERE item_id = ". $id;
        
        try {
            $response = $this->db_connection->query($sql);
    
            if($response->rowCount() != 1) {
                throw new ItemNotFoundException();
            }
            $row = $response->fetchAll()[0];
            return new Item((int)$row['item_id'], $row['Name'], (float)$row['Cost'], $row['Description']);
        }
        catch (\Exception $e) {
            throw new Exception($e->getCode().': '.$e->getMessage());
        }
    }
}