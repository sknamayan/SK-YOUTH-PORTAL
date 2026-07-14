<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class SystemAuditObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $type = class_basename($model);
        $action = strtolower($type) . '_created';
        
        $payload = $this->getPayload($model, 'created');

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => get_class($model),
            'subject_id' => $model->getKey(),
            'payload' => $payload,
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $type = class_basename($model);
        $action = strtolower($type) . '_updated';
        
        $payload = $this->getPayload($model, 'updated');

        // Prevent logging if nothing meaningful changed
        if (isset($payload['changes']) && empty($payload['changes'])) {
            return;
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => get_class($model),
            'subject_id' => $model->getKey(),
            'payload' => $payload,
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $type = class_basename($model);
        $action = strtolower($type) . '_deleted';
        
        $payload = $this->getPayload($model, 'deleted');

        $userId = auth()->id();
        // If a user deletes their own account, we set user_id to null in the log to prevent foreign key issues
        if ($model instanceof \App\Models\User && $model->id === $userId) {
            $userId = null;
        }

        ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'subject_type' => get_class($model),
            'subject_id' => $model->getKey(),
            'payload' => $payload,
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Construct a customized payload descriptive of the model change.
     */
    private function getPayload(Model $model, string $event): array
    {
        $payload = [];
        
        if ($event === 'updated') {
            $changes = [];
            foreach ($model->getDirty() as $key => $value) {
                // Don't log passwords or tokens in plain text
                if (in_array($key, ['password', 'remember_token'])) {
                    $changes[$key] = [
                        'from' => '[REDACTED]',
                        'to' => '[REDACTED]'
                    ];
                } else {
                    $changes[$key] = [
                        'from' => $model->getOriginal($key),
                        'to' => $value
                    ];
                }
            }
            $payload['changes'] = $changes;
        }

        // Add display descriptions
        switch (class_basename($model)) {
            case 'User':
                $payload['name'] = $model->name;
                $payload['email'] = $model->email;
                $payload['role'] = $model->role;
                break;
            case 'Committee':
                $payload['name'] = $model->name;
                $payload['slug'] = $model->slug;
                break;
            case 'Initiative':
                $payload['title'] = $model->title;
                break;
            case 'Partner':
                $payload['name'] = $model->name;
                break;
            case 'AccomplishmentReport':
                $payload['report_title'] = $model->report_title;
                break;
            case 'CarouselSlide':
                $payload['title'] = $model->title;
                break;
        }

        return $payload;
    }
}
