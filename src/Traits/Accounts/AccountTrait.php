<?php


namespace Aissaio\QuickbooksCrud\Traits\Accounts;


use App\Traits\DataStructure\ArrayManipulator;
use App\Traits\Model\QuickBooks\QuickBooksTrait;
use QuickBooksOnline\API\DataService\DataService;

trait AccountTrait
{
    use ArrayManipulator;

        protected $start_request = 'Select * from Account';


    public function getAccountWithClassification(DataService $dataService,$classification){
        if (is_null($dataService))
            $dataService = $this->getDataServiceOAuth2('company_2');

        $accounts = $dataService->Query("$this->start_request where Classification = '$classification' and Active ='true'");

        $this->to_array_objects($accounts);

    }



    public function getAccountByName(DataService $dataService,$name){

        $account = $dataService->Query("$this->start_request where NAME = '$name' ");
     
        return reset($account);
    }


    public function getAllAccount(DataService $dataService){

         if (is_null($dataService))
            $dataService = $this->getDataServiceOAuth2('company_2');

        $accounts = $dataService->Query("$this->start_request");

        $array_accounts = $this->to_array_objects($accounts);


        dd($array_accounts);
    }


}
