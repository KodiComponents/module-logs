<?php

namespace KodiCMS\Logs\Providers;

use Arcanedev\LogViewer\LogViewerServiceProvider;
use KodiCMS\Dashboard\WidgetType;
use Navigation;

class ModuleServiceProvider extends LogViewerServiceProvider
{

    public function boot()
    {
        $this->publishConfig();
        $this->publishViews();
        $this->publishTranslations();

        if (\ModulesLoader::getRegisteredModule('dashboard')) {
            app('dashboard.manager')->registerWidget(new WidgetType('logs', 'logs::core.widget.title', 'KodiCMS\Logs\Widget\Logs', 'bar-chart'));
        }
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