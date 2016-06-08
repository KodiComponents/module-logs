<?php

namespace KodiCMS\Logs\Http\Controllers;

use Arcanedev\LogViewer\Exceptions\LogNotFound;
use Arcanedev\LogViewer\LogViewer;
use Illuminate\Pagination\LengthAwarePaginator;
use KodiCMS\CMS\Http\Controllers\System\BackendController;

class LogViewerController extends BackendController
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    protected $perPage = 30;

    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * The log viewer instance
     *
     * @var LogViewer
     */
    protected $logViewer;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    public function boot()
    {
        parent::boot();

        $this->logViewer = app('arcanedev.log-viewer');
    }

    public function listLogs()
    {
        $stats = $this->logViewer->statsTable();

        $headers = $stats->header();
        // $footer   = $stats->footer();

        $page   = request('page', 1);
        $offset = ($page * $this->perPage) - $this->perPage;

        $rows = new LengthAwarePaginator(array_slice($stats->rows(), $offset, $this->perPage, true), count($stats->rows()), $this->perPage, $page);

        $rows->setPath(request()->url());

        $this->setContent('logs', compact('headers', 'rows', 'footer'));
    }

    /**
     * Show the log.
     *
     * @param  string  $date
     *
     * @return \Illuminate\View\View
     */
    public function show($date)
    {
        $log     = $this->getLogOrFail($date);
        $levels  = $this->logViewer->levelsNames();
        $entries = $log->entries()->paginate($this->perPage);

        $this->breadcrumbs->add($date);

        $this->setContent('show', compact('log', 'levels', 'entries'));
    }

    /**
     * Filter the log entries by level.
     *
     * @param  string  $date
     * @param  string  $level
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showByLevel($date, $level)
    {
        $log = $this->getLogOrFail($date);

        if ($level == 'all') {
            return redirect()->route('log-viewer::logs.show', [$date]);
        }

        $levels  = $this->logViewer->levelsNames();
        $entries = $this->logViewer
            ->entries($date, $level)
            ->paginate($this->perPage);

        $this->breadcrumbs->add($date, route('log-viewer::logs.show', [$date]));
        $this->breadcrumbs->add(trans('log-viewer::levels.'.$level));

        $this->setContent('show', compact('log', 'levels', 'entries'));
    }

    /**
     * Download the log
     *
     * @param  string  $date
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($date)
    {
        return $this->logViewer->download($date);
    }

    /**
     * Delete a log.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete()
    {
        if ( ! request()->ajax()) abort(405, 'Method Not Allowed');

        $date = request()->get('date');
        $ajax = [
            'result' => $this->logViewer->delete($date) ? 'success' : 'error'
        ];

        return response()->json($ajax);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Get a log or fail
     *
     * @param  string  $date
     *
     * @return Log|null
     */
    private function getLogOrFail($date)
    {
        $log = null;

        try {
            $log = $this->logViewer->get($date);
        }
        catch(LogNotFound $e) {
            abort(404, $e->getMessage());
        }

        return $log;
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