<?php

use yii\helpers\Url;
use yii\helpers\Html;
use mobile\assets\MainAsset;
use mobile\assets\RegisterAsset;
MainAsset::register($this);
RegisterAsset::register($this);
/* @var $this yii\web\View */
$this->title = '会员登录_上海外高桥进口商品网';
?>

    <?php $this->beginPage() ?>
        <!DOCTYPE html>
        <html lang="<?= Yii::$app->language ?>" class="am-touch js cssanimations">

        <head>
            <meta charset="<?= Yii::$app->charset ?>">
            <?= Html::csrfMetaTags() ?>
                <title>
                    <?= Html::encode($this->title) ?>
                </title>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="renderer" content="webkit">
                <meta name="generator" content="ecos.b2c">
                <meta http-equiv="Cache-Control" content="no-siteapp" />
                <meta name="keywords" content="上海外高桥进口商品网">
                <meta name="description" content="上海外高桥进口商品网是一家以销售...">
                <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
                <?php $this->head() ?>
        </head>

        <body>
            <?php $this->beginBody() ?>

                <?=$content?>

            <?php $this->endBody() ?>
        </body>

        </html>
        <?php $this->endPage() ?>
