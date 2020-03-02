<?php


namespace App\Forms;


use App\Model\EmployeeManager;
use App\Model\FunctionManager;
use App\Presenters\BasePresenter;
use Nette\Application\UI;
use Nette;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


class SearchFormFactory extends Control
{
    private $database;

    /**
     * @var FunctionManager
     */
    private $functionManager;
    /**
     * @var EmployeeManager
     */
    private $employeeManager;

    /**
     * FunctionFormFactory constructor.
     * @param Nette\Database\Connection $database
     * @param EmployeeManager $employeeManager
     */
    public function __construct(Nette\Database\Connection $database,EmployeeManager $employeeManager)
    {
        $this->database = $database;
        $this->employeeManager = $employeeManager;
    }

    /**
     * @param callable $onSuccess
     * @return Form
     */
    public function create(callable $onSuccess)
    {
        $form = new Form;

        $form->addInteger('code',null)->setRequired(true);
        $form->addSubmit('send', 'Vyhledat');
        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
            $values = $form->getValues();
            $onSuccess($values);
        };
        BasePresenter::makeBootstrap4($form);
        return $form;
    }

}