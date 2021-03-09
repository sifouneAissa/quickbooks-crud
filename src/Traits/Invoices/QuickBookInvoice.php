<?php


namespace Aissaio\QuickbooksCrud\Traits\Invoices;

use Aissaio\QuickbooksCrud\Traits\Accounts\AccountTrait;
use Aissaio\QuickbooksCrud\Traits\QuickBooksTrait;
use App\User;
use Exception;
use QuickBooksOnline\API\Facades\Invoice;

trait QuickBookInvoice
{
    use QuickBooksTrait,AccountTrait;


    public function createInvoice(User $user=null,$account=null,$amount=null){

            try {

            $dataService = $this->getDataServiceOAuth2('company_2');
            $account = $this->getAccountByName($dataService,$account);
            
            $invoice = Invoice::create([
                "Line" => [
                    "Amount" => $amount,
                    "DetailType" => "SalesItemLineDetail",
                    "SalesItemLineDetail" => [
                        "Qty" => 1,
                        "ItemRef" => [
                            "value" => $account->Id
                        ]
                    ]
                ],
                "CustomerRef"=> [
                    "value"=> $user->quickBook_id
                ],
                "BillEmail" => [
                    "Address" => $user->email
                ]
            ]);

            $resultingObj = $dataService->Add($invoice);

            $error = $dataService->getLastError();

            if ($error) {
            }
            else {

            }

        
        } catch (Exception $e) {
            
        }

    }
}
