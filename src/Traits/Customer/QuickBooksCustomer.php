<?php


namespace Aissaio\QuickbooksCrud\Traits\Customer;


use App\Traits\Model\QuickBooks\QuickBooksTrait;
use App\User;
use Exception;
use Illuminate\Support\Arr;
use Laravel\Telescope\Http\Controllers\DumpController;
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\Data\IPPCustomer;
use QuickBooksOnline\API\Data\IPPEmailAddress;
use QuickBooksOnline\API\Data\IPPPhysicalAddress;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\IdsException;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Data\IPPIntuitEntity;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\QueryFilter\QueryMessage;
use QuickBooksOnline\API\ReportService\ReportName;
use Symfony\Component\VarDumper\Cloner\Data;

trait QuickBooksCustomer
{
//    use QuickBooksTrait;


    public $customers = [
        'guardian'
    ];


    protected $start_request_customer = 'Select * from Customer';


    public function getAllCustomer(DataService  $dataService=null){

        if (is_null($dataService))
            $dataService = $this->getDataServiceOAuth2('company_2');


        $customers = $dataService->Query("$this->start_request_customer ");


        return $this->to_array_objects($customers);

        // return $list;
    }


    public function getCustomerById(User $user,DataService $dataService=null){
        $resultingCustomerObj  = null;
        try {

            if (is_null($dataService))
                $dataService = $this->getDataServiceOAuth2('company_2');


            $resultingCustomerObj = $dataService->Query("Select * from Customer WHERE Id = '".($user->quickBook_id)."'");
//            $resultingCustomerObj = $dataService->Query("Select * from Customer WHERE Id = '72'");

            // type IppCustomer
            if($resultingCustomerObj)
            $resultingCustomerObj = reset($resultingCustomerObj);


            $this->putQuickBooKErrors(QuickBooksTrait::$ERROR_TOKEN_REFRESH,false);
            
        }
        catch(Exception $e) {
//            dd($e->getMessage());
                $this->putQuickBooKErrors(QuickBooksTrait::$ERROR_TOKEN_REFRESH,true);
            
        }

        return $resultingCustomerObj;
    }
    public function getCustomerByQuickBooksId($quick_id,DataService $dataService=null){
        $resultingCustomerObj  = null;
        try {

            $dataService = $this->getDataServiceOAuth2('company_2');
           
            $resultingCustomerObj = $dataService->Query("Select * from Customer WHERE Id = '".($quick_id)."'");
//            $resultingCustomerObj = $dataService->Query("Select * from Customer WHERE Id = '72'");

            // type IppCustomer
            if($resultingCustomerObj)
            $resultingCustomerObj = $resultingCustomerObj[0];

            // dd($resultingCustomerObj);

            $this->putQuickBooKErrors(QuickBooksTrait::$ERROR_TOKEN_REFRESH,false);
            
        }
        catch(Exception $e) {
           dd($e->getMessage());
                $this->putQuickBooKErrors(QuickBooksTrait::$ERROR_TOKEN_REFRESH,true);
            
        }

        return $resultingCustomerObj;
    }




    public function createCustomer(User $user=null){
        // dd('xx');
        // dd($user->role->type);

        if(in_array($user->role->type,$this->customers)){
            // dd('xx');
            try {

            $dataService = $this->getDataServiceOAuth2('company_2');
            $customerObj = Customer::create([
                "BillAddr" => [
                    "Line1"=>  $user->address,
                    "City"=>   $user->city,
                    "Country"=>  "UAE",
                ],
                "Title"=>  $user->title,
                "GivenName"=>  $user->last_name,
                "FamilyName"=>  $user->first_name,
                "Suffix"=>  $user->title,
                "FullyQualifiedName"=>  $user->first_name." ".$user->last_name,
                "CompanyName"=>  "Demo company ".rand(),
                "DisplayName"=>  $user->first_name." ".$user->last_name,
                "PrimaryPhone"=>  [
                    "FreeFormNumber"=>  $user->phone
                ],
                "PrimaryEmailAddr"=>  [
                    "Address" => $user->email
                ]
            ]);

            $resultingCustomerObj = $dataService->Add($customerObj);

            if($resultingCustomerObj!==null) {
                $resultingCustomerObj = (array)$resultingCustomerObj;
                $id = $resultingCustomerObj['Id'];
                if (!is_null($user))
                {
                    $user->quickBook_id = $id;
                    $user->quickBook_id_status = true;
                    
                    $user->save();
                };
            }
        } catch (Exception $e) {
            // dd($e->getMessage());
        }

    }




    }

    public function UpdateCustomer(User $user=null,$isActive='true'){
    //    dd($isActive);
        // dd('xx');
        

        // dump('xx');
        if(in_array($user->role->type,$this->customers)){
            try {
                // dd('xxx');
            $dataService = $this->getDataServiceOAuth2('company_2');
            // dd('xxx');
            $entities = $dataService->Query("SELECT * FROM Customer where Id = '".$user->quickBook_id."' and Active IN (true,false)");
            // dd($entities);   c     
            if($entities===null) {
                $this->createCustomer($user);
                return ;
            }
            $theCustomer = reset($entities);
            // dd($theCustomer);
            
            if($theCustomer===null) {
                $this->createCustomer($user);
                return ;
            }
            
            $customerObj = Customer::update($theCustomer,[
                "BillAddr" => [
                    "Line1"=>  $user->address,
                    "City"=>   $user->city,
                    "Country"=>  "UAE",
                ],
                "Title"=>  $user->title,
                "Active" => $isActive,
                "GivenName"=>  $user->last_name,
                "FamilyName"=>  $user->first_name,
                "Suffix"=>  $user->title,
                "FullyQualifiedName"=>  $user->first_name." ".$user->last_name,
                "CompanyName"=>  "Demo company ".rand(),
                "DisplayName"=>  $user->first_name." ".$user->last_name,
                "PrimaryPhone"=>  [
                    "FreeFormNumber"=>  $user->phone
                ],
                "PrimaryEmailAddr"=>  [
                    "Address" => $user->email
                ]
            ]);
            $resultingCustomerObj = $dataService->Add($customerObj);
            if($resultingCustomerObj!==null) {
                $resultingCustomerObj = (array)$resultingCustomerObj;
                $id = $resultingCustomerObj['Id'];
                $isActive =$resultingCustomerObj['Active'] ==="false" ? false : true;

                if (!is_null($user))
                {
                    $user->quickBook_id = $id;
                    $user->quickBook_id_status = $isActive;
                    $user->save();
                };
            }
        } catch (Exception $e) {
            // dd($e->getMessage());

        }
        }
    }
  



    public function getInvoiceById($id,$dataService){

            $entity = null;
            try {
            // dd('xxx');
            $entities = $dataService->Query("SELECT * FROM Invoice where Id = '".$id."'");
            
            $entity = reset($entities);
            
        } catch (Exception $e) {
        }
        

          return $entity;
        
        }


    public function getPaymentById($id,$dataService){

            $entity = null;
            try {
            // $dataService = $this->getDataServiceOAuth2('company_2');
            
            $entities = $dataService->Query("SELECT * FROM Payment where Id = '".$id."'");
            
            $entity = reset($entities);
            
        } catch (Exception $e) {
        }
        

          return $entity;
        
        }

    
    
        public function downloadInvoiceByEntity($entity,$dataService){
        $url = null;
         try {
            $this->check_if_folder_exist('/files/quickbooks/invoices');
            $url = $dataService->DownloadPDF($entity,public_path("/files/quickbooks/invoices"));
            
        } catch (Exception $e) {
            dd($e->getMessage());
        }
        
          return $url;
        
        }

    public function downloadPaymentByEntity($entity,$dataService){
        $url = null;
            try {
            
             $this->check_if_folder_exist('/files/quickbooks/payments');
            $url = $dataService->DownloadPDF($entity,public_path("/files/quickbooks/payments"));

        } catch (Exception $e) {

        }
        

          return $url;
        
        }
    


        private function getQuickbooks($withQuickBooks,&$user,$data){
            // dd('xx');
            if($withQuickBooks){
                if($user->quickBook_id===null)
                {
                // dd($data);
                $isActive = false;
                if(Arr::has($data,'active'))
                $isActive = $data['active']==='false' ? false : true;
                if($isActive)
                $this->createCustomer($user);
                }
                else{
                 $isActive = 'false';
                 if(Arr::has($data,'active'))
                 $isActive = $data['active'];
                 $this->UpdateCustomer($user,$isActive);
                 }
           }
           
        }
    

        public function check_if_folder_exist(string $folder){

            if(!is_dir(public_path($folder))){
                mkdir(public_path($folder,0777));
            }
        }
    

}

