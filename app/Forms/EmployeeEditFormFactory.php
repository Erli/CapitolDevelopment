<?php


namespace App\Forms;


use App\Model\EmployeeManager;
use App\Model\FunctionManager;
use App\Presenters\BasePresenter;
use Nette;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


class EmployeeEditFormFactory extends Control {
    private $database;
    private $id;
    use Nette\SmartObject;

    /**
     * @var EmployeeManager
     */
    private $employeeManager;
    /**
     * @var FunctionManager
     */
    private $functionManager;


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
     * Vytvoreni formularepro editaci funkci
     * @param $id
     * @param callable $onSuccess
     * @return Form
     * @throws Nette\Application\AbortException
     */
    public function create($id, callable $onSuccess) {
        $form = new Form;
        $values = $this->employeeManager->getEmployeeById($id);
        $options = $this->functionManager->getAllFunctionsForSelect();
        $optionsDefault = $this->employeeManager->getEmployeeFunctions($id);

        if (!$values) {
            $this->redirect('Employee:list');
        }
        $form->addHidden('id')->setDefaultValue($id);
        $form->addHidden('code_before')->setDefaultValue($values['code']);
        $form->addInteger('code', 'Kód')->setDefaultValue($values['code'])->setRequired(true);
        $form->addText('name', 'Jméno')->setDefaultValue($values['name'] ?: null)->setRequired(true);
        $form->addText('surname', 'Přijmení')->setDefaultValue($values['surname'] ?: null)->setRequired(true);
        $form->addMultiSelect('options', 'Funkce', $options)->setDefaultValue(array_keys($optionsDefault->fetchAssoc('id_functions')))->setRequired(true);
        $form->addSubmit('send', 'Upravit');
        $form->onValidate[] = [$this, 'validateForm'];
        $form->onSuccess[] = function (Form $form) use ($onSuccess): void {
            $values = $form->getValues();
            $this->employeeManager->update($values->id, $values->code, $values->name, $values->surname);
            $this->employeeManager->insertAndUpdateFunctions($values->id, $values->options);
            $onSuccess();
        };

        BasePresenter::makeBootstrap4($form);
        return $form;
    }


    /**
     * @param Form $form
     * @return array
     */
    public function validateForm(Form $form) {
        $values = $form->getValues();
        if ($this->employeeManager->isCodeUsed($values->code) && $values->code != $values->code_before) {
            $form['code']->addError('Tento kód je již použit.');
        }
        return [];

    }
}