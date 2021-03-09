<?php


namespace Aissaio\QuickbooksCrud\Traits\Reports;

use Aissaio\QuickbooksCrud\Traits\Accounts\AccountTrait;
use Aissaio\QuickbooksCrud\Traits\QuickBooksTrait;
use App\Traits\Date\DateOperations;
use App\User;
use Exception;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\ReportService\ReportName;

trait QuickBookReport
{
    use QuickBooksTrait,AccountTrait,DateOperations;




    public function ReportBalanceSheet(){

        $dataService = $this->getDataServiceOAuth2('company_2');
        $reportService = $this->getReportService($dataService);

        $reportService->setDateMacro(QuickBooksTrait::$macro_date_month);

        $reportService->setSummarizeColumnBy(QuickBooksTrait::$summarize_column_by_week);
        $reportService->setAccountingMethod(QuickBooksTrait::$accounting_method_accrual);


        return $reportService->executeReport(ReportName::BALANCESHEET);
    }





    public function ProfitAndLossSheet(){
        $report = null;
        try{
            
        $dataService = $this->getDataServiceOAuth2('company_2');
            
        $reportService = $this->getReportService($dataService);

        $reportService->setDateMacro(QuickBooksTrait::$macro_date_month);

        $reportService->setSummarizeColumnBy('Week');

        $reportService->setAccountingMethod(QuickBooksTrait::$accounting_method_accrual);

        $report = $reportService->executeReport(ReportName::PROFITANDLOSS);
        
        $this->putQuickBooKErrors(QuickBooksTrait::$ERROR_TOKEN_REFRESH,false);

        // dd($report);
        
        }
        catch (Exception $e) {
            
            $this->putQuickBooKErrors(QuickBooksTrait::$ERROR_TOKEN_REFRESH,true);
            // dd($e->getMessage());
        }


        return $report;

    }


    public function BalanceSheetByCustomer(){

        $dataService = $this->getDataServiceOAuth2('company_2');

        // dd($dataService);

        $reportService = $this->getReportService($dataService);

      //  $reportService->setDateMacro(QuickBooksTrait::$macro_date_month);
//        $customrs = $dataService->Query('Select * from Payment ');
//
////
//        dd($customrs);
      $reportService->setCustomer('67');
        //$reportService->setArpaid(ReportName::);
        //$reportService->setSummarizeColumnBy('Week');
        // customer ballunce sheet
        //
        $reportService->setAccountingMethod(QuickBooksTrait::$accounting_method_accrual);

        return $reportService->executeReport('TransactionList');

    }


    public function ListAccount(String $company='company_2',$type_account='Asset'){

        $dataService = $this->getDataServiceOAuth2($company);


        if ($type_account!=QuickBooksTrait::$account_all)
          $this->getAccountWithClassification($dataService,$type_account);
        else
            //$accounts = $dataService->Query("Select * from Account ");
            $this->getAllAccount($dataService);
//        foreach ($accounts as$account) {
//
//        }
    }



    public function TransactionListByCustomer(User $user,DataService &$dataService = null){
        
        $report = null;
        try {


            if(is_null($dataService))$dataService = $this->getDataServiceOAuth2('company_2');

            $reportService = $this->getReportService($dataService);

            
            $reportService->setCustomer($user->quickBook_id);
            $reportService->setDateMacro(QuickBooksTrait::$macro_date_month);

            $reportService->setSummarizeColumnBy(QuickBooksTrait::$summarize_column_by_week);
            $reportService->setAccountingMethod(QuickBooksTrait::$accounting_method_accrual);
            $reportService->setTransactionType(QuickBooksTrait::$receive_payment);

            $report = $reportService->executeReport(QuickBooksTrait::$TRANSACTIONLISTBYCUSTOMER);

            $this->putQuickBooKErrors(QuickBooksTrait::$ERROR_TOKEN_REFRESH,false);
            
        
        }

        catch (Exception $e){
                // dd($e->getMessage());
                $this->putQuickBooKErrors(QuickBooksTrait::$ERROR_TOKEN_REFRESH,true);
            
        }
        // dd('xx');

        return $report;
    }
    public function InvoicesListByCustomer(User $user,DataService &$dataService = null,$extra){
        
        $invoices = [];
        try {


            if(is_null($dataService))$dataService = $this->getDataServiceOAuth2('company_2');

        
            $query = "Select * from Invoice where TxnDate >= '".$extra['start']."'  AND TxnDate <= '".$extra['finish']."' and CustomerRef = '".$user->quickBook_id."'";

            $invoices  = $dataService->Query($query);


        }

        catch (Exception $e){
                $this->putQuickBooKErrors(QuickBooksTrait::$ERROR_TOKEN_REFRESH,true);
            
        }


        return $invoices;
    }
    public function PaymentsListByCustomer(User $user,DataService &$dataService = null,$extra){
        
        $payments = [];

        try {


            if(is_null($dataService))$dataService = $this->getDataServiceOAuth2('company_2');

        
            $query = "Select * from Payment  where TxnDate >= '".$extra['start']."'  AND TxnDate <= '".$extra['finish']."' and CustomerRef = '".$user->quickBook_id."'";

            $payments  = $dataService->Query($query);

        }

        catch (Exception $e){
                $this->putQuickBooKErrors(QuickBooksTrait::$ERROR_TOKEN_REFRESH,true);
            
        }
        return $payments;
    }
    public function SalesByCustomer(User $user,DataService $dataService = null){

        $report = null;
        try {


            if(is_null($dataService))$dataService = $this->getDataServiceOAuth2('company_2');

            $reportService = $this->getReportService($dataService);


            $reportService->setCustomer($user->quickBook_id);
            $reportService->setDateMacro(QuickBooksTrait::$macro_date_month);

            $reportService->setSummarizeColumnBy(QuickBooksTrait::$summarize_column_by_week);
            $reportService->setAccountingMethod(QuickBooksTrait::$accounting_method_accrual);
            $reportService->setTransactionType(QuickBooksTrait::$receive_payment);

            $report = $reportService->executeReport(ReportName::CUSTOMERSALES);
            $this->putQuickBooKErrors(QuickBooksTrait::$ERROR_TOKEN_REFRESH,false);
            
//            $report = $reportService->executeReport("SalesByCustomerTypeDetail");

        }
        catch (Exception $e){
            // dd($e->getMessage());
            $this->putQuickBooKErrors(QuickBooksTrait::$ERROR_TOKEN_REFRESH,true);
            
        }
        return $report;
    }


    public function InvoicesListByCustomerAndWeek(User $user,DataService &$dataService = null,$date=null){

        $invoices = [];
        try {
            
               if($date===null)
               $date = $this->getMonth();
               $res = $this->InvoicesListByCustomer($user,$dataService,$date);
               
               if($res)
               $invoices = $res;
        }



        catch (Exception $e){
                 
                $this->putQuickBooKErrors(QuickBooksTrait::$ERROR_TOKEN_REFRESH,true);
            
        }
        return $invoices;
    }
    public function PaymentsListByCustomerAndWeek(User $user,DataService &$dataService = null,$date=null){

        $payments = [];
        try { 
            
             if($date===null)
             $date = $this->getMonth();
             $res = $this->PaymentsListByCustomer($user,$dataService,$date);
             if($res)
             $payments = $res;
         }



        catch (Exception $e){
                
                $this->putQuickBooKErrors(QuickBooksTrait::$ERROR_TOKEN_REFRESH,true);
            
        }
        return $payments;
    }
}
