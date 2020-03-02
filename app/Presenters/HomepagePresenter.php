<?php

declare(strict_types=1);

namespace App\Presenters;


use App\Forms\SearchFormFactory;
use App\Model\EmployeeManager;

final class HomepagePresenter extends BasePresenter
{
    /**
     * @var EmployeeManager
     */
    private $employeeManager;

    /**
     * HomepagePresenter constructor.
     * @param EmployeeManager $employeeManager
     */
    public function __construct(EmployeeManager $employeeManager)
    {
        $this->employeeManager = $employeeManager;
    }

    /**
     * @param $code
     */
    public function renderHasFunction($code){
        $this->template->code = $code;
        $this->template->function = $this->employeeManager->getFunctionByCode($code);
        $this->template->listEmployees = $this->employeeManager->getEmployeesWithFunction($code);
    }
    /**
     * @param $code
     */
    public function renderHasNotFunction($code){
        $this->template->code = $code;
        $this->template->function = $this->employeeManager->getFunctionByCode($code);
        $this->template->listEmployees = $this->employeeManager->getEmployeesWithoutFunction($code);
    }

    /** @var SearchFormFactory @inject */
    public $searchFormFactory;

    /**
     * Formulář pro editaci zaměstnanců
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSearchHasCodeForm()
    {
        return $this->searchFormFactory->create(function ($values): void {
            $this->getPresenter()->redirect('Homepage:hasFunction', $values->code);
        });
    }

    /**
     * Formulář pro editaci zaměstnanců
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSearchHasNotCodeForm()
    {
        return $this->searchFormFactory->create(function ($values): void {
            $this->getPresenter()->redirect('Homepage:hasNotFunction', $values->code);
        });
    }
}
