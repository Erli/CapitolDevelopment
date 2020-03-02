<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FunctionEditFormFactory;
use App\Forms\FunctionFormFactory;
use App\Model\FunctionManager;
use Nette;

final class FunctionsPresenter extends BasePresenter
{
    /**
     * @var FunctionManager
     */
    private $functionManager;


    /**
     * FunctionsPresenter constructor.
     * @param FunctionFormFactory $functionFormFactory
     * @param FunctionManager $functionManager
     */
    public function __construct(FunctionFormFactory $functionFormFactory, FunctionManager $functionManager)
    {
        $this->functionFormFactory = $functionFormFactory;
        $this->functionManager = $functionManager;
    }


    /**
     * editace funkce
     * @param $id
     * @throws Nette\Application\AbortException
     */
    public function actionEdit($id){
        $values = $this->functionManager->getFunctionById(intval($id));
        if (!$values) {
            $this->flashMessage('Funkce s tímto id neexistuje');
            $this->redirect('Functions:list');
        }
    }


    /**
     * Smazaní funkce
     * @param $id
     * @throws Nette\Application\AbortException
     */
    public function actionDelete($id){
        $values = $this->functionManager->deleteFunctionById(intval($id));
        if (!$values) {
            $this->flashMessage('Funkce s tímto id neexistuje');
            $this->redirect('Functions:list');
        }
        $this->flashMessage('Funkce byla úspěšně smazána');
        $this->redirect('Functions:list');
    }


    /**
     * Render metodapro list funkcí
     */
    public function renderList(){
        $this->template->listFunctions = $this->functionManager->getAllFunctions();
    }


    /** @var FunctionFormFactory @inject */
    public $functionFormFactory;


    /**
     * Formulář pro vytvoření funkcce
     * @return FunctionFormFactory
     */
    protected function createComponentFunctionForm()
    {
        return $this->functionFormFactory->create(function (): void {
            $this->flashMessage('Funkce byla úspěšně přidána.');
            $this->redirect('Functions:list');
        });
    }


    /** @var FunctionEditFormFactory @inject */
    public $functionEditFormFactory;

    /**
     * Formulář pro editaci funkcí
     * @return Nette\Application\UI\Form
     * @throws Nette\Application\AbortException
     */
    protected function createComponentFunctionEditForm()
    {
        return $this->functionEditFormFactory->create($this->getParameter('id'), function (): void {
            $this->flashMessage('Funkce byla úspěšně upravena.');
            $this->redirect('Functions:list');
        });
    }


}