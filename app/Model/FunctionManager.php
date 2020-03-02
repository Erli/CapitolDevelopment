<?php

namespace App\Model;

use Exception;
use Nette;

class FunctionManager
{
    use Nette\SmartObject;

    private const
        TABLE_NAME = 'functions',
        COLUMN_ID = 'id',
        COLUMN_NAME = 'name',
        COLUMN_DESCRIPTION = 'description',
        COLUMN_CODE = 'code';


    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }


    /**
     * Vrátí všechny funkce
     * @return Nette\Database\Table\Selection
     */
    public function getAllFunctions()
    {
        return $this->database->table(self::TABLE_NAME)->select('*');
    }


    /**
     * Vrátí všechny funkce
     * @return array
     */
    public function getAllFunctionsForSelect()
    {
        return $this->database->table(self::TABLE_NAME)->fetchPairs('id','name');
    }


    /**
     * Vrátí fukci dle id
     * @param int $id
     * @return Nette\Database\Table\Selection
     */
    public function getFunctionById(int $id)
    {
        $function = $this->database->table(self::TABLE_NAME)->get($id);
        if ($function) {
            return $function->toArray();
        }
        return false;
    }

    /**
     * Smaze záznam
     * @param int $id
     * @return int pocet smazaných řádků
     */
    public function deleteFunctionById(int $id)
    {
        $result = $this->database->query('DELETE FROM '.self::TABLE_NAME.' WHERE id = ?', $id);
        return $result->getRowCount();
    }


    /**
     * Add new function.
     * @param int $code
     * @param string $name
     * @param string $description
     * @return void
     * @throws Exception
     */
    public function add(int $code, string $name, string $description): void
    {
        $this->database->table(self::TABLE_NAME)->insert([
            self::COLUMN_NAME => $name,
            self::COLUMN_CODE => $code,
            self::COLUMN_DESCRIPTION => $description,
        ]);
    }

    /**
     * Update function.
     * @param int $id
     * @param int $code
     * @param string $name
     * @param string $description
     * @return void
     */
    public function update(int $id, int $code, string $name, string $description): void
    {
        $this->database->query('UPDATE '.self::TABLE_NAME. ' SET ',[
            self::COLUMN_NAME => $name,
            self::COLUMN_CODE => $code,
            self::COLUMN_DESCRIPTION => $description,
        ], 'WHERE id=?',$id);
    }


    /**
     * Zda je kod již použit
     * @param int $code
     * @return bool
     */
    public function isCodeUsed(int $code): bool
    {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_CODE, $code)->count() ? true : false;
    }

}