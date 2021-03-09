<?php


namespace Aissaio\QuickbooksCrud\Repositories;

use App\Models\Config\Setting;

use App\Repositories\Interfaces\BaseInterfaceRepositoryNonTrashed;
use Illuminate\Database\Eloquent\Model;

interface quickBookRepositoryInterface{
    /**
     * Save a new Setting instance.
     *
     * @param array data of the Setting
     * @return Setting
     */
    public function save(array $data):? Model;
    /**
     * Edit a record of Setting model.
     *
     * @param int $id
     * @param array $data
     * @param onlyPass [Optional]
     * @return Setting|null
     */
    public function edit(array $data,int $id): ? Model;
    /**
     * Delete a record of Setting model.
     *
     * @param int $id
     * @return bool
     */


     public function getCurrent() : ? Model;


}
