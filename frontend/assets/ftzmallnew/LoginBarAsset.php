<?php

/**
 * @link http://www.ftzmall.com/
 * @copyright Copyright (c) 2008 Ftzmall&IBM Software LLC
 * @license http://www.ftzmall.com/license/
 */

namespace frontend\assets\ftzmallnew;

use yii\web\AssetBundle;

/**
 * @author daichengjun <daicjun@cn.ibm.com>
 * @since 0.3
 */
class LoginBarAsset extends AssetBundle
{

    public $basePath = '@webroot';
    public $baseUrl = '@web/themes/ftzmallnew';
    public $js =[
        'src/js/cbt.ft.login.js',
    ];
    
    public $jsOptions = ['position' => \yii\web\View::POS_END];

}
