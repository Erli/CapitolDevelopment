<?php


namespace App\Forms;

use App\Model\EmployeeManager;
use App\Model\FunctionManager;
use App\Presenters\BasePresenter;
use Nette;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


class EmployeeFormFactory extends Control {
    private $database;
    private $id;
    /**
     * @var EmployeeManager
     */
    private $employeeManager;
    /**
     * @var FunctionManager
     */
    private $functionManager;
    use \Nette\SmartObject;


    /**
     * FunctionFormFactory constructor.
     * @param Nette\Database\Connection $database
     * @param EmployeeManager $employeeManager
     * @param FunctionManager $functionManager
     */
    public function __construct(Nette\Database\Connection $database, EmployeeManager $employeeManager, FunctionManager $functionManager) {
        $this->database = $database;
        $this->employeeManager = $employeeManager;
        $this->functionManager = $functionManager;
    }


    /**
     * @param callable $onSuccess
     * @return Form
     */
    public function create(callable $onSuccess) {
        $form = new Form;
        $options = $this->functionManager->getAllFunctionsForSelect();

        $form->addInteger('code','Kód')->setRequired(true);
        $form->addText('name', 'Jméno')->setRequired(true);
        $form->addText('surname', 'Přijmení')->setRequired(true);
        $form->addMultiSelect('options', 'Funkce', $options)->setRequired(true);
        $form->addSubmit('send', 'Vytvořit');
        $form->onValidate[] = [$this, 'validateForm'];
        $form->onSuccess[] = function (Form $form) use ($onSuccess): void {
            $values = $form->getValues();
            $id = $this->employeeManager->add($values->code, $values->name, $values->surname);
            $this->employeeManager->insertAndUpdateFunctions($id, $values->options);

            $onSuccess();
        };
        BasePresenter::makeBootstrap4($form);
        return $form;
    }

    /**
     * @param Form $form
     * @return array
     */
    public function validateForm(Form $form)
    {
        $values = $form->getValues();
        if ($this->employeeManager->isCodeUsed($values->code)){
            $form['code']->addError('Tento kód je již použit.');
        }
        return [];
    }
}