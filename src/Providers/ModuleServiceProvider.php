<?php

namespace KodiCMS\Logs\Providers;

use Arcanedev\LogViewer\LogViewerServiceProvider;
use Navigation;

class ModuleServiceProvider extends LogViewerServiceProvider
{

    public function boot()
    {
        $this->publishConfig();
        $this->publishViews();
        $this->publishTranslations();
    }

    public function contextBackend()
    {
        $navigation = \Navigation::getPages()->findById('system');

        $navigation->setFromArray([
            [
                'id' => 'logs',
                'title' => trans('logs::core.title.index'),
                'url' => route('log-viewer::logs.list'),
                'priority' => 1000,
                'icon' => 'bar-chart',
            ]
        ]);
    }
}