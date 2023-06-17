<?php

$reasons = CJSON::decode($claim->status->denied->reasons_json);
if ($reasons) {
    $listReasons = '<ul><li>' . implode('</li><li>', $reasons) . '</li></ul>';
    Yii::app()->user->setFlash('error', '<strong>Claim denied due to the following reasons:</strong>' . $listReasons);
}