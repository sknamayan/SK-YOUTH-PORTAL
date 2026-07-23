<?php

namespace App\Services;

use App\Helpers\PrivacyHelper;
use App\Models\CustomRequest;
use App\Models\HealthRequest;
use App\Models\Initiative;
use App\Models\MedicineRequest;
use App\Models\Notification;
use App\Models\SilidKarununganRequest;
use App\Models\RegistrationResponse;
use App\Models\SportsRegistration;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RequestManagementService
{
    private const TYPE_MAP = [
        'custom' => CustomRequest::class,
        'health' => HealthRequest::class,
        'medicine' => MedicineRequest::class,
        'silid' => SilidKarununganRequest::class,
        'sports' => SportsRegistration::class,
    ];

    private const TYPE_LABELS = [
        'custom' => 'Custom Request',
        'health' => 'Health Request',
        'medicine' => 'Medicine Request',
        'silid' => 'Silid Karunungan Request',
        'sports' => 'Sports Registration',
    ];

    public function __construct(
        private readonly MailDispatchService $mailDispatch
    ) {}

    /**
     * Build the citizen requests index view data.
     */
    public function buildRequestsIndexData(Request $request): array
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $yearFilter = $request->input('year');
        $divisionFilter = $request->input('division');
        $limit = $this->normalizeLimit($request->input('limit', 10));
        $activeTab = $request->query('tab', 'all');
        $cutoff = now()->subDays(30);

        $initiatives = Initiative::all();
        $this->attachInitiativeStats($initiatives, $cutoff);

        $allStats = $this->computeAllStats($cutoff);
        $archivedStats = $this->computeArchivedStats($cutoff);
        $activeInitiative = null;

        if ($activeTab === 'all') {
            $paginatedRequests = $this->paginateUnifiedActive($search, $status, $yearFilter, $limit, $cutoff);
        } elseif ($activeTab === 'archive') {
            $paginatedRequests = $this->paginateUnifiedArchive($search, $status, $yearFilter, $limit, $cutoff);
        } else {
            $id = str_replace('init_', '', $activeTab);
            $activeInitiative = Initiative::findOrFail($id);
            $paginatedRequests = $this->paginateInitiativeTab(
                $activeInitiative,
                $search,
                $status,
                $yearFilter,
                $divisionFilter,
                $limit,
                $cutoff
            );
        }

        $currentUser = auth()->user();
        $paginatedRequests->getCollection()->transform(
            fn ($item) => PrivacyHelper::filterPII($item, $currentUser)
        );

        return [
            'initiatives' => $initiatives,
            'paginatedRequests' => $paginatedRequests,
            'activeTab' => $activeTab,
            'activeInitiative' => $activeInitiative,
            'search' => $search,
            'status' => $status,
            'allStats' => $allStats,
            'archivedStats' => $archivedStats,
            'yearFilter' => $yearFilter,
            'divisionFilter' => $divisionFilter,
            'limit' => $limit,
            'years' => $this->distinctRequestYears(),
        ];
    }

    /**
     * Resolve and load a request model by type slug.
     */
    public function resolveModel(string $type, int|string $id)
    {
        $class = self::TYPE_MAP[$type] ?? null;
        if (!$class) {
            abort(404, 'Invalid request type.');
        }

        return $class::findOrFail($id);
    }

    public function resolveTypeName(string $type): string
    {
        return self::TYPE_LABELS[$type] ?? 'General Request';
    }

    /**
     * Mark pending request as under review when opened.
     */
    public function markUnderReviewIfPending(object $requestModel, string $type): void
    {
        if ($requestModel->status !== 'pending') {
            return;
        }

        $requestModel->status = 'review';
        $requestModel->save();

        $user = User::where('email', $requestModel->email)->first();
        if ($user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Request Under Review',
                'message' => 'Your ' . $this->resolveTypeName($type) . ' request is now under review.',
                'url' => route('profile.my-requests'),
                'type' => ($type === 'sports') ? 'sports_league' : 'service_request',
            ]);
        }

        $this->mailDispatch->queueStatusChanged($requestModel);
    }

    /**
     * Update request status and notify the citizen.
     */
    public function updateStatus(string $type, int|string $id, string $status): object
    {
        if (!in_array($status, ['pending', 'review', 'approved', 'declined'])) {
            abort(422, 'Invalid status state.');
        }

        $requestModel = $this->resolveModel($type, $id);
        $requestModel->status = $status;
        $requestModel->processed_by = in_array($status, ['approved', 'declined'])
            ? auth()->id()
            : null;
        $requestModel->save();

        $user = User::where('email', $requestModel->email)->first();
        if ($user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Request Status: ' . ucfirst($status),
                'message' => 'Your ' . $this->resolveTypeName($type) . ' request status has been updated to ' . ucfirst($status) . '.',
                'url' => route('profile.my-requests'),
                'type' => ($type === 'sports') ? 'sports_league' : 'service_request',
            ]);
        }

        $this->mailDispatch->queueStatusChanged($requestModel);

        return $requestModel;
    }

    private function normalizeLimit(mixed $limit): int
    {
        $limit = (int) $limit;

        return in_array($limit, [10, 15, 25, 50, 100], true) ? $limit : 10;
    }

    private function paginateUnifiedActive(?string $search, ?string $status, ?string $year, int $limit, $cutoff): LengthAwarePaginator
    {
        $queries = [
            $this->healthSelect()->where(fn ($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff)),
            $this->medicineSelect()->where(fn ($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff)),
            $this->silidSelect()->where(fn ($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff)),
            $this->sportsSelect(),
            $this->customSelect()->where(fn ($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff)),
        ];

        return $this->paginateUnion($queries, $search, $status, $year, $limit, 'active');
    }

    private function paginateUnifiedArchive(?string $search, ?string $status, ?string $year, int $limit, $cutoff): LengthAwarePaginator
    {
        $archivedInitiatives = Initiative::onlyTrashed()->get();
        $archivedInitiativeIds = $archivedInitiatives->pluck('id')->toArray();
        $archivedRoutes = $archivedInitiatives->pluck('form_route')->filter()->toArray();

        $queries = [];

        $healthArch = $this->healthSelect();
        if (!in_array('forms.health.create', $archivedRoutes) && !in_array('forms.mental-health.create', $archivedRoutes)) {
            $healthArch->where('status', 'approved')->where('updated_at', '<', $cutoff);
        }
        $queries[] = $healthArch;

        $medicineArch = $this->medicineSelect();
        if (!in_array('forms.medicine.create', $archivedRoutes)) {
            $medicineArch->where('status', 'approved')->where('updated_at', '<', $cutoff);
        }
        $queries[] = $medicineArch;

        $silidArch = $this->silidSelect();
        if (!in_array('forms.silid.create', $archivedRoutes)) {
            $silidArch->where('status', 'approved')->where('updated_at', '<', $cutoff);
        }
        $queries[] = $silidArch;

        $sportsArch = $this->sportsSelect();
        if (!in_array('forms.sports.create', $archivedRoutes)) {
            $sportsArch->whereRaw('1 = 0');
        }
        $queries[] = $sportsArch;

        $customArch = $this->customSelect()->where(function ($q) use ($archivedInitiativeIds, $cutoff) {
            $q->whereIn('initiative_id', $archivedInitiativeIds)
                ->orWhere(function ($sub) use ($cutoff) {
                    $sub->where('status', 'approved')->where('updated_at', '<', $cutoff);
                });
        });
        $queries[] = $customArch;

        return $this->paginateUnion($queries, $search, $status, $year, $limit, 'archive', $cutoff);
    }

    private function paginateInitiativeTab(
        Initiative $initiative,
        ?string $search,
        ?string $status,
        ?string $year,
        ?string $division,
        int $limit,
        $cutoff
    ): LengthAwarePaginator {
        // Determine base query for the initiative
        if ($initiative->form_route) {
            $query = match ($initiative->form_route) {
                // Highlighted programs: show all records without cutoff filter
                'forms.health.create', 'forms.mental-health.create' => HealthRequest::with('processedBy')->latest(),
                'forms.medicine.create' => MedicineRequest::with('processedBy')->latest(),
                'forms.silid.create' => SilidKarununganRequest::with('processedBy')->latest(),
                // Sports retains existing behavior (includes cutoff filter)
                'forms.sports.create' => SportsRegistration::with('processedBy')->latest(),
                // Custom initiatives keep existing cutoff logic
                default => CustomRequest::where('initiative_id', $initiative->id)
                    ->where(fn ($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))
                    ->with(['processedBy', 'initiative'])
                    ->latest(),
            };
        } else {
            $query = CustomRequest::where('initiative_id', $initiative->id)
                ->where(fn ($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff))
                ->with(['processedBy', 'initiative'])
                ->latest();
        }

        // Apply common filters (search, status, year)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status && in_array($status, ['pending', 'approved', 'declined'], true)) {
            $query->whereIn('status', $status === 'pending' ? ['pending', 'review'] : [$status]);
        }

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        if ($initiative->form_route === 'forms.sports.create' && $division) {
            $this->applySportsDivisionFilter($query, $division);
        }

        $paginated = $query->paginate($limit, ['*'], 'cPage')->withQueryString();
        $type = $this->initiativeTypeSlug($initiative);

        $paginated->getCollection()->transform(function ($item) use ($type, $initiative) {
            return $this->decorateModel($item, $type, $initiative->title);
        });

        return $paginated;
    }

    /**
     * @param  array<int, Builder>  $queries
     */
    private function paginateUnion(
        array $queries,
        ?string $search,
        ?string $status,
        ?string $year,
        int $limit,
        string $context = 'active',
        $cutoff = null
    ): LengthAwarePaginator {
        $union = null;
        foreach ($queries as $query) {
            $this->applyUnionFilters($query, $search, $status, $year);
            $union = $union ? $union->unionAll($query) : $query;
        }

        if (!$union) {
            return new Paginator(collect(), 0, $limit, 1, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'cPage',
            ]);
        }

        $wrapped = DB::query()->fromSub($union, 'unified_requests');
        $total = (clone $wrapped)->count();
        $page = Paginator::resolveCurrentPage('cPage');

        $rows = (clone $wrapped)
            ->orderByDesc('created_at')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get();

        $models = $this->hydrateUnifiedRows($rows, $context, $cutoff);

        return new Paginator(
            $models,
            $total,
            $limit,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'pageName' => 'cPage']
        );
    }

    private function applyUnionFilters(Builder $query, ?string $search, ?string $status, ?string $year): void
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status && in_array($status, ['pending', 'approved', 'declined'], true)) {
            $query->whereIn('status', $status === 'pending' ? ['pending', 'review'] : [$status]);
        }

        if ($year) {
            $driver = DB::connection()->getDriverName();
            if ($driver === 'sqlite') {
                $query->whereRaw("strftime('%Y', created_at) = ?", [$year]);
            } else {
                $query->whereYear('created_at', $year);
            }
        }
    }

    private function hydrateUnifiedRows(Collection $rows, string $context, $cutoff = null): Collection
    {
        if ($rows->isEmpty()) {
            return collect();
        }

        $grouped = $rows->groupBy('request_type');
        $loaded = [];

        foreach ($grouped as $type => $typeRows) {
            $ids = $typeRows->pluck('id')->all();
            $class = self::TYPE_MAP[$type];
            $with = ['processedBy'];
            if ($type === 'custom') {
                $with[] = 'initiative';
            }

            $models = $class::with($with)->whereIn('id', $ids)->get()->keyBy('id');
            $loaded[$type] = $models;
        }

        return $rows->map(function ($row) use ($loaded, $context, $cutoff) {
            $model = $loaded[$row->request_type][$row->id] ?? null;
            if (!$model) {
                return null;
            }

            $suffix = '';
            if ($context === 'archive') {
                $isAuto = $cutoff && $model->status === 'approved' && $model->updated_at->lt($cutoff);
                $suffix = ($isAuto ? ' (Auto-Archived)' : ' (Archived Form)');
            }

            $typeName = self::TYPE_LABELS[$row->request_type] ?? 'Request';
            if ($row->request_type === 'custom' && $model->initiative) {
                $typeName = $model->initiative->title;
            }

            return $this->decorateModel($model, $row->request_type, $typeName . $suffix);
        })->filter()->values();
    }

    private function decorateModel(object $item, string $type, string $typeName): object
    {
        $item->type = $type;
        $item->type_name = $typeName;

        if ($type === 'medicine' || $type === 'silid') {
            $item->display_name = $item->requestor_last_name . ', ' . $item->requestor_first_name;
            $item->first_name = $item->requestor_first_name;
            $item->last_name = $item->requestor_last_name;
        } else {
            $item->display_name = $item->last_name . ', ' . $item->first_name;
        }

        return $item;
    }

    private function healthSelect(): Builder
    {
        return DB::table('health_requests')->select([
            DB::raw("'health' as request_type"),
            'id',
            'first_name',
            'last_name',
            'email',
            'status',
            'created_at',
            'updated_at',
            'processed_by',
            DB::raw('NULL as initiative_id'),
        ]);
    }

    private function medicineSelect(): Builder
    {
        return DB::table('medicine_requests')->select([
            DB::raw("'medicine' as request_type"),
            'id',
            DB::raw('requestor_first_name as first_name'),
            DB::raw('requestor_last_name as last_name'),
            'email',
            'status',
            'created_at',
            'updated_at',
            'processed_by',
            DB::raw('NULL as initiative_id'),
        ]);
    }

    private function silidSelect(): Builder
    {
        return DB::table('silid_karunungan_requests')->select([
            DB::raw("'silid' as request_type"),
            'id',
            DB::raw('requestor_first_name as first_name'),
            DB::raw('requestor_last_name as last_name'),
            'email',
            'status',
            'created_at',
            'updated_at',
            'processed_by',
            DB::raw('NULL as initiative_id'),
        ]);
    }

    private function sportsSelect(): Builder
    {
        return DB::table('sports_registrations')->select([
            DB::raw("'sports' as request_type"),
            'id',
            'first_name',
            'last_name',
            'email',
            'status',
            'created_at',
            'updated_at',
            'processed_by',
            DB::raw('NULL as initiative_id'),
        ]);
    }

    private function customSelect(): Builder
    {
        return DB::table('custom_requests')->select([
            DB::raw("'custom' as request_type"),
            'id',
            'first_name',
            'last_name',
            'email',
            'status',
            'created_at',
            'updated_at',
            'processed_by',
            'initiative_id',
        ]);
    }

    private function attachInitiativeStats(Collection $initiatives, $cutoff): void
    {
        $healthStats = $this->tableStats(HealthRequest::class, $cutoff);
        $medicineStats = $this->tableStats(MedicineRequest::class, $cutoff);
        $silidStats = $this->tableStats(SilidKarununganRequest::class, $cutoff);
        $sportsStats = $this->tableStats(SportsRegistration::class, null);

        foreach ($initiatives as $init) {
            if ($init->form_route) {
                $init->stats = match ($init->form_route) {
                    'forms.health.create', 'forms.mental-health.create' => $healthStats,
                    'forms.medicine.create' => $medicineStats,
                    'forms.silid.create' => $silidStats,
                    'forms.sports.create' => $sportsStats,
                    default => $this->customInitiativeStats($init->id, $cutoff),
                };
            } else {
                $init->stats = $this->customInitiativeStats($init->id, $cutoff);
            }
        }
    }

    private function tableStats(string $modelClass, $cutoff): array
    {
        $base = $modelClass::query();
        if ($cutoff) {
            $base->where(fn ($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff));
        }

        return [
            'total' => (clone $base)->count(),
            'pending' => $modelClass::whereIn('status', ['pending', 'review'])->count(),
            'approved' => $cutoff
                ? $modelClass::where('status', 'approved')->where('updated_at', '>=', $cutoff)->count()
                : $modelClass::where('status', 'approved')->count(),
            'declined' => $modelClass::where('status', 'declined')->count(),
        ];
    }

    private function customInitiativeStats(int $initiativeId, $cutoff): array
    {
        $base = CustomRequest::where('initiative_id', $initiativeId)
            ->where(fn ($q) => $q->where('status', '!=', 'approved')->orWhere('updated_at', '>=', $cutoff));

        return [
            'total' => (clone $base)->count(),
            'pending' => CustomRequest::where('initiative_id', $initiativeId)->whereIn('status', ['pending', 'review'])->count(),
            'approved' => CustomRequest::where('initiative_id', $initiativeId)->where('status', 'approved')->where('updated_at', '>=', $cutoff)->count(),
            'declined' => CustomRequest::where('initiative_id', $initiativeId)->where('status', 'declined')->count(),
        ];
    }

    private function computeAllStats($cutoff): array
    {
        $health = $this->tableStats(HealthRequest::class, $cutoff);
        $medicine = $this->tableStats(MedicineRequest::class, $cutoff);
        $silid = $this->tableStats(SilidKarununganRequest::class, $cutoff);
        $custom = $this->tableStats(CustomRequest::class, $cutoff);

        return [
            'total' => $health['total'] + $medicine['total'] + $silid['total'] + $custom['total'],
            'pending' => $health['pending'] + $medicine['pending'] + $silid['pending'] + $custom['pending'],
            'approved' => $health['approved'] + $medicine['approved'] + $silid['approved'] + $custom['approved'],
            'declined' => $health['declined'] + $medicine['declined'] + $silid['declined'] + $custom['declined'],
        ];
    }

    private function computeArchivedStats($cutoff): array
    {
        $archivedInitiatives = Initiative::onlyTrashed()->get();
        $archivedInitiativeIds = $archivedInitiatives->pluck('id')->toArray();
        $archivedRoutes = $archivedInitiatives->pluck('form_route')->filter()->toArray();

        $stats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'declined' => 0];

        $customArchQuery = CustomRequest::where(function ($q) use ($archivedInitiativeIds, $cutoff) {
            $q->whereIn('initiative_id', $archivedInitiativeIds)
                ->orWhere(function ($sub) use ($cutoff) {
                    $sub->where('status', 'approved')->where('updated_at', '<', $cutoff);
                });
        });

        foreach (['total', 'pending', 'approved', 'declined'] as $key) {
            $q = clone $customArchQuery;
            if ($key === 'pending') {
                $q->whereIn('status', ['pending', 'review']);
            } elseif ($key !== 'total') {
                $q->where('status', $key);
            }
            $stats[$key] += $q->count();
        }

        $predefinedRoutes = [
            'forms.health.create' => HealthRequest::class,
            'forms.mental-health.create' => HealthRequest::class,
            'forms.medicine.create' => MedicineRequest::class,
            'forms.silid.create' => SilidKarununganRequest::class,
            'forms.sports.create' => SportsRegistration::class,
        ];

        foreach ($predefinedRoutes as $route => $modelClass) {
            $isArchived = in_array($route, $archivedRoutes, true);
            $query = $modelClass::query();

            if (!$isArchived) {
                if ($route === 'forms.sports.create') {
                    $query->whereRaw('1 = 0');
                } else {
                    $query->where('status', 'approved')->where('updated_at', '<', $cutoff);
                }
            }

            $stats['total'] += (clone $query)->count();
            $stats['pending'] += (clone $query)->whereIn('status', ['pending', 'review'])->count();
            $stats['approved'] += (clone $query)->where('status', 'approved')->count();
            $stats['declined'] += (clone $query)->where('status', 'declined')->count();
        }

        return $stats;
    }

    private function distinctRequestYears(): array
    {
        $driver = DB::connection()->getDriverName();
        $yearExpr = $driver === 'sqlite'
            ? "strftime('%Y', created_at) as year"
            : 'YEAR(created_at) as year';

        $years = array_unique(array_merge(
            HealthRequest::selectRaw($yearExpr)->distinct()->pluck('year')->toArray(),
            MedicineRequest::selectRaw($yearExpr)->distinct()->pluck('year')->toArray(),
            SilidKarununganRequest::selectRaw($yearExpr)->distinct()->pluck('year')->toArray(),
            SportsRegistration::selectRaw($yearExpr)->distinct()->pluck('year')->toArray(),
            CustomRequest::selectRaw($yearExpr)->distinct()->pluck('year')->toArray(),
        ));
        rsort($years);

        return empty($years) ? [date('Y')] : $years;
    }

    private function initiativeTypeSlug(Initiative $initiative): string
    {
        if (!$initiative->form_route) {
            return 'custom';
        }

        return match ($initiative->form_route) {
            'forms.health.create', 'forms.mental-health.create' => 'health',
            'forms.medicine.create' => 'medicine',
            'forms.silid.create' => 'silid',
            'forms.sports.create' => 'sports',
            default => 'custom',
        };
    }

    private function applySportsDivisionFilter($query, string $division): void
    {
        match ($division) {
            'Basketball Midget Division' => $query->where('sport', 'Basketball')->where(function ($q) {
                $q->where('division', 'Basketball Midget Division')->orWhere('age', '<', 18);
            }),
            'Basketball Senior Division' => $query->where('sport', 'Basketball')->where(function ($q) {
                $q->where('division', 'Basketball Senior Division')->orWhere('age', '>=', 18);
            }),
            'Volleyball Womens' => $query->where('sport', 'Volleyball')->where(function ($q) {
                $q->where('division', 'Volleyball Womens')->orWhere('gender', 'Female');
            }),
            'Volleyball Mens Division' => $query->where('sport', 'Volleyball')->where(function ($q) {
                $q->where('division', 'Volleyball Mens Division')->orWhere('gender', 'Male');
            }),
            default => $query->where('division', $division),
        };
    }
}
