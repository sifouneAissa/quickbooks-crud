<?php


namespace Aissaio\QuickbooksCrud\Traits;

use Aissaio\QuickbooksCrud\Repositories\quickBookRepository;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\ReportService\ReportService;

trait QuickBooksTrait
{

    // macro date
    public static $macro_date_month= 'This%20Month';

        // macro date
    public static $macro_date_week= 'This%20Week';



    // summarize
    public static $summarize_column_by_week = 'Week';

    // Accounting Method
    public static $accounting_method_accrual = "Accrual";


    // classification type
    public static $account_asset = 'Asset';
    public static $account_expense = 'Expense';
    public static $account_equity = 'Equity';

    public static $account_all = 'All';



    // names

    //
    public static $account_receivable_name = 'Accounts Receivable (A/R)';
    // bank
    public static $account_Cash_on_hand = 'Cash on hand';
    public static $account_FAB_Bank = 'FAB Bank';
    // income
    public static $account_activities_and_events = 'Activities and events';
    public static $account_donation_and_supports = 'Donation and supports';
    public static $account_services = 'Services';


    // errors

    public static $ERROR_TOKEN_REFRESH= 'ERROR_TOKEN_REFRESH';



    // transcations types
    public static $receive_payment = 'ReceivePayment';


    // verifications columns
    public static $account_classification = 'Classification';

    // Reports
    public  static  $TRANSACTIONLISTBYCUSTOMER = "TransactionListByCustomer";



    /**
 * create a data service api for quickbooks data
 * @param string $company
 * @return DataService
 * @throws \QuickBooksOnline\API\Exception\SdkException
 * @throws \QuickBooksOnline\API\Exception\ServiceException
 */
    public function getDataServiceOAuth2($company = 'company_2'){
        if($this->repository===null)
        $this->repository = new quickBookRepository();
        $config = $this->repository->getCurrent();
        

        $oauth2LoginHelper = new OAuth2LoginHelper($config->client_id,$config->client_secret);
        $accessTokenObj = $oauth2LoginHelper->refreshAccessTokenWithRefreshToken(
            $config->refreshTokenKey);
        $accessTokenValue = $accessTokenObj->getAccessToken();
        $refreshTokenValue = $accessTokenObj->getRefreshToken();
        // update the refresh token 
        $this->repository->edit(
            ["refreshTokenKey" => $refreshTokenValue,"isCurrent"=>true]
            ,$config->id);
        
            
        
        return DataService::Configure(array(
            'auth_mode' => 'oauth2',
            // 'ClientID' => config("quickbooks.$company.client_id"),
            'ClientID' => $config->client_id,
            // 'ClientSecret' =>  config("quickbooks.$company.client_secret"),
            'ClientSecret' =>  $config->client_secret,
            // 'RedirectURI' => config("quickbooks.$company.oauth_redirect_uri") ,
            // 'scope' => config("quickbooks.$company.oauth_scope"),
            'RedirectURI' => $config->oauth_redirect_uri,
            'scope' => $config->oauth_scope,
            'baseUrl' => $config->baseUrl,  
            // 'baseUrl' => "development",  
            // 'QBORealmID' => config("quickbooks.$company.QBORealmID"),
            'QBORealmID' => $config->QBORealmID,
            'accessTokenKey' =>$accessTokenValue,
            'refreshTokenKey' => $refreshTokenValue,

        ));
    }


    /**
     * create a data service api for quickbooks data
     * @param string $company
     * @return DataService
     * @throws \QuickBooksOnline\API\Exception\SdkException
     * @throws \QuickBooksOnline\API\Exception\ServiceException
     */
    public function getDataServiceOAuth1($company = 'company_1'){

        // initialise the Auth2 Helper
        $oauth2LoginHelper = new OAuth2LoginHelper(config("quickbooks.$company.client_id"),config("quickbooks.$company.client_secret"));
        $accessTokenObj = $oauth2LoginHelper->refreshAccessTokenWithRefreshToken(config("quickbooks.$company.refreshTokenKey"));
        $accessTokenValue = $accessTokenObj->getAccessToken();
        $refreshTokenValue = $accessTokenObj->getRefreshToken();

        


        return DataService::Configure(array(

            'auth_mode' => 'oauth2',
            'ClientID' => config("quickbooks.$company.client_id"),
            'ClientSecret' =>  config("quickbooks.$company.client_secret"),
            'RedirectURI' => config("quickbooks.$company.oauth_redirect_uri") ,
            'scope' => config("quickbooks.$company.oauth_scope"),
            'baseUrl' => "development",
            'QBORealmID' => config("quickbooks.$company.QBORealmID"),
            'accessTokenKey' =>$accessTokenValue,
            'refreshTokenKey' => $refreshTokenValue,

        ));
    }


    // needed when generate a report

    public function getReportService(DataService $dataService){

        $serviceContext = $dataService->getServiceContext();

        // Prep Data Services
        return new ReportService($serviceContext);

    }

    public function hasQuickBooksErrors(){
        return session()->has(QuickBooksTrait::$ERROR_TOKEN_REFRESH);
    }

    public function isQuickBooksErrors(){
        return session()->get(QuickBooksTrait::$ERROR_TOKEN_REFRESH)===true;
    }

     /**
     * flashKeyErrorsQuickbooks
     *
     * @param  mixed $key
     * @param  mixed $validation
     *
     * @return void
     */
    public function putQuickBooKErrors($key,$value)
    {
        session()->flash($key, $value);
    }


}
