<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CustomRequest;
use App\Models\SportsRegistration;
use App\Models\HealthRequest;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use App\Models\RegistrationResponse;

class MyRequests extends Component
{
    public $search = '';
    public $statusFilter = '';

    public function render()
    {
        $user = auth()->user();
        $email = $user ? trim(strtolower($user->email)) : '';

        $requests = CustomRequest::where(function($q) use ($user, $email) {
                if ($user) {
                    $q->where('user_id', $user->id);
                    if ($email) {
                        $q->orWhereRaw('LOWER(email) = ?', [$email]);
                    }
                } elseif ($email) {
                    $q->whereRaw('LOWER(email) = ?', [$email]);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })->latest()->get();

        $sportsRegistrations = SportsRegistration::where(function($q) use ($user, $email) {
                if ($user) {
                    $q->where('user_id', $user->id);
                    if ($email) {
                        $q->orWhereRaw('LOWER(email) = ?', [$email]);
                    }
                } elseif ($email) {
                    $q->whereRaw('LOWER(email) = ?', [$email]);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })->latest()->get();

        $healthRequests = HealthRequest::where(function($q) use ($user, $email) {
                if ($user) {
                    $q->where('user_id', $user->id);
                    if ($email) {
                        $q->orWhereRaw('LOWER(email) = ?', [$email]);
                    }
                } elseif ($email) {
                    $q->whereRaw('LOWER(email) = ?', [$email]);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })->latest()->get();

        $medicineRequests = MedicineRequest::where(function($q) use ($user, $email) {
                if ($user) {
                    $q->where('user_id', $user->id);
                    if ($email) {
                        $q->orWhereRaw('LOWER(email) = ?', [$email]);
                    }
                } elseif ($email) {
                    $q->whereRaw('LOWER(email) = ?', [$email]);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })->latest()->get();

        $silidRequests = SilidKarununganRequest::where(function($q) use ($user, $email) {
                if ($user) {
                    $q->where('user_id', $user->id);
                    if ($email) {
                        $q->orWhereRaw('LOWER(email) = ?', [$email]);
                    }
                } elseif ($email) {
                    $q->whereRaw('LOWER(email) = ?', [$email]);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })->latest()->get();

        $registrationResponses = RegistrationResponse::where(function($q) use ($user, $email) {
                if ($user) {
                    $q->where('user_id', $user->id);
                    if ($email) {
                        $q->orWhereRaw('LOWER(citizen_email) = ?', [$email]);
                    }
                } elseif ($email) {
                    $q->whereRaw('LOWER(citizen_email) = ?', [$email]);
                } else {
                    $q->whereRaw('1 = 0');
                }
            })->latest()->get();

        $mappedRequests = $requests->map(function($req) {
            return [
                'reference_number' => $req->reference_number ?? ('SK-REQ-' . str_pad($req->id, 5, '0', STR_PAD_LEFT)),
                'type_label' => $req->type ?? 'Custom Request',
                'detail' => $req->description ?? $req->details ?? 'N/A',
                'status' => $req->status ?? 'pending',
                'created_at' => $req->created_at ? $req->created_at->format('M d, Y') : now()->format('M d, Y'),
                'raw_date' => $req->created_at,
            ];
        });

        $mappedSports = $sportsRegistrations->map(function($sport) {
            return [
                'reference_number' => $sport->reference_number ?? ('SK-SPT-' . str_pad($sport->id, 5, '0', STR_PAD_LEFT)),
                'type_label' => 'SIKLAB Sports (' . ($sport->sport ?? 'Tournament') . ' - ' . ($sport->division ?? 'General') . ')',
                'detail' => 'Position: ' . ($sport->position ?? 'N/A') . ($sport->team_name ? ' | Team: ' . $sport->team_name : ''),
                'status' => $sport->status ?? 'pending',
                'created_at' => $sport->created_at ? $sport->created_at->format('M d, Y') : now()->format('M d, Y'),
                'raw_date' => $sport->created_at,
            ];
        });

        $mappedHealth = $healthRequests->map(function($req) {
            return [
                'reference_number' => $req->reference_number ?? ('SK-REQ-' . str_pad($req->id, 5, '0', STR_PAD_LEFT)),
                'type_label' => 'Health Request',
                'detail' => 'Concerns: ' . ($req->concerns ?? 'N/A'),
                'status' => $req->status ?? 'pending',
                'created_at' => $req->created_at ? $req->created_at->format('M d, Y') : now()->format('M d, Y'),
                'raw_date' => $req->created_at,
            ];
        });

        $mappedMedicine = $medicineRequests->map(function($req) {
            return [
                'reference_number' => $req->reference_number ?? ('SK-REQ-' . str_pad($req->id, 5, '0', STR_PAD_LEFT)),
                'type_label' => 'Medicine Request',
                'detail' => 'Complete Address: ' . ($req->complete_address ?? 'N/A'),
                'status' => $req->status ?? 'pending',
                'created_at' => $req->created_at ? $req->created_at->format('M d, Y') : now()->format('M d, Y'),
                'raw_date' => $req->created_at,
            ];
        });

        $mappedSilid = $silidRequests->map(function($req) {
            $preferredDate = $req->preferred_date;
            $dateStr = $preferredDate instanceof \Carbon\Carbon ? $preferredDate->format('Y-m-d') : ($preferredDate ?? 'N/A');
            return [
                'reference_number' => $req->reference_number ?? ('SK-REQ-' . str_pad($req->id, 5, '0', STR_PAD_LEFT)),
                'type_label' => 'Silid Karunungan Request',
                'detail' => 'Preferred Date: ' . $dateStr . ' | Time: ' . ($req->preferred_time ?? 'N/A'),
                'status' => $req->status ?? 'pending',
                'created_at' => $req->created_at ? $req->created_at->format('M d, Y') : now()->format('M d, Y'),
                'raw_date' => $req->created_at,
            ];
        });

        $mappedResponses = $registrationResponses->map(function($resp) {
            return [
                'reference_number' => $resp->reference_number ?? ('SK-REG-' . str_pad($resp->id, 5, '0', STR_PAD_LEFT)),
                'type_label' => 'Sports Registration (' . ($resp->registrationForm?->division_name ?? 'Dynamic') . ')',
                'detail' => 'Citizen: ' . ($resp->citizen_name ?? 'N/A'),
                'status' => $resp->status ?? 'Pending',
                'created_at' => $resp->created_at ? $resp->created_at->format('M d, Y') : now()->format('M d, Y'),
                'raw_date' => $resp->created_at,
            ];
        });

        $results = $mappedRequests
            ->concat($mappedSports)
            ->concat($mappedHealth)
            ->concat($mappedMedicine)
            ->concat($mappedSilid)
            ->concat($mappedResponses)
            ->sortByDesc('raw_date');

        if ($this->search) {
            $q = strtolower($this->search);
            $results = $results->filter(function($r) use ($q) {
                return str_contains(strtolower($r['reference_number']), $q) ||
                       str_contains(strtolower($r['type_label']), $q) ||
                       str_contains(strtolower($r['detail']), $q);
            });
        }

        if ($this->statusFilter) {
            $f = strtolower($this->statusFilter);
            $results = $results->filter(function($r) use ($f) {
                $status = strtolower($r['status']);
                if ($f === 'pending') {
                    return in_array($status, ['pending', 'review', 'under_review']);
                }
                if ($f === 'approved') {
                    return in_array($status, ['approved', 'confirmed', 'completed', 'active']);
                }
                if ($f === 'declined') {
                    return in_array($status, ['declined', 'rejected', 'cancelled']);
                }
                return $status === $f;
            });
        }

        return view('livewire.my-requests', [
            'requestsList' => $results->values(),
            'total' => $results->count(),
            'pending' => $results->filter(fn($r) => in_array(strtolower($r['status']), ['pending', 'review', 'under_review']))->count(),
            'approved' => $results->filter(fn($r) => in_array(strtolower($r['status']), ['approved', 'confirmed', 'completed', 'active']))->count(),
            'declined' => $results->filter(fn($r) => in_array(strtolower($r['status']), ['declined', 'rejected', 'cancelled']))->count(),
        ]);
    }
}
