<?php
/**
 *------------------------------------------------------
 * Logistics.php
 *------------------------------------------------------
 *
 * @author    qqiu@qq.com
 * @version   V1.0
 *
 */

namespace  SimpleShop\Logistics;

use SimpleShop\Commons\Exceptions\DatabaseException;
use SimpleShop\Logistics\Contracts\LogisticsContract;
use SimpleShop\Logistics\Repositories\LogisticsRepository;
use SimpleShop\Logistics\Repositories\Criteria\Order;
use SimpleShop\Logistics\Repositories\Criteria\Search;

class Logistics implements LogisticsContract
{
    /**
     * @var LogisticsRepository
     */
    protected $LogisticsRepository;

    /**
     * Logistics constructor.
     * @param LogisticsRepository $LogisticsRepository
     */
    public function __construct(LogisticsRepository $LogisticsRepository)
    {
        $this->LogisticsRepository = $LogisticsRepository;
    }

    /**
     * 获取列表
     *
     * @param array $search
     * @param array $orderBy
     * @param int $page
     * @param int $pageSize
     * @return mixed
     */
    public function search(array $search = [], array $orderBy = [], $page = 1, $pageSize = 10)
    {
        return $this->LogisticsRepository
            ->pushCriteria(new Search($search))
            ->pushCriteria(new Order($orderBy))
            ->paginate($pageSize, ['*'], $page);
    }

    /**
     * 获取所有
     *
     * @param array $search
     * @param array $orderBy
     * @return mixed
     */
    public function getAll(array $search = [], array $orderBy = [])
    {
        return $this->LogisticsRepository
            ->pushCriteria(new Search($search))
            ->pushCriteria(new Order($orderBy))
            ->all();
    }

    /**
     * 获取详情
     *
     * @param $id
     * @return mixed
     */
    public function detail($id)
    {
        return $this->LogisticsRepository->find($id);
    }

    /**
     * 添加
     *
     * @param $data
     * @return mixed
     */
    public function add($data)
    {
        $resData = $this->LogisticsRepository->create($data);
        if ( !$resData ) {
            throw new DatabaseException("添加数据失败");
        }
        return $resData;
    }

    /**
     * 更新
     *
     * @param $data
     * @param $id
     * @return mixed
     */
    public function update($id,$data)
    {
        $resData = $this->LogisticsRepository->update($id,$data);
        if ( $resData === false ) {
            throw new DatabaseException("更新数据失败");
        }
        return $resData;
    }

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     */
    public function remove($id)
    {
        $resData = $this->LogisticsRepository->delete($id);
        if ( $resData === false ) {
            throw new DatabaseException("删除数据失败");
        }
        return $resData;
    }

    /**
     * 获取当前的物流费用信息
     *
     * @param $logisticsIds
     * @param int $floor
     * @return mixed
     */
    public function getCurrentLogisticsCost($logisticsIds, $floor = 0)
    {
        $logisticsIds = array_unique((array) $logisticsIds);
        $resData = $this->LogisticsRepository->find($logisticsIds);

        $logisticsInfo = $this->getLogisticsCostMaxValue($resData);
        return $this->totalLogisticsCost($logisticsInfo, $floor);
    }

    /**
     * 获取多个物流模板中，各项最大值
     *
     * @param $data
     * @return array
     */
    private function getLogisticsCostMaxValue($data)
    {
        $logisticsInfo = [];
        $base_logistics_amount_arr = $start_floor_arr = $floor_carry_amount_arr = [];
        foreach ($data as $item){
            // 商品物流配置信息
            $logisticsRegexp = json_decode($item['regexp'], true);
            // 基础物流费用
            if( isset($logisticsRegexp['base_logistics_amount']) ){
                $base_logistics_amount_arr[] = floatval($logisticsRegexp['base_logistics_amount']);
            }
            // 启用楼层费用
            if( isset($logisticsRegexp['enable_carry_way']) && $logisticsRegexp['enable_carry_way'] ){
                $start_floor_arr[] = intval($logisticsRegexp['start_floor']);
                $floor_carry_amount_arr[] = floatval($logisticsRegexp['floor_carry_amount']);
            }
        }
        rsort($base_logistics_amount_arr);
        rsort($floor_carry_amount_arr);
        sort($start_floor_arr);

        $logisticsInfo['base_logistics_amount'] = $base_logistics_amount_arr ? current($base_logistics_amount_arr) : 0;
        if( !$floor_carry_amount_arr && !$start_floor_arr ){
            $logisticsInfo['enable_carry_way'] = 0;
            $logisticsInfo['start_floor'] = 0;
            $logisticsInfo['floor_carry_amount'] = 0;
        }else{
            $logisticsInfo['enable_carry_way'] = 1;
            $logisticsInfo['start_floor'] = $start_floor_arr ? current($start_floor_arr) : 0;
            $logisticsInfo['floor_carry_amount'] = $floor_carry_amount_arr ? current($floor_carry_amount_arr) : 0;
        }
        return $logisticsInfo;
    }

    /**
     * 获取物流费用
     *
     * @param $data
     * @param int $floor
     * @return mixed
     */
    private function totalLogisticsCost($data, $floor = 0)
    {
        $data['carry_amount'] = 0;
        if( !$data['enable_carry_way'] ){
            return $data;
        }
        if($floor <= $data['start_floor']){
            return $data;
        }
        $data['carry_amount'] = bcmul(($floor - $data['start_floor']), $data['floor_carry_amount'], 2);
        return $data;
    }

}
