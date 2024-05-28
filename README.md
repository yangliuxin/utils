# utils 
### WeChat Callback Controller ###

    public function paymentNotify()
    {
        $request = $this->request->all();
        $app = new Application(config('wechat.payment'));
        try {
            $server = $app->getServer();
            $message = $server->getRequestMessage($request);
            $out_trade_no = $message['out_trade_no']; // 商户订单号
            $order = Orders::getOrderInfoByOutTradeNo($out_trade_no);
            if (empty($order)) {
                return true;
            }
            if ($message['trade_state'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                //加入自己支付完成后该做的事
                Orders::setOrderStatus($order['id'], Orders::STATUS_PAYED);
                Orders::where('id',$order['id'])->update(['pay_at'=> date("Y-m-d H:i:s")]);
                //给卖主发通知发货
                foreach ($order['goods'] as $key => $val){
                    //作品状态置为已售
                    Product::where('id',$val['good_id'])->update(['status' => Product::STATUS_SOLD, 'is_recommend' => 0, 'is_discount' => 0]);
                }

            }
            return true;
        } catch (InvalidArgumentException|\ReflectionException|\Throwable $e) {
            return false;
        }
    }

    public function paymentRefundNotify()
    {
        $request = $this->request->all();
        Log::get()->info('WECHAT_REFUND_NOTIFY_DATA_REQUEST', $request);
        $app = new Application(config('wechat.payment'));
        try {
            $server = $app->getServer();
            $message = $server->getRequestMessage($request);
            $out_refund_no = $message['out_refund_no']; // 商户订单号
            $order = Orders::getOrderInfoByOutRefundNo($out_refund_no);
            if (empty($order)) {
                return true;
            }
            if ($message['refund_status'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                //加入自己支付完成后该做的事
                Orders::setOrderStatus($order['id'], Orders::STATUS_CANCEL);
                Orders::where('id',$order['id'])->update(['refund_at' =>  date("Y-m-d H:i:s")]);
                $this->queueService->push(['type' => 'Notification',  'class' => 'OrderRefundNotification', 'from' => 0, 'to' => $order['uid'], 'data' => [$order['order_id']]]);
                //给卖主发通知发货
                foreach ($order['goods'] as $key => $val){
                    //作品状态置为已售
                    Product::where('id',$val['good_id'])->update(['status' => Product::STATUS_DEFAULT, 'is_recommend' => 1, 'is_discount' => 1]);
                }

            }
            return true;
        } catch (InvalidArgumentException|\ReflectionException|\Throwable $e) {
            Log::get()->info('PAY_REFUND_NOTIFY_DATA_EXCEPTION',[ $e->getMessage()]);
            return false;
        }
    }

### Patch ###
    ### php文件代码

    ! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__.'/../', 1));
    spl_autoload_register(function ($cls) {
    $map = [
        'Hyperf\HttpServer\CoreMiddleware' => BASE_PATH . '/patch/CoreMiddleware.php',
    ];
    
    if (isset($map[$cls])) {
        include $map[$cls];
        return true;
    }
    return true;
    }, true, true);

    ### composer.json

    "autoload": {
        "files": [
            "patch/autoload.php"
        ]
    },
