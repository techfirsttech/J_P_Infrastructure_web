<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class BaseModel extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        // Handle created_by field when creating a model
        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });

        // Handle updated_by field when updating a model
        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });

        // Handle deleted_by field for soft deletes
        static::deleting(function ($model) {
            if (!$model->isForceDeleting()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });

        // Handle deleted_by for force deletes (optional)
        static::forceDeleting(function ($model) {
            $model->deleted_by = Auth::id();
            $model->saveQuietly();
        });

        static::created(function ($model) {
            ChangeLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'old_values' => null,
                'new_values' => json_encode($model->getAttributes()),
            ]);
        });

        static::updating(function ($model) {
            $oldValues = $model->getOriginal();
            $newValues = $model->getAttributes();

            ChangeLog::create([
                'user_id' => Auth::id(),
                'action' => 'edit',
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode($newValues),
            ]);
        });

        static::deleting(function ($model) {
            ChangeLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'old_values' => json_encode($model->getAttributes()),
                'new_values' => null,
            ]);
        });

        static::restoring(function ($model) {
            ChangeLog::create([
                'user_id' => Auth::id(),
                'action' => 'restore',
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'old_values' => json_encode($model->getAttributes()),
                'new_values' => null,
            ]);
        });
    }

    // Check if the model uses SoftDeletes
    protected function usesSoftDeletes()
    {
        return in_array(SoftDeletes::class, class_uses($this));
    }
}
