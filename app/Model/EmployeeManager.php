<?php


namespace App\Model;

use Exception;
use Nette;

class EmployeeManager {

    use Nette\SmartObject;

    private const
        TABLE_NAME = 'employees',
        COLUMN_ID = 'id',
        COLUMN_CODE = 'code',
        COLUMN_NAME = 'name',
        COLUMN_SURNAME = 'surname',
        TABLE_EMPLOYEES_FUNCTIONS = 'employees_functions',
        TABLE_EMPLOYEES_FUNCTIONS_ID_FUNCTIONS = 'id_functions',
        TABLE_EMPLOYEES_FUNCTIONS_ID_EMPLOYEES = 'id_employees';


    /** @var Nette\Database\Context */
    private $database;


    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }


    /**
     * Vrátí všechny funkce
     * @return Nette\Database\Table\Selection
     */
    public function getAllEmployees() {
        return $this->database->table(self::TABLE_NAME);
    }


    /**
     * Vrátí fukci dle id
     * @param int $id id zaměstnance
     * @return array|bool
     */
    public function getEmployeeById(int $id) {
        $employee = $this->database->table(self::TABLE_NAME)->get($id);
        if ($employee) {
            return $employee->toArray();
        }
        return false;
    }


    /**
     * Vrátí fukci dle id
     * @param int $id employee
     * @return Nette\Database\Table\Selection
     */
    public function getEmployeeFunctions(int $id) {
        return $this->database->table(self::TABLE_EMPLOYEES_FUNCTIONS)->where('id_employees=?', $id);

    }


    /**
     * Smaze záznam
     * @param int $id id zaměstnance
     * @return int počet smazaných řádků
     */
    public function deleteEmployeeById(int $id) {
        $result = $this->database->query('DELETE FROM '.self::TABLE_NAME.' WHERE id = ?', $id);
        return $result->getRowCount();
    }


    /**
     * Vytvoří zaměstnance a vrátí jeho id
     * @param int $code identifikator
     * @param string $name jméno
     * @param string $surname přijmení
     * @return int id zaměstnance
     */
    public function add(int $code, string $name, string $surname): int {
        $row = $this->database->table(self::TABLE_NAME)->insert([
            self::COLUMN_CODE => $code,
            self::COLUMN_NAME => $name,
            self::COLUMN_SURNAME => $surname,
        ]);
        return $row->id;
    }


    /**
     * Update function.
     * @param int $id id zaměstnance
     * @param int $code
     * @param string $name jméno
     * @param string $surname přijmení
     * @return void
     */
    public function update(int $id, int $code, string $name, string $surname): void {
        $this->database->query('UPDATE '.self::TABLE_NAME.' SET ', [
            self::COLUMN_CODE => $code,
            self::COLUMN_NAME => $name,
            self::COLUMN_SURNAME => $surname,
        ], 'WHERE id=?', $id);
    }


    /**
     * Aktualizuje vazební tabulku zaměstnanec / funkce.
     * @param int $id id zaměstnance
     * @param array $options pole s id funkcí
     * @return void
     */
    public function insertAndUpdateFunctions(int $id, array $options): void {

        $this->database->query('
            DELETE FROM '.self::TABLE_EMPLOYEES_FUNCTIONS.' 
            WHERE '.self::TABLE_EMPLOYEES_FUNCTIONS_ID_EMPLOYEES.' = ?', $id, ' 
                AND '.self::TABLE_EMPLOYEES_FUNCTIONS_ID_FUNCTIONS.' NOT IN(?)', array_values($options) ?: 0);
        foreach ($options as $item) {
            if (!$item) continue;
            $this->database->query('INSERT INTO '.self::TABLE_EMPLOYEES_FUNCTIONS, [
                self::TABLE_EMPLOYEES_FUNCTIONS_ID_FUNCTIONS => $item,
                self::TABLE_EMPLOYEES_FUNCTIONS_ID_EMPLOYEES => $id,
            ], 'ON DUPLICATE KEY UPDATE',
                [
                    self::TABLE_EMPLOYEES_FUNCTIONS_ID_FUNCTIONS => $item,
                    self::TABLE_EMPLOYEES_FUNCTIONS_ID_EMPLOYEES => $id,
                ]
            );
        }
    }


    /**
     * Vráti zaměstnance dle kódu funkce
     * @param $code
     * @return array zaměstnanců
     */
    public function getEmployeesWithFunction($code) {
        $result = [];
        $functions = $this->getFunctionByCode($code);
        if ($functions) {
            foreach ($functions->related('employees_functions') as $item) {
                $result[] = $item->employees;
            }
        }
        return $result;
    }


    /**
     * Vráti zaměstnance kteří nemají vybranou funkci
     * @param $code
     * @return array zaměstnanců
     */
    public function getEmployeesWithoutFunction($code) {
        $notUse = [];
        $employeesWithFunction = $this->getEmployeesWithFunction($code);
        foreach ($employeesWithFunction as $employee) {
            $notUse[] = $employee->id;
        }

        if (count($notUse)) {
            return $this->database->table('employees')->where('id NOT IN(?)', $notUse);
        }
        return [];
    }


    /**
     * Vráti funkci dle kódu
     * @param $code
     * @return Nette\Database\IRow|Nette\Database\Table\ActiveRow|null
     */
    public function getFunctionByCode($code) {
        return $this->database->table('functions')->where('code=?', $code)->fetch();

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