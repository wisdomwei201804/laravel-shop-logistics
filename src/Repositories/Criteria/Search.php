<?php
/**
 *------------------------------------------------------
 * Search.php
 *------------------------------------------------------
 *
 * @author    qqiu@qq.com
 * @version   V1.0
 *
 */

namespace SimpleShop\Logistics\Repositories\Criteria;

use SimpleShop\Repositories\Contracts\RepositoryInterface as Repository;
use SimpleShop\Repositories\Criteria\Criteria;

class Search extends Criteria
{
    /**
     * @var array
     */
    private $search;

    /**
     * Search constructor.
     *
     * @param array $search
     */
    public function __construct(array $search)
    {
        $this->search = $search;
    }

    /**
     * @param $model
     * @param Repository $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        if ( isset($this->search['name']) ) {
            $model = $model->where('name', 'like', "%{$this->search['name']}%");
        }

        if ( isset($this->search['base_logistics_amount']) ) {
            $model = $model->where('regexp', 'like', "%\"base_logistics_amount\":{$this->search['base_logistics_amount']}%");
        }

        if ( isset($this->search['enable_carry_way']) ) {
            $model = $model->where('regexp', 'like', "%\"enable_carry_way\":{$this->search['enable_carry_way']}%");
        }

        return $model;
    }
}