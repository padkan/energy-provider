<?php

namespace App\Models;

use PDO;

class Product extends \Core\BaseModel {

    public $title;
    public $type;
    public $baseCost;
    public $additionalKwhCost;
    public $includedKwh;
    
    public function __construct($data = []) {
        $this->title = $data['name'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->baseCost = $data['baseCost'] ?? null;
        $this->additionalKwhCost = $data['additionalKwhCost'] ?? null;
        $this->includedKwh = $data['includedKwh'] ?? null;
    }

    /**
     * @return [type]
     */
    public static function getAll() {
        $db = static::getDB();
        $stmt = $db->query('SELECT * FROM products');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }

    /**
     * @param int $id
     * 
     * @return [type]
     */
    public static function findById(int $id) {
        $sql = 'SELECT * FROM products WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * @param string $title
     * 
     * @return [type]
     */
    public static function findByTitle(string $title) {
        $sql = 'SELECT * FROM products WHERE title = :title';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':title', $$title, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }

}