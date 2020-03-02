<?php


namespace App\Forms;

use App\Model\FunctionManager;
use App\Presenters\BasePresenter;
use Nette\Application\UI;
use Nette;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


class FunctionEditFormFactory extends Control
{
    private $database;
    private $id;

    use Nette\SmartObject;

    /**
     * @var FunctionManager
     */
    private $functionManager;

    /**
     * FunctionFormFactory constructor.
     * @param Nette\Database\Connection $database
     * @param FunctionManager $functionManager
     */
    public function __construct(Nette\Database\Connection $database, FunctionManager $functionManager)
    {
        $this->database = $database;
        $this->functionManager = $functionManager;
    }

    /**
     * Vytvoreni formularepro editaci funkci
     * @param $id
     * @param callable $onSuccess
     * @return Form
     * @throws Nette\Application\AbortException
     */
    public function create($id, callable $onSuccess)
    {
        $form = new Form();

        $values = $this->functionManager->getFunctionById($id);
        if (!$values) {
            $this->redirect('Function:list');
        }
        $form->addHidden('id')->setDefaultValue($id);
        $form->addHidden('code_before')->setDefaultValue($values['code']);
        $form->addInteger('code', 'Kód')->setDefaultValue($values['code'] ?: null)->setRequired(true);
        $form->addText('name', 'Název')->setDefaultValue($values['name'] ?: null)->setRequired(true);
        $form->addText('description', 'Popis')->setDefaultValue($values['description'] ?: null)->setRequired(true);
        $form->addSubmit('send', 'Upravit');
        $form->onValidate[] = [$this, 'validateForm'];
        $form->onSuccess[] = function (Form $form) use ($onSuccess): void {
            $values = $form->getValues();
            $this->functionManager->update($values->id,$values->code, $values->name, $values->description);
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
        if ($this->functionManager->isCodeUsed($values->code) && $values->code != $values->code_before) {
            $form['code']->addError('Tento kód je již použit.');
        }
        return [];

    }
}