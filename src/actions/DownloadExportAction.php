<?php

declare(strict_types=1);

namespace hiqdev\yii2\export\actions;

use hipanel\actions\IndexAction;
use hiqdev\yii2\export\models\ExportJob;
use hiqdev\yii2\export\models\SaveManager;
use Yii;

class DownloadExportAction extends IndexAction
{
    public function run()
    {
        $id = $this->controller->request->get('id');
        /** @var ExportJob $job */
        $job = Yii::$app->exporter->getJob($id);
        if ($job && $job->getStatus() === ExportJob::STATUS_SUCCESS) {
            $job->deleteJob();
            $saver = new SaveManager($id);
            $stream = $saver->getStream($job->mimeType);
            $saver->delete();

            return $this->controller->response->sendStreamAsFile($stream, $job->getFilename());
        }
        Yii::$app->end();
    }
}
