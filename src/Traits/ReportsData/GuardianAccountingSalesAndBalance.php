<?php

namespace Aissaio\QuickbooksCrud\Traits\ReportsData;

use Aissaio\QuickbooksCrud\Traits\Customer\QuickBooksCustomer;
use App\Traits\Collections\CollectionOperation;

trait GuardianAccountingSalesAndBalance {

    use QuickBooksCustomer,CollectionOperation;


    public function mergeInvoiceAndPayments($data){
        $invoices = $data['invoices'];
        $payments = $data['payments'];
        $all = $this->getCollectionFromArray($invoices,null,'Invoice');
        $all = $this->getCollectionFromArray($payments,$all,'Payment');
        $all = $all->sortBy('TxnDate');
        return $this->getCollectionFromArray($all->toArray());
    }




    public function getTotalFromPaymentCustomer($payments){
        $total = 0;
        foreach ($payments as $payment) {
            $total = $total + $payment->TotalAmt;
        }

        return $total;
    }

    public function ManageTransactionReport($report,&$data){
        $rows = $report->Rows;
        $transactions = [];
        $rows = (array)$rows;
        if(count($rows))
        foreach ($rows as $r) {

            $r = (array)$r;

            if(count($r)) {
                foreach ($r[0]->Rows->Row as $row) {
                    $one = (array)$row->ColData;
                    // get the last one
                    $value = $one[count($one) - 1]->value;
                    if(count($one))
                    $date = $one[0]->value;
                    if((int)$value!==0)
                    array_push($transactions, ['date' => $date,'value' => $value]);
                }
                // get the total
               $total =  $r[0]->Summary->ColData;
               $value = $total[count($total) - 1]->value;
//                if((int)$value!==0)
               array_push($transactions, ["total"=>(int)$value]);
            }


            $data['transactions'] = $transactions;

        }
    }
   public function ManageSalesReport($report,&$data){

        $rows = $report->Rows;
        $columns = $report->Columns->Column;
        $sales = [];
        $rows = (array)$rows;

        // dd($rows);
        if(count($rows)){
        // dd($r[0]->Columns->Column);
        // dd('xx');
        foreach ($rows as $r) {
            
            $r = (array)$r;




                if (count($r)>1) {
                    // dd(count($r));
                    // dd($r);
                    
                    $rows = $r[1]->Summary->ColData;
//                    dd($r[1]->Summary->ColData);

                    // dd('xx');
                    
                    for ($i = 1; $i < count($rows) - 1; $i++) {
                        // dd((array)$columns[2]);
                        $onecolumns = (array)$columns[$i];
                        $index = 0;
                        foreach($onecolumns["MetaData"] as $m) {
                            $onecolumns["MetaData"][$index] =(array)$m;
                            $index++;
                        }
                        // dd($columns);
//                      dump($r[1]->Summary->ColData[$i]);
                        if((int)$rows[$i]->value!==0)
                        // array_push($sales,$rows[$i]->value);
                        array_push($sales,['date' =>$onecolumns,'value' => $rows[$i]->value]);
                    }

                    // get the total
//                    $total = $r[1]->Summary->ColData;
                    $value = $rows[count($rows) - 1]->value;
                    array_push($sales, ["total" => (int)$value]);
//                    dd($sales);
                }
            
            }


            $data['sales'] = $sales;

//            dd($sales);

        }
    }

    private function setToDefaultCustomer(&$report){
        $report["invoices"] = [];
        $report["payments"] = [];
        $report["all"] = [];
        $report["total_invoices"] = "0";
        $report["total_payments"] = "0";
        $report["balance"] = "0.00";
    }





}
