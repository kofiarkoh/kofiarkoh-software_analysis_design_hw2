<?php

namespace App\Utils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\ChartWidget;

/**
 * Generic monthly "onboarding" (created) trend for any model.
 *
 * Extend this class and override:
 *  - getModel(): class-string<Model>
 *  - getDateColumn(): string   (default: 'created_at')
 *  - getDatasetLabel(): string (default: 'New Records')
 *  - getMonths(): int          (default: 12)
 * Optionally override:
 *  - baseQuery(): Builder      (for scoping/tenancy/filters)
 *  - getHeading(): ?string     (widget title)
 */
abstract class GenericOnboardingTrendWidget extends ChartWidget
{
    protected static ?string $heading = null; // subclasses can set, or override getHeading()

    public function getColumnSpan(): int|string
    {
        return 'full';
    }

    protected function getType(): string
    {
        return 'line';
    }

    /** @return class-string<Model> */
    abstract protected function getModel(): string;

    /** Which timestamp column to use (defaults to created_at). */
    protected function getDateColumn(): string
    {
        return 'created_at';
    }

    /** Dataset label on the chart. */
    protected function getDatasetLabel(): string
    {
        return 'New Records';
    }

    /** How many months back (inclusive of current month). */
    protected function getMonths(): int
    {
        return 12;
    }

    /** Base query hook for scoping/tenancy/filtering. */
    protected function baseQuery(): Builder
    {
        $model = $this->getModel();
        /** @var Model $instance */
        $instance = new $model();
        return $instance->newQuery();
    }

    protected function getData(): array
    {
        $months = max(1, (int) $this->getMonths());

        $start = now()->startOfMonth()->subMonths($months - 1);
        $end   = now()->endOfMonth();

        $dateCol = $this->getDateColumn();
        $driver  = DB::getDriverName();

        // Build DB-specific month key expression
        $monthExpr = match ($driver) {
            'mysql'   => "DATE_FORMAT($dateCol, '%Y-%m')",
            'pgsql'   => "to_char(date_trunc('month', $dateCol), 'YYYY-MM')",
            'sqlite'  => "strftime('%Y-%m', $dateCol)",
            'sqlsrv'  => "FORMAT($dateCol, 'yyyy-MM')",
            default   => "DATE_FORMAT($dateCol, '%Y-%m')", // sensible default
        };

        $rows = $this->baseQuery()
            ->whereBetween($dateCol, [$start, $end])
            ->selectRaw("$monthExpr as ym, COUNT(*) as cnt")
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('cnt', 'ym'); // ['2025-01' => 10, ...]

        // Build labels & data for each month in the range (fill gaps with 0)
        $labels = [];
        $data   = [];

        for ($i = 0; $i < $months; $i++) {
            $month = (clone $start)->addMonths($i);
            $key   = $month->format('Y-m');
            $labels[] = $month->format('M Y');
            $data[]   = (int) ($rows[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => $this->getDatasetLabel(),
                    'data' => $data,
                    'borderColor' => 'rgb(26, 67, 173)',
                    'backgroundColor' => 'rgba(26, 67, 173, 0.15)',
                    'tension' => 0.3,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 4,
                    'fill' => true,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['display' => false],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
            'plugins' => [
                'legend' => ['display' => true],
                'tooltip' => ['enabled' => true],
            ],
        ];
    }
}
