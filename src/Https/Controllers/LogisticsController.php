<?php
/**
 *------------------------------------------------------
 * LogisticsController.php
 *------------------------------------------------------
 *
 * @author    qqiu@qq.com
 * @version   V1.0
 *
 */

namespace SimpleShop\Logistics\Https\Controllers;

use Illuminate\Http\Request;
use SimpleShop\Commons\Https\Controllers\Controller;
use SimpleShop\Commons\Utils\ReturnJson;
use SimpleShop\Logistics\Logistics;

class LogisticsController extends Controller
{
    /**
     * @var Logistics
     */
    private $_service;

    /**
     * LogisticsController constructor.
     * @param Logistics $LogisticsService
     */
    public function __construct(Logistics $LogisticsService)
    {
        $this->_service = $LogisticsService;
    }

    /**
     * 列表
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->getRouteParam($request);
        $data = $this->_service->search($request->all(),
            [$this->routeParam['sort'] => $this->routeParam['order']],
            $this->routeParam['page'],
            $this->routeParam['limit']);

        foreach ($data as $item){
            if( isset($item['regexp']) && $item['regexp'] ){
                $item['regexp'] = json_decode($item['regexp'], true);
            }
        }

        return ReturnJson::paginate($data);
    }

    /**
     * 获取所有
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        $data = $this->_service->getAll();
        return ReturnJson::success($data);
    }

    /**
     * 获取详情
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->_service->detail($id);
        if( isset($data['regexp']) && $data['regexp'] ){
            $data['regexp'] = json_decode($data['regexp'], true);
        }
        return ReturnJson::success($data);
    }

    /**
     * 更新
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $request['regexp'] = json_encode($request->input('regexp', []));
        $this->_service->update($id,$request->all());
        return ReturnJson::success();
    }

    /**
     * 增添
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request['regexp'] = json_encode($request->input('regexp', []));
        $resData = $this->_service->add($request->all());
        return ReturnJson::success($resData);
    }

    /**
     * 删除
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->_service->remove($id);
        return ReturnJson::success();
    }

    /**
     * 获取物流模板当前最高价格
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function currentCost(Request $request)
    {
        $data = $this->_service->getCurrentLogisticsCost($request->input('logistics_ids', []), $request->input('floor', 0));
        return ReturnJson::success($data);
    }

}
