<?php


namespace App\Forms;


use App\Model\FunctionManager;
use App\Presenters\BasePresenter;
use Nette\Application\UI;
use Nette;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


class FunctionFormFactory extends Control
{
    private $database;
    private $id;
    use \Nette\SmartObject;

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
     * @param callable $onSuccess
     * @return FunctionFormFactory
     */
    public function create(callable $onSuccess)
    {
        $form = new Form;

        $form->addInteger('code','Kód')->setRequired(true);
        $form->addText('name','Název')->setRequired(true);
        $form->addTextArea('description','Popis')->setRequired(true);
        $form->addSubmit('send', 'Odeslat');
        $form->onValidate[] = [$this, 'validateForm'];
        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
            $values = $form->getValues();
            $this->functionManager->add($values->code, $values->name,$values->description);
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
        if ($this->functionManager->isCodeUsed($values->code)){
            $form['code']->addError('Tento kód je již použit.');
        }
       return [];
    }
}