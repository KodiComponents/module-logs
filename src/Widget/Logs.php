<?php

namespace KodiCMS\Logs\Widget;

use KodiCMS\Dashboard\Widget\WidgetDashboardAbstract;

class Logs extends WidgetDashboardAbstract
{
    /**
     * @var string
     */
    protected $frontendTemplate = 'logs::widget.template';

    /**
     * @var array
     */
    protected $size = [
        'x' => 3,
        'y' => 2,
        'max_size' => [6, 2],
        'min_size' => [3, 2],
    ];

    /**
     * @return array
     */
    public function prepareData()
    {
        $logViewer = app('arcanedev.log-viewer');

        $stats    = $logViewer->statsTable();
        $reports  = $stats->totalsJson();
        $percents = $this->calcPercentages($stats->footer(), $stats->header());

        return compact('reports', 'percents');
    }

    /**
     * Calculate the percentage
     *
     * @param  array  $total
     * @param  array  $names
     *
     * @return array
     */
    private function calcPercentages(array $total, array $names)
    {
        $percents = [];
        $all      = array_get($total, 'all');

        foreach ($total as $level => $count) {
            $percents[$level] = [
                'name'    => $names[$level],
                'count'   => $count,
                'percent' => $all ? round(($count / $all) * 100, 2) : 0,
            ];
        }

        return $percents;
    }
}
