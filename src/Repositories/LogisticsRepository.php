<?php
/**
 *------------------------------------------------------
 * LogisticsRepository.php
 *------------------------------------------------------
 *
 * @author    qqiu@qq.com
 * @version   V1.0
 *
 */

namespace SimpleShop\Logistics\Repositories;

use SimpleShop\Logistics\Models\ShopLogisticsModel;
use SimpleShop\Repositories\Eloquent\Repository;

/**
 * Class LogisticsRepository
 * @package SimpleShop\Logistics\Repositories
 */
class LogisticsRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return ShopLogisticsModel::class;
    }

}
