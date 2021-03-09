<?php


namespace Aissaio\QuickbooksCrud\Traits\Controller;


use App\Traits\QuickBooks\Reports\QuickBookReport;
use App\Traits\QuickBooks\ReportsData\QuickBooksReportData;

class QuickBooksOperation
{
    use QuickBookReport,QuickBooksReportData;


    public function getProfitAndLossReport(){

        $report = $this->ProfitAndLossSheet();
        $data = [];
        $this->getProfitAndLossSheetData($report,$data,true);
        return $data;
    }





}
