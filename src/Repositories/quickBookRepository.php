<?php


namespace Aissaio\QuickbooksCrud\Repositories;

use App\Models\Config\Setting;
use App\QuickBookConfig;
use App\Repositories\Interfaces\Config\quickBookRepositoryInterface;
use App\Repositories\Interfaces\Config\SettingRepositoryInterface;
use App\Traits\DataStructure\ArrayManipulator;
use App\Traits\Model\ModelOperations;
use App\Traits\Repository\RepositoriesRelations;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class quickBookRepository implements quickBookRepositoryInterface {

    use RepositoriesRelations,ModelOperations,ArrayManipulator;


  /**
     * Save a new setting instance.
     *
     * @param array data of the setting
     * @return Setting
  */
    public function save(array $data):  ? Model{


        $model = new QuickBookConfig;

        $this->save_columns($data,$model);
        
        return $model->save() ? $model : null;

    }





     /**
     * Edit a record of Setting model.
     *
     * @param int $id
     */
    public function edit(array $data,int $id):  ? Model{
        
        
        $model = $this->get($id);

        $this->save_columns($data,$model);


        return $model->save() ? $model : null;

    }




    /**
     * get
     *
     * @param  mixed $id
     *
     * @return Model
     */
    public function get(int $id): ? Model{

        return QuickBookConfig::where('id',$id)->get()->first();
    }


    public function getCurrent() : ? Model {
        return QuickBookConfig::where('isCurrent',true)->get()->first();
    }





}



