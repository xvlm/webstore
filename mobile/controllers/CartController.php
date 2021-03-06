<?php

namespace mobile\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use mobile\models\CartApi;
use yii\helpers\ArrayHelper;
use mobile\models\DirectOrderApi;
use mobile\models\InventoryApi;
use mobile\models\AddressApi;
use mobile\models\PromotionApi;
use mobile\models\ProductApi;
use common\helpers\Tools;
use mobile\models\RealnameApi;
use mobile\models\OrderApi;
use mobile\models\ValidateApi;
use yii\helpers\Url;
use common\helpers\Buylimits;

/**
 * Site controller
 */
class CartController extends Controller
{

    public $layout = "maincart";
    private $searchResultPrefix = 'csrp_';

    public function behaviors()
    {
            return [
                'access' => Yii::$app->params['pageAccess']['cart'] 
            ];
    }
    
    public function beforeAction($action)
    {
    	$userId = Yii::$app->mobileUser->getId();
    	if ( $action->id === 'ajaxcreate' )
    	{
    		$params = Yii::$app->request->post();
    		
	    	if ( $userId && isset( $params['itemPartNumber'] ) )
	    	{
	    		$buylimitsClass = new Buylimits();
	    		$result = $buylimitsClass->isBuyItem($userId,$params['itemPartNumber']);
	    		
	    		if ( $result['no_buy'] )
	    		{
	    			Tools::error('99031', $result['depict']);
	    		}
	    	}
    	}
    	elseif ( $action->id === 'can-checkout' )
    	{
    		$cartModel = new CartApi($userId);
    		$cart = $cartModel->getCartList();
    	
    		$itemId = array();
    		foreach ( $cart['cart'] as $key => $value )
    		{
    			foreach ( $value as $k => $v )
    			{
    				$itemId[$v['itemId']] = $v['itemPartNumber'];
    			}
    		}
    	
    		$buylimitsClass = new Buylimits();
    		foreach ( $itemId as $key => $id )
    		{
    			$result = $buylimitsClass->isBuyItem($userId,$id);
    			if ( $result['no_buy'] )
    			{
    				echo $result['depict'];
    				die;
    			}
    		}
    	}
    	elseif ( $action->id === 'checkout' )
    	{
    		$params = Yii::$app->request->post();
    		$pid = isset($params['itemId'])?$params['itemId']:0;
    		if ( $pid )
    		{
	    		$productModel = new ProductApi($userId);
	    		$product = $productModel->getProductById($pid);
	    		$itemId = $product['product']['partNumber'];
	    		$buylimitsClass = new Buylimits();
	    		$result = $buylimitsClass->isBuyItem($userId,$itemId);
	    		if ( $result['no_buy'] )
	    		{
	    			$errorMsg = str_replace("baokuanshangpin", "", $result['depict']);
	    			Tools::error('99031', $errorMsg);
	    		}
    		}
    	}
    	 
    	return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        //现阶段用户模块并没有完成，但是将来用下面的方法获取用户id
        $userId = Yii::$app->mobileUser->getId();
        $cartModel = new CartApi($userId);
        $productModel = new ProductApi($userId);
        $data = $cartModel->getCartList();
        if (is_array($data) && isset($data['cart']) && count($data['cart']) > 0) {
            $data = $data['cart'];
            $cartNum = array();
            $allIds = array(); 
            $totalCartNum = 0;
            foreach ($data as $key => $value) {
                $cartNum[$key] = count(ArrayHelper::getColumn($value, 'cartlineId'));// 统计每个单中商品种类数 删除时用          
                $allIds = array_merge($allIds,ArrayHelper::getColumn($value, 'itemId'));//items 供搜索使用
                $totalCartNum = $totalCartNum + array_sum(ArrayHelper::getColumn($value, 'cartlineQuantity'));
            }
            $finalData = $cartModel->getExtraInfoFromSearch($productModel,$data, $allIds, $userId);//添加是否下架字段,税率，限购数量

        } else {
            return $this->render('cartempty');
        }
        //setcookie ($name, $value = null, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false)
        setcookie('cartNum', serialize($cartNum), 0, '/', null, false, true);
        setcookie('totalNum', array_sum($cartNum), 0, '/', null, false, true);

        $_csrf = Yii::$app->request->getCsrfToken();
        return $this->render('cart', [
                    'allItems' => $finalData,
                    'totalCartNum' => $totalCartNum,
                    'storeName' => Yii::$app->params['sm']['cart']['store'],
                    '_csrf' => $_csrf
        ]);
    }
    
    public function actionApp()
    {
    	//现阶段用户模块并没有完成，但是将来用下面的方法获取用户id
    	$userId = Yii::$app->mobileUser->getId();
    	$cartModel = new CartApi($userId);
    	$productModel = new ProductApi($userId);
    	$data = $cartModel->getCartList();
    	if (is_array($data) && isset($data['cart']) && count($data['cart']) > 0) {
    		$data = $data['cart'];
    		$cartNum = array();
    		$allIds = array();
    		$totalCartNum = 0;
    		foreach ($data as $key => $value) {
    			$cartNum[$key] = count(ArrayHelper::getColumn($value, 'cartlineId'));// 统计每个单中商品种类数 删除时用
    			$allIds = array_merge($allIds,ArrayHelper::getColumn($value, 'itemId'));//items 供搜索使用
    			$totalCartNum = $totalCartNum + array_sum(ArrayHelper::getColumn($value, 'cartlineQuantity'));
    		}
    		$finalData = $cartModel->getExtraInfoFromSearch($productModel,$data, $allIds, $userId);//添加是否下架字段,税率，限购数量
    
    	} else {
    		return $this->render('cartempty_app');
    	}
    	//setcookie ($name, $value = null, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false)
    	setcookie('cartNum', serialize($cartNum), 0, '/', null, false, true);
    	setcookie('totalNum', array_sum($cartNum), 0, '/', null, false, true);
    
    	$mobileSystem = '';
    	$agent = $_SERVER['HTTP_USER_AGENT'];
	    if(preg_match('/iphone\s*os/i',$agent))
	    {
	    	$mobileSystem = 'ios';
	    }
    	
    	$_csrf = Yii::$app->request->getCsrfToken();
    	return $this->render('cart_app', [
    			'allItems' => $finalData,
    			'totalCartNum' => $totalCartNum,
    			'storeName' => Yii::$app->params['sm']['cart']['store'],
    			'_csrf' => $_csrf,
    			'mobileSystem' => $mobileSystem
    			]);
    }

    
    #TODO    hezll 谁的购物车

    public function actionAjaxcreate() {

        $userId = Yii::$app->mobileUser->getId();      
        if ($userId == "") {
            return [$cart->getErrors()];
        }        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $cart = new \mobile\models\CartApi($userId);
        $params = Yii::$app->request->post();
        $data = ArrayHelper::remove($params, '_csrf');
        $ProductModel =  new \mobile\models\ProductApi($userId);
        $product = $ProductModel->getProductById($params['itemId']);
        $key = key($product);
        $product = isset($product[$key]) ? $product[$key] : [];     
        // TODO 这个字段search 暂时拿不到， 但是加入购物车又必须，所以现在hardcode成0  
        $params['itemVolumn']= 0;                               //商品体积 required
        
        $params['itemWeightUOM'] = "克";
        //$params['itemWeight']= 98765;                              //商品重量 required
        foreach($product['descriptiveAttributes'] as $k => $arr)
        {
            if($arr['id']=='Sales-WeightUOM'){
                $params['itemWeightUOM'] = $arr['value'];
            }
            if($arr['id']=='Sales-Weight'){
                $params['itemWeight'] = $arr['value'];
            }
             if($arr['id']=='Sales-Volumn'){
                $params['itemVolumn'] = $arr['value'];
            }
        }      
        
        if(isset($params['itemWeight']) === FALSE ){  
            if($product['type'] === 'package'){
                $params['itemWeight'] = 999999;
            }else{
                Yii::error("itemId=" .$params['itemId']." 该商品未设置重量");
                return ['errorCode'=>'c003' ,'errorMsg'=>'该商品未设置重量，暂不支持购买' ];
            }
        }
        
        $params['itemPriceListId'] = "offer";                  //购物车项商品的采用的价格列表 required
        $params['buyable'] = $product['buyable'];           
        $params['minBuyCount'] = isset($product['minBuyCount'])?$product['minBuyCount']:'1';
        $params['maxBuyCount'] = isset($product['maxBuyCount'])?$product['maxBuyCount']:'50';
        $params['itemType'] = $product['type'];       
        $params['cartlineType'] = 1;                           //买家类型，游客0，登录用户1 , required
        $params['itemDisplayText'] = $product['desc']['name'];
        $params['shopDisplayText'] = "自贸区直购专场";             //购物车项商品所属店铺显示的文本,自营情况下可以为空，或者默认 required   
        $params['shopContactId'] = "1111";                      //...听完了也没懂，跟店小二有关？
        $params['channelType'] = Yii::$app->params['platform']; //购物车来源类型,app,pc,wechat等,required
        //$params['channelId'] = "ftzmall";                       //购物车来源Id , required //modift by dcj channelid 已经在post body里了
        //$params['itemImageLink'] = "{\"img\":\"".$product['desc']['thumbnail']."\",\"img_large\":\"".$product['desc']['fullImage']."\"}";
        
        $params['itemImageLink'] = $product['desc']['fullImage'];
        
        $params['itemSalestype'] = isset($product['salesType'])?$product['salesType']:$params['salestype'];       //商品销售来源,一般贸易（DIG，供应商直送），跨境贸易（自营，分销），海外直邮 required
        $params['shopId'] = $product['memberId'];               //购物车项商品所属店铺的Id,自营情况下可以为空，或者默认,required
        $params['shopLink'] = "http://www.ftzmall.com";      //required
        $params['itemOwnerId'] = "ftzmall";                     //购物车项商品的货主Id required
        $params['itemListPrice'] = isset($product['listPriceInfo'][0]['price'])?$product['listPriceInfo'][0]['price']:0;    //购物车项商品的标价，显示用 required
        $params['itemMfname'] = $product['manufactureName'];    //供应商 required   manufactureName
        //$params['itemWeight']= 12;                              //商品重量 required
        //$params['itemVolumn']= 0;                               //商品体积 required
        $params['cartlineComment']= "";                          //买家留言
        $params['itemOfferPrice'] = isset($product['offerPriceInfo'][0]['price'])?$product['offerPriceInfo'][0]['price']:0; //购物车项商品的售价，显示用(没懂，自己算？)
        $params['tariffno'] = isset($product['tax']['taxSerialNumber']) ? $product['tax']['taxSerialNumber'] : '0';        //税则号，一定要，tax里的
        $params['isGift'] = 0;
        $item['CartApi'] = $params;
        $quantity = $cart->getQuantityInCart($params['itemId']);
        
        $validator = new ValidateApi(); 
        $validateCartData['maxBuyCount'] =$params['maxBuyCount'];
        $validateCartData['minBuyCount'] =$params['minBuyCount'];
        $validateCartData['buyable'] =$params['buyable'];
        $validateCartData['itemInv'] =intval($params['inventory']); //等和曹琦确认以后再加上
        $validateCartData['quantity'] =$params['cartlineQuantity'] + $quantity;
        $cartInfo['ValidateApi'] = $validateCartData;
        $validator->scenario = 'itemsVal';
        if($validator->load($cartInfo) && (!$validator->validate())){
            $errors = $validator->getErrors();
            if(isset($errors['quantity'])){
                return ['errorCode'=>'c001' ,'errorMsg'=>'您购物车中已有'. $quantity . '件, '. $errors['quantity'][0] ];
            }
            if(isset($errors['itemInv'])){
                return ['errorCode'=>'c002' ,'errorMsg'=>'您购物车中已有'. $quantity . '件, '. $errors['itemInv'][0] ];
            }
        }
        
        if ($cart->load($item) && $cart->validate()) { 
            $data = $cart->addToCart($item['CartApi']);
            return $data;
        } else {
            return [$cart->getErrors()];
        }
    }

    public function actionCheckout()
    {
        $this->layout = "mainnew";
        $userId = Yii::$app->mobileUser->getId();
        $postData = Yii::$app->request->post() ? Yii::$app->request->post() : Yii::$app->request->get();
        $cartModel = new CartApi($userId);
        $productModel = new ProductApi($userId);
        $isCBT = false;
        $isRealname = false;
        
        if ($userId && !empty($postData) && (isset($postData['productsel']) || isset($postData['itemId']))) {
            //获取用户所有地址
            $model = new AddressApi($userId);
            $address = $model->getList($userId)['address'];
            ArrayHelper::multisort($address, 'isDefault', SORT_DESC);
            //地址信息超过40个字符后，多余的隐藏，如果有默认地址，顺便取出来
            foreach($address as $key => $val){
                if($val['isDefault'] == 1){
                    $defaultAddress = $val;
                }
                $address[$key]['address'] = Tools::substr_mb($val['address']);
            }
                        
            if (isset($postData['productsel'])) {
                //获取结算商品信息
                $ids = $postData['productsel'];
                $CartLines = $cartModel->getCartLinebyIds($ids);
                if(empty($CartLines) === TRUE){
                    Tools::error('99002','购物车信息无效，请检查购物车');
                }
                $itemIdsInfo = array();
                $returnUrl = Url::to(['cart/checkout','productsel' => $ids],true);
            }
            if (isset($postData['itemId'])) {
                $ids = [];
                $DirectOrderApi = new DirectOrderApi($userId);
                $itemIdsInfo = [$postData['itemId'] => $postData['cartlineQuantity']];
                $channelId = isset($postData['channelId']) ? $postData['channelId'] : 'ftzmall';
                $DTOinfo = $DirectOrderApi->getDTOInfo($itemIdsInfo, $channelId)['DTOinfo'];
                $CartLines[$DTOinfo[0]['itemSalestype']] = $DTOinfo;
                $returnUrl = Url::to(['cart/checkout', 'itemId' => $postData['itemId'], 'cartlineQuantity' => $postData['cartlineQuantity']],true);
            }

            $realnameModel = new RealnameApi($userId);
            $realnameInfo = $realnameModel->isNeedRealname($CartLines);
            $isCBT = $realnameInfo['isCBT'];
            $isRealname = $realnameInfo['isRealname'];
            if($isCBT && !$isRealname){
                $realnameInfo = $realnameModel->getById();
            }
            
            $params = [
                'cartlineList' => Tools::getColumn($CartLines),
                "couponIds" => null,
                "price" => true,
                "promotion" => true,
                "shipping" => false,
                "tax" => true,
            ];
            
            if($isCBT && isset($defaultAddress)){
                $params['address'] = [
                    'country_code' => 'CN',
                    'district_code' => $defaultAddress['districtCode'],
                    'postcode' => $defaultAddress['postcode'],
                    'city_code' => $defaultAddress['cityCode'],
                    'state_code' => $defaultAddress['stateCode'],
                ];
                $params['shipping'] = true;
            }
            $price = $cartModel->priceResultPreprocess($params);
            $keys = array_keys($price['orderDetail']);
            $price['orderDetail'] = $price['orderDetail'][$keys[0]];
            $orderType = $keys[0];
            $flag = true;
            foreach ($price['itemDetail'] as $val){
                //itemFreeShipment 有值代表单品包邮，如果单品包邮数量与订单商品总数相同，则认为订单包邮
                if(!isset($val['itemFreeShipment'])){
                    $flag = false;
                    break;
                }
            }
            if($flag){
                $price['orderDetail']['orderFreeShipment'] = '商品享受包邮活动';
            }
            
            //将购物车的二维数组变成一维方便处理
            $keys = array_keys($CartLines);
            $CartLines = $CartLines[$keys[0]];
            
            //先拿cache，如果拿不到就报id放到一个list里，在foreach外，统一再拿一次。
            $cache = Yii::$app->cache;
            foreach($CartLines as $key => $val){
                $cacheKey = $this->searchResultPrefix.$val['itemId'];
                $cacheInfo = $cache->get($cacheKey);

                if($cacheInfo !== FALSE){
                    $CartLines[$key] = ArrayHelper::merge($CartLines[$key], $cacheInfo);
                }else{
                    $itemIds[] = $val['itemId'];
                }
            }
            if(!empty($itemIds)){
                $itemCache = $productModel->filterInfoFromByids($itemIds);
                $CartLines = ArrayHelper::index($CartLines, 'itemId');
                foreach($CartLines as $key => $val){
                    $CartLines[$key] = ArrayHelper::merge($CartLines[$key], $itemCache[$key]);
                }
            }
            
//            print_r($price);exit;
            return $this->render('checkout', [
                        'model' => $model,
                        'addressData' => $address,
                        'CartLines' => $CartLines,
                        'orderModel' => new OrderApi($userId),
                        'CartLinesIds' => implode(',', $ids),
                        'price' => $price,
                        '_csrf' => Yii::$app->request->getCsrfToken(),
                        'itemIdsInfo' => empty($itemIdsInfo) ? '' : json_encode($itemIdsInfo),
                        'isRealname' => $isRealname,
                        'isCBT' => $isCBT,
                        'orderType' => $orderType,
                        'realnameModel' => $realnameModel,
                        'realnameInfo' => isset($realnameInfo['realname']) ? $realnameInfo['realname'] : '',
                        'returnUrl' => $returnUrl,
                        'channelId' => isset(reset($CartLines)['channelId']) ? reset($CartLines)['channelId'] : 'ftzmall',
            ]);
        } else {
            $this->redirect(Url::to(['cart/index']));
        }
    }
    
    public function actionCheckoutapp()
    {
    	$this->layout = "mainnew";
    	$userId = Yii::$app->mobileUser->getId();
    	$postData = Yii::$app->request->post() ? Yii::$app->request->post() : Yii::$app->request->get();
    	$cartModel = new CartApi($userId);
    	$productModel = new ProductApi($userId);
    	$isCBT = false;
    	$isRealname = false;
    
    	if ($userId && !empty($postData) && (isset($postData['productsel']) || isset($postData['itemId']))) {
    		//获取用户所有地址
    		$model = new AddressApi($userId);
    		$address = $model->getList($userId)['address'];
    		ArrayHelper::multisort($address, 'isDefault', SORT_DESC);
    		//地址信息超过40个字符后，多余的隐藏，如果有默认地址，顺便取出来
    		foreach($address as $key => $val){
    			if($val['isDefault'] == 1){
    				$defaultAddress = $val;
    			}
    			$address[$key]['address'] = Tools::substr_mb($val['address']);
    		}
    
    		if (isset($postData['productsel'])) {
    			//获取结算商品信息
    			$ids = $postData['productsel'];
    			$CartLines = $cartModel->getCartLinebyIds($ids);
    			if(empty($CartLines) === TRUE){
    				Tools::error('99002','购物车信息无效，请检查购物车');
    			}
    			$itemIdsInfo = array();
    			$returnUrl = Url::to(['cart/checkoutapp','productsel' => $ids],true);
    		}
    		if (isset($postData['itemId'])) {
    			$ids = [];
    			$DirectOrderApi = new DirectOrderApi($userId);
    			$itemIdsInfo = [$postData['itemId'] => $postData['cartlineQuantity']];
    			$channelId = isset($postData['channelId']) ? $postData['channelId'] : 'ftzmall';
    			$DTOinfo = $DirectOrderApi->getDTOInfo($itemIdsInfo, $channelId)['DTOinfo'];
    			$CartLines[$DTOinfo[0]['itemSalestype']] = $DTOinfo;
    			$returnUrl = Url::to(['cart/checkoutapp', 'itemId' => $postData['itemId'], 'cartlineQuantity' => $postData['cartlineQuantity']],true);
    		}
    
    		$realnameModel = new RealnameApi($userId);
    		$realnameInfo = $realnameModel->isNeedRealname($CartLines);
    		$isCBT = $realnameInfo['isCBT'];
    		$isRealname = $realnameInfo['isRealname'];
    		if($isCBT && !$isRealname){
    			$realnameInfo = $realnameModel->getById();
    		}
    
    		$params = [
    		'cartlineList' => Tools::getColumn($CartLines),
    		"couponIds" => null,
    		"price" => true,
    		"promotion" => true,
    		"shipping" => false,
    		"tax" => true,
    		];
    
    		if($isCBT && isset($defaultAddress)){
    			$params['address'] = [
    			'country_code' => 'CN',
    			'district_code' => $defaultAddress['districtCode'],
    			'postcode' => $defaultAddress['postcode'],
    			'city_code' => $defaultAddress['cityCode'],
    			'state_code' => $defaultAddress['stateCode'],
    			];
    			$params['shipping'] = true;
    		}
    		$price = $cartModel->priceResultPreprocess($params);
    		$keys = array_keys($price['orderDetail']);
    		$price['orderDetail'] = $price['orderDetail'][$keys[0]];
    		$orderType = $keys[0];
    		$flag = true;
    		foreach ($price['itemDetail'] as $val){
    			//itemFreeShipment 有值代表单品包邮，如果单品包邮数量与订单商品总数相同，则认为订单包邮
    			if(!isset($val['itemFreeShipment'])){
    				$flag = false;
    				break;
    			}
    		}
    		if($flag){
    			$price['orderDetail']['orderFreeShipment'] = '商品享受包邮活动';
    		}
    
    		//将购物车的二维数组变成一维方便处理
    		$keys = array_keys($CartLines);
    		$CartLines = $CartLines[$keys[0]];
    
    		//先拿cache，如果拿不到就报id放到一个list里，在foreach外，统一再拿一次。
    		$cache = Yii::$app->cache;
    		foreach($CartLines as $key => $val){
    			$cacheKey = $this->searchResultPrefix.$val['itemId'];
    			$cacheInfo = $cache->get($cacheKey);
    			if($cacheInfo !== FALSE){
    				$CartLines[$key] = ArrayHelper::merge($CartLines[$key], $cacheInfo);
    			}else{
    				$itemIds[] = $val['itemId'];
    			}
    		}
    		if(!empty($itemIds)){
    			$itemCache = $productModel->filterInfoFromByids($itemIds);
    			$CartLines = ArrayHelper::index($CartLines, 'itemId');
    			foreach($CartLines as $key => $val){
    				$CartLines[$key] = ArrayHelper::merge($CartLines[$key], $itemCache[$key]);
    			}
    		}
    
    		//            print_r($price);exit;
    		return $this->render('checkout_app', [
    				'model' => $model,
    				'addressData' => $address,
    				'CartLines' => $CartLines,
    				'orderModel' => new OrderApi($userId),
    				'CartLinesIds' => implode(',', $ids),
    				'price' => $price,
    				'_csrf' => Yii::$app->request->getCsrfToken(),
    				'itemIdsInfo' => empty($itemIdsInfo) ? '' : json_encode($itemIdsInfo),
    				'isRealname' => $isRealname,
    				'isCBT' => $isCBT,
    				'orderType' => $orderType,
    				'realnameModel' => $realnameModel,
    				'realnameInfo' => isset($realnameInfo['realname']) ? $realnameInfo['realname'] : '',
    				'returnUrl' => $returnUrl,
    				'channelId' => isset($CartLines[0]['channelId']) ? $CartLines[0]['channelId'] : 'ftzmall',
    				]);
    	} else {
    		$this->redirect(Url::to(['cart/app']));
    	}
    }

   public function actionDelete() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $userId = Yii::$app->mobileUser->getId();
        $deleteData = Yii::$app->request->post();
        if (!$deleteData || !is_array($deleteData) || !isset($deleteData['cartlineId'])) {
            $errorInfo = '购物车删除传入数据异常';
            Yii::error($errorInfo);
            throw new BadRequestHttpException("非法请求");
        }
        $model = new CartApi($userId);
        return $model->deleteCartLine($deleteData);
    }

    public function actionUpdate() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $userId = Yii::$app->mobileUser->getId();
        $updateData = Yii::$app->request->post();
        if (!$updateData || !is_array($updateData) || !isset($updateData['quantity'])) {
            $errorInfo = '购物车更新，传入数据异常';
            Yii::error($errorInfo);
            throw new BadRequestHttpException("非法请求");
        }
        $cartModel = new CartApi($userId);
        $InventoryModel = new InventoryApi($userId);
        $productModel = new ProductApi($userId);
        return $cartModel->updateCartLineQuantity($InventoryModel,$productModel,$updateData);
    }


   public function actionPrice() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $userId = Yii::$app->mobileUser->getId();
       
        $postData = Yii::$app->request->post();
        if (!$postData || !is_array($postData) ) {
            throw new BadRequestHttpException("非法请求");
        }
        $model = new CartApi($userId);
        $dtoModel = new DirectOrderApi($userId);
        $result = $model->calculatePriceCore($postData,$dtoModel);
  
        return $result; 
    }

    public function actionGetcarttotalnum() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $userId = Yii::$app->mobileUser->getId();
        if ($userId == "") {
            return 0;
        }
        $model = new CartApi($userId);
        $data = $model->getCartList($userId);
        if ($data && is_array($data) && isset($data['cart'])) {
            $data = $data['cart'];
            $totalNum = 0;
            foreach ($data as $key => $value) {
                foreach ($value as $v) {
                    $totalNum = $totalNum + $v['cartlineQuantity'];
                }
            }
            setcookie('ccn_' . $userId, $totalNum, 0, '/');
            return $totalNum;
        } else {
            throw new BadRequestHttpException("请求出错，请稍后重试");
        }
    }
    
    public function actionCanCheckout() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $userId = Yii::$app->mobileUser->getId();
        if ($userId == "")  return false;
        $checkoutData = Yii::$app->request->post();
        $model = new CartApi($userId);
        $InventoryModel = new InventoryApi($userId);
        $productModel = new ProductApi($userId);
        $Ids = explode(',', $checkoutData['cartlineIds']);
        return $model->canCheckOut($InventoryModel,$productModel,$Ids, $userId);
    }
    
    public function actionGetShippingRule() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $userId = Yii::$app->mobileUser->getId();
        if ($userId == "")  return false;
        $getData = Yii::$app->request->get();
        $model = new \frontend\models\PromotionApi($userId);
        $rule = $model->findRuleByAttr($getData['attrname'], $getData['value']);
        if(isset($rule['promotion']) && !empty($rule['promotion'])){
            foreach ($rule['promotion'] as $value) {
                $result['name'] = $value['desc'];
            }
            return $result;
        }
        return ['errorCode' => 'c500'];
    }
    
    public function actionListValidCoupons() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $userId = Yii::$app->mobileUser->getId();
        if ($userId == "")  return false;
        $postData = Yii::$app->request->post();
        if ($postData && is_array($postData) &&(isset($postData['dtoItemsInfo'])||isset($postData['cartlineIds']))) {
            $cartModel = new CartApi($userId);
            $dtoModel = new DirectOrderApi($userId);
            $promotionMode = new PromotionApi($userId);
            return $promotionMode -> listValidCoupons($postData, $cartModel,$dtoModel);
        }else{      
            throw new BadRequestHttpException("非法请求, listcoupons未提供正确参数");
        }
    }

    public function actionActiveCoupon() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $userId = Yii::$app->mobileUser->getId();
        //$userId = '5032';
        if ($userId == "")  return false;
        $postData = Yii::$app->request->post();
        if ($postData && is_array($postData) && isset($postData['couponCode'])) {
            $cartModel = new CartApi($userId);
            $dtoModel = new DirectOrderApi($userId);
            $promotionMode = new PromotionApi($userId);
            return $promotionMode ->activeCoupons($postData, $cartModel, $dtoModel);
        }else{      
            throw new BadRequestHttpException("非法请求");
        }
        
    }
    
    
    
}
