<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\icons\Icon;
//use yii\widgets\Breadcrumbs;
use frontend\assets\MainAsset;

//use frontend\widgets\Alert;
/* @var $this \yii\web\View */
/* @var $content string */
MainAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        
        <?php $this->head() ?>
    </head>
    <body data-lazyload-config="{
          onAfter:function(img){
          try{if(img.get('data-img-zoom') == 'true') fixImageSize(img);}catch(e){}
          }
          }" style="zoom: 1;">
              <?php $this->beginBody() ?>
        <script>
            jQuery.noConflict();
        </script>
        <div class="toolbar clb">
            <div class="w1200 clearfix">
                <?= \frontend\widgets\LoginWidget::widget(); ?>
                <?= $this->render('_tool_link_order') ?> 
            </div>
        </div>
        <!--<div class="head_ad_banner"><div class="w1200 clearfix"><a href="<?= Url::to(['activity/']); ?>" target="_blank"><img src="<?= Yii::$app->params['baseimgUrl'] ?>339d220ede38e72704fdb6b7e8838efaf1b.jpg" class="lazyload" alt="">
          <!-- <img src='http://ecomgq-images.oss.aliyuncs.com/32/a5/9d/339d220ede38e72704fdb6b7e8838efaf1b.jpg?1431917527#w' alt=""  /> 
                </a></div></div>-->
        <div class="header clb">

            <div class="w1200 clearfix">
                <div class="logo fl"><a href="<?= Url::to(['act/page']); ?>"><img alt="FTZMALL" src="<?= Yii::$app->params['baseimgUrl'] ?>c50e5fbf3bef1b333a7cc1b6abd4013b92c.png" border="0"></a></div>

                <ul class="dzul">
                    <!-- <li class="li4"><img src="<?= Yii::$app->params['baseimgUrl'] ?>index_m4_03.jpg" width="90" height="33" alt="全场免税"></li>
                    <li class="li4"><img src="<?= Yii::$app->params['baseimgUrl'] ?>index_m4_05.jpg" width="95" height="33" alt="原装进口"></li>
                    <li class="li4"><img src="<?= Yii::$app->params['baseimgUrl'] ?>index_m4_07.jpg" width="94" height="33" alt="海关监管"></li>
                    <li class="li4"><img src="<?= Yii::$app->params['baseimgUrl'] ?>index_m4_09.jpg" width="94" height="33" alt="快捷到货"></li> -->
                    <!-- <li class="li5">
                        <div class="shopdiv"><div class="spic"><img src="<?= Yii::$app->params['baseimgUrl'] ?>index_m4_13.png" width="20" height="20"></div>
                            <div class="stext">购物车</div>
                            <div class="sshow">
                                <a href="<?= Url::to(['cart/']); ?>" class="cart-wrap">
                                    <span class="icon"></span>（
                                    <script>

                                        var cookietotalnum = 0;
                                        var _prefix_number = "";
                                        var _subfix_number = "</span>";
                                        _prefix_number = '<span id="cart_num" class="red">';

                                        if ('<?= Yii::$app->user->getId() ?>' != '')
                                        {
                                            cookietotalnum = jQuery.cookie('carttotalNum' + '<?= Yii::$app->user->getId() ?>');
                                            if (!cookietotalnum)
                                            {

                                                jQuery.ajax({
                                                    type: 'get',
                                                    url: '<?= Url::to(['cart/getcarttotalnum']) ?>',
                                                    success: function (carttotalnum) {
                                                        cookietotalnum = carttotalnum;
                                                        jQuery("#cart_num").html(carttotalnum);
                                                    }
                                                })
                                            }
                                        }
                                        document.write(typeof (cookietotalnum) == 'undefined' ? (_prefix_number + '0' + _subfix_number) : _prefix_number + (cookietotalnum ? cookietotalnum : '0') + _subfix_number);


                                    </script>                                    
                                    ）    
                                </a>&gt;</div>
                        </div>
                    </li> -->
                </ul>
               <?= $this->render('_search_box') ?> 
            </div>

            <?= $this->render('_right') ?> 
            <?= $this->render('_nav') ?> 
            <?= $content ?>   
            <script>
                (function () {
                    $$('.sidebar-backtop').addEvent('click', function () {
                        $(window).scrollTo(0, 0)
                    });
                    $$('.sidebar-close').addEvent('click', function () {
                        $$('.sidebar-right').setStyle('display', 'none')
                    })
                })();
            </script>


            <?= $this->render('_map') ?> 
            <?= $this->render('_footer') ?> 

            <script>
                (function () {
                    var setPageSize = function () {
                        if (window.getWidth() > 1200) {
                            $$('.w1000').addClass('w1200').removeClass('w1000');
                            $$('.goods-wrap.w800').setStyle('width', 797);
                            $$('.goods-warp-2.w800').setStyle('width', 800);
                            $$('.f_help dl').removeClass('w100');
                            $$('.goods-rightbox').setStyle('width', 770);
                            // $$('.search_wrap').setStyle('width',540);
                            $$('.search_keyword').setStyle('width', 210);
                            $$('.ppsc-index-floor-01 .banner1').setStyle('margin-left', 232);
                            $$('.allkind_title').setStyle('width', 170);
                            $$('.allkind_menu_index').setStyle('width', 166);
                            $$('.menu_box .i_mc').setStyle('left', 168);
                            $$('.brand-list').setStyle('width', 700);
                            $$('.brand-list ul li').setStyle('width', 139);
                            $$('.kjt-brand').setStyle('width', 1194);
                            $$('.kjt-brand .gallery_ri ul li').setStyle('width', 198);
                            $$('.t1').setStyle('width', 400);
                            $$('.t2').setStyle('width', 600);
                            $$('.kjt-main-wrap').setStyle('width', 1200);
                            $$('.layout-t').setStyle('width', 797);
                            $$('.layout-b').setStyle('width', 797);
                            $$('.special').setStyle('width', 1200);
                            $$('.foot').removeClass('foot2');
                            $$('.azg-banner-1 li img').setStyle('width', 1200);
                            $$('.azg-goods-main .goods-list li').setStyle('width', 227);
                            $$('.gjzs-banner-1 img').setStyle('width', 1200);
                            $$('.w601').removeClass('change400');
                            $$('.activity-title').setStyle('width', 1200);
                            $$('.span-16 ').setStyle('width', 630);
                            $$('.member-sidebar').setStyle('width', 210);
                            $$('.sell_ri').setStyle('width', 960);
                            $$('.member_ri_top_ri').setStyle('width', 695);
                            $$('.sell_ri_top_ri').setStyle('width', 800);
                            $$('.memberindex_money').setStyle('width', 248);
                            $$('.sell_ri').setStyle('width', 960);
                            $$('.member-main').setStyle('width', 960);
                            $$('.bai1200').setStyle('width', 1200);
                            $$('.pro_list_cart_xu').setStyle('width', 1200);
                            $$('.orderpay').setStyle('width', 1200);
                            $$('.J_order_total').setStyle('width', 500);
                            $$('.width_500').setStyle('width', 590);
                            $$('.return_settlement').setStyle('width', 1200);
                            $$('.return_settlement').setStyle('width', 1200);
                            $$('.ad-wrap-main').setStyle('width', 1200);
                        } else {
                            $$('body').setStyle('min-width', 1000);
                            $$('.w1200').addClass('w1000').removeClass('w1200');
                            $$('.goods-wrap.w800').setStyle('width', 600);
                            $$('.goods-warp-2.w800').setStyle('width', 600);
                            $$('.f_help dl').addClass('w100');
                            $$('.goods-rightbox').setStyle('width', 570);
                            $$('.search_wrap').setStyle('width', 340);
                            $$('.search_keyword').setStyle('width', 166);
                            $$('.ppsc-index-floor-01 .banner1').setStyle('margin-left', 32);
                            //$$('.allkind_title').setStyle('width',140);
                            //$$('.allkind_menu_index').setStyle('width',151);
                            //$$('.menu_box .i_mc').setStyle('left',151);
                            $$('.brand-list').setStyle('width', 500);
                            $$('.brand-list ul li').setStyle('width', 124);
                            $$('.kjt-brand').setStyle('width', 1000);
                            $$('.kjt-brand .gallery_ri ul li').setStyle('width', 165);
                            $$('.t1').setStyle('width', 333);
                            $$('.t2').setStyle('width', 500);
                            $$('.kjt-main-wrap').setStyle('width', 1000);
                            $$('.kjt-main-wrap').setStyle('overflow', 'hidden');
                            $$('.layout-t').setStyle('width', 597);
                            $$('.layout-b').setStyle('width', 597);
                            $$('.special').setStyle('width', 1000);
                            $$('.foot').addClass('foot2');
                            $$('.azg-banner-1 li img').setStyle('width', 1000);
                            $$('.azg-goods-main .goods-list li').setStyle('width', 239);
                            $$('.gjzs-banner-1 img').setStyle('width', 1000);
                            $$('.w601').addClass('change400');
                            $$('.activity-title').setStyle('width', 1000);
                            $$('.common-mod').setStyle('overflow', 'hidden');
                            $$('.span-16 ').setStyle('width', 500);
                            $$('.member-sidebar').setStyle('width', 154);
                            $$('.sell_ri').setStyle('width', 800);
                            $$('.sell_ri_top_ri').setStyle('width', 540);
                            $$('.memberindex_money').setStyle('width', 100);
                            $$('.sell_ri').setStyle('width', 800);
                            $$('.member-main').setStyle('width', 800);
                            $$('.bai1200').setStyle('width', 1000);
                            $$('.pro_list_cart_xu').setStyle('width', 1000);
                            $$('.orderpay').setStyle('width', 1000);
                            $$('.J_order_total').setStyle('width', 500);
                            $$('.width_500').setStyle('width', 300);
                            $$('.return_settlement').setStyle('width', 1000);
                            $$('.return_settlement').setStyle('width', 1000);
                        }
                    }
                    window.addEvents({
                        'domready': function (e) {
                            setPageSize();
                        },
                        'resize': function (e) {
                            setPageSize();
                        }
                    });


                })();
            </script>

            <!-- FOR DEVELOP
            <script src="http://cdn.bootcss.com/holder/2.0/holder.min.js"></script> -->

        </div>
        <?php $this->endBody() ?>
        <script>jQuery.noConflict();</script> 
    </body></html>
<?php $this->endPage() ?>
