<?php


namespace Aissaio\QuickbooksCrud\Traits\ReportsData;


use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait ProfitAndLossSheetData
{


    public function getProfitAndLossSheetData($report,&$new_array,$with_currency=false){

        // dump($report);
        $rows = $report['Rows'];

        // dump($rows);


        $row = $rows['Row'];
        // dump($row);
        $columns = $report['Columns'];
        // dump($columns);


        $with_out_header = new Collection();
        $with_header = new Collection();

        foreach ($row as $r) {
            if(!$this->array_has($r,'Header'))
                $with_out_header->add($r);
            else
                $with_header->add($r);

             }

            $new_array = [];
            $this->getTotal('total_income', $with_header->first(),$new_array);
            $this->getTotal('total_expenses', $with_header->last(),$new_array);
             
            //  dd($new_array);
            //  dd($with_header->first());
            $this->getDetails('details_income', $with_header->first(),$new_array);
            $this->getDetails('details_expenses', $with_header->last(),$new_array);
            
            // dd($new_array);
            // dd($new_array);
            $this->getColumns($columns,$new_array,'total_income');
            $this->getColumns($columns,$new_array,'total_expenses');


            if($with_currency)
                $this->getCurrency($report,$new_array);

                // dd($new_array);

    }



    public function getTotal(String $total_type='total',array $data,&$new_array)  {
            $new_array[$total_type] = [];
            // dump($data['Summary']);
            $array = $data['Summary']['ColData'];
            // dump($array);

            for ($i=1;$i<=6;$i++){
                array_push($new_array[$total_type],$array[$i]);
            }

            // dd($new_array);

    }

    public function getColumns($columns,&$new_array,$type_search){

        $i = 1;
        foreach ($new_array[$type_search] as &$item){
            // $item['extra'] = $this->to_array_objects($columns->Column[$i]->MetaData);
            $item['extra'] = $columns['Column'][$i]['MetaData'];
            // dd($item);
            $i++;
        }

    }

    public function getDetails(String $total_type='total',array $data,&$new_array){
        $new_array[$total_type] = [];
        
        if(Arr::has($data,'Rows')){
            
           $rows = $data['Rows']['Row'];
                
                foreach ($rows as $row) {
                    if(!is_string($row) && Arr::has($row,'ColData')){
                    $array = $row['ColData'];
                    $account_array = [];
                    array_push($account_array,head($array));
                    array_push($account_array,last($array));
                    array_push($new_array[$total_type],$account_array);
        
                    }
                }

            }
            

    }


    public  function getCurrency($report,&$new_array){
        $currency = $report['Header']['Currency'];
        $new_array['currency'] = $currency;
    }



    private function getOnlyNotRepeated($data,&$array){


        foreach($data as $d){
            if(in_array($d[0]["value"],$array)){
                $key = array_search($d[0]["value"],$array);
                unset($array[$key]);
            }
        }


        return $array;
    }
    private function getOnlyNotExisting($data,$array){
        
        $inc = 0;

        foreach($data as $d){
            if(!in_array($d[0]["value"],$array)){
                unset($data[$inc]);
            }
           $inc++;
        }
        // reinitialise $data
        if(!empty($data)){
        $col = new Collection();
        foreach($data as $d) $col->add($d);
        $data = $col->toArray();
        }

        return $data;
    }

    private function setToDefault(&$report){
        $report["total_income"] = [];
        $report["total_expenses"] = [];
        $report["details_income"] = [];
        $report["details_expenses"] = [];
        $report["currency"] = "USD";
    }

    private function tryToNotSendThisEmpty($data,&$array){
        $data = $this->getOnlyNotExisting($data,$array);

        if(!empty($data)) $this->getOnlyNotRepeated($data,$array);
        $i = sizeof($data);

        foreach ($array as $inc){

            $data[$i]=[
                [
                    "value" => $inc,
                    "id" => $i,
                ],
                [
                    "value" => 0
                ]
            ];

            $i++;
        }
        return $data;
    }



}
