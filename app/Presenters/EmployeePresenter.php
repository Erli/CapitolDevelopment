<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\EmployeeEditFormFactory;
use App\Forms\EmployeeFormFactory;
use App\Forms\SearchHasFunctionFormFactory;
use App\Model\EmployeeManager;
use Nette;
use Nette\Application\UI\Form;

final class EmployeePresenter extends BasePresenter
{
    /**
     * @var Nette\Database\Context
     */
    private $database;
    /**
     * @var EmployeeManager
     */
    private $employeeManager;

    /**
     * EmployeePresenter constructor.
     * @param Nette\Database\Context $database
     * @param EmployeeManager $employeeManager
     */
    public function __construct(Nette\Database\Context $database, EmployeeManager $employeeManager)
    {
        $this->database = $database;
        $this->employeeManager = $employeeManager;
    }

    public function renderDefault(): void
    {
        $this->template->anyVariable = 'any value';
    }


    /**
     * editace zaměstnance
     * @param $id
     * @throws Nette\Application\AbortException
     */
    public function actionEdit($id){
        $values = $this->employeeManager->getEmployeeById(intval($id));
        if (!$values) {
            $this->flashMessage('Zaměstnanec s tímto id neexistuje');
            $this->redirect('Employee:list');
        }
    }



    /**
     * Smazaní zaměstnance
     * @param $id
     * @throws Nette\Application\AbortException
     */
    public function actionDelete($id){
        $values = $this->employeeManager->deleteEmployeeById(intval($id));
        if (!$values) {
            $this->flashMessage('Zaměstnanec s tímto id neexistuje');
            $this->redirect('Employee:list');
        }
        $this->flashMessage('Zaměstnanec byl úspěšně smazán');
        $this->redirect('Employee:list');


    }



    /**
     * Render metoda pro list zaměstnanců
     */
    public function renderList(){
        $this->template->listEmployees  = $this->employeeManager->getAllEmployees();
    }


    /** @var EmployeeFormFactory @inject */
    public $employeeFormFactory;


    /**
     * Formulář pro vytvoření funkcce
     * @return EmployeeFormFactory
     */
    protected function createComponentEmployeeForm()
    {
        return $this->employeeFormFactory->create(function (): void {
            $this->flashMessage('Zaměstnanec byl úspěšně vytvořen');
            $this->redirect('Employee:list');
        });
    }


    /** @var EmployeeEditFormFactory @inject */
    public $employeeEditFormFactory;

    /**
     * Formulář pro editaci zaměstnanců
     * @return Nette\Application\UI\Form
     */
    protected function createComponentEmployeeEditForm()
    {
        return $this->employeeEditFormFactory->create($this->getParameter('id'), function (): void {
            $this->flashMessage('Zaměstnanec byl úspěšně upraven.');
            $this->redirect('Employee:list');
        });
    }



}