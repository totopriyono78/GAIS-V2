<?php

namespace App\Support\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Mencatat setiap pembuatan, perubahan, dan penghapusan record ke tabel audit_logs.
 * Nilai lama dan nilai baru disimpan hanya untuk kolom yang benar benar berubah.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            $model->writeAuditLog('created', null, $model->filterAuditValues($model->getAttributes()));
        });

        static::updated(function (Model $model) {
            $changes = $model->filterAuditValues($model->getChanges());

            if ($changes === []) {
                return;
            }

            $old = array_intersect_key($model->getOriginal(), $changes);

            $model->writeAuditLog('updated', $old, $changes);
        });

        static::deleted(function (Model $model) {
            $model->writeAuditLog('deleted', $model->filterAuditValues($model->getOriginal()), null);
        });
    }

    protected function auditExcludedColumns(): array
    {
        $extra = property_exists($this, 'auditExclude') ? $this->auditExclude : [];

        return array_merge(['created_at', 'updated_at', 'remember_token', 'password'], $extra);
    }

    protected function filterAuditValues(array $values): array
    {
        return array_diff_key($values, array_flip($this->auditExcludedColumns()));
    }

    public function getAuditLabel(): string
    {
        foreach (['name', 'full_name', 'title', 'code', 'key'] as $column) {
            if (! empty($this->{$column})) {
                return (string) $this->{$column};
            }
        }

        return '#'.$this->getKey();
    }

    protected function writeAuditLog(string $event, ?array $old, ?array $new): void
    {
        try {
            $user = Auth::user();

            AuditLog::create([
                'user_id' => $user?->getKey(),
                'user_name' => $user?->name,
                'event' => $event,
                'auditable_type' => static::class,
                'auditable_id' => $this->getKey(),
                'auditable_label' => mb_substr($this->getAuditLabel(), 0, 200),
                'old_values' => $old,
                'new_values' => $new,
                'url' => app()->runningInConsole() ? 'console' : mb_substr(request()->fullUrl(), 0, 255),
                'ip_address' => app()->runningInConsole() ? null : request()->ip(),
                'user_agent' => app()->runningInConsole() ? null : mb_substr((string) request()->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $exception) {
            // Jejak audit tidak boleh menggagalkan aksi pengguna. Kegagalannya dicatat ke log aplikasi.
            report($exception);
        }
    }
}
