<?php

namespace Aissaio\QuickbooksCrud\Services;

use Aissaio\QuickbooksCrud\Repositories\quickBookRepository;
use Aissaio\QuickbooksCrud\Repositories\quickBookRepositoryInterface;
use Aissaio\QuickbooksCrud\Traits\Invoices\QuickBookInvoice;
use Aissaio\QuickbooksCrud\Traits\QuickBooksTrait;
use Aissaio\QuickbooksCrud\Traits\Reports\QuickBookReport;
use Aissaio\QuickbooksCrud\Traits\ReportsData\GuardianAccountingSalesAndBalance;
use Aissaio\QuickbooksCrud\Traits\ReportsData\ProfitAndLossSheetData;

class QuickBook {

    use QuickBookInvoice,QuickBookReport,ProfitAndLossSheetData,
    QuickBooksTrait,
    GuardianAccountingSalesAndBalance;

    protected $repository ; 

    
    public function __construct(quickBookRepository $repository)
    {
        $this->repository = $repository;
    }

    protected $incomes = [
        "Activities and events",
        "Revenue - General",//"General sales",
        "Other income",
        "Study charges",
        "?????????????",
        "?????????????",
        "?????????????",
        "?????????????",
        "?????????????",
    ]
    ;
    protected $expenses = [
        "Payroll Expenses", //"Salaries",
        "Petrol",
        "Office Expenses",
        "Legal and professional fees", //"Taxes & Licenses",
        "Rent or lease payments", //"Rent or Lease",
        "Other general and administrative expenses",//"Purchases",
        "Bank Charges",
        "Advertising",
        "Meals and Entertainment",
    ]
    ;


    // for the admin dashboard
    public function handleReportProfitAndLoss(){

       $report = $this->ProfitAndLossSheet();
       
        $report = json_encode($report);
        $report = json_decode($report,true);

        if(is_null($report)) $this->setToDefault($data);
        else $this->getProfitAndLossSheetData($report,$data,true);
        
        $data["details_expenses"] =   $this->tryToNotSendThisEmpty($data["details_expenses"],$this->expenses);
        $data["details_income"] =  $this->tryToNotSendThisEmpty($data["details_income"],$this->incomes);
        
        return $data;

    }



    public function handleReportGuardian($user,$date=null){

        $data = [];
        $dataService = null;
        $third = null;
        if($user!=null){
        $third = $this->getCustomerById($user,$dataService);
        // dd($date);
        $data['invoices'] = $this->InvoicesListByCustomerAndWeek($user,$dataService,$date);
        $data['payments'] = $this->PaymentsListByCustomerAndWeek($user,$dataService,$date);
        $data['total_payments'] = $this->getTotalFromPaymentCustomer($data['payments']);
        $data['total_invoices'] = $this->getTotalFromPaymentCustomer($data['invoices']);
        $data['all'] = $this->mergeInvoiceAndPayments($data);
        if($third)
        $data['balance'] = $third->Balance;
        else $data['balance'] = "0";
        }
        if($this->isQuickBooksErrors()) $this->setToDefaultCustomer($data);
        return $data;



    }


    

    public function downloadInvoice($id){

        $dataService = $this->getDataServiceOAuth2('company_2');
        
        $inv = $dataService->FindById("invoice", $id);
        
        return $this->downloadInvoiceByEntity($inv,$dataService);
    }

    
    public function downloadPayment($id){

        $dataService = $this->getDataServiceOAuth2('company_2');

        $pay =  $this->getPaymentById($id,$dataService);

        return $this->downloadPaymentByEntity($pay,$dataService);

    }



  
}
