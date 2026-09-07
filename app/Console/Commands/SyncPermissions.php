<?php

namespace App\Console\Commands;

use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;

class SyncPermissions extends Command
{
    protected $signature = 'gais:sync-permissions {--prune : Hapus izin yang modulnya sudah tidak menyediakan aksi itu}';

    protected $description = 'Menyamakan tabel permissions dengan registri modul';

    public function handle(): int
    {
        $created = 0;
        $removed = 0;

        foreach (Module::query()->get() as $module) {
            $actions = $module->available_actions ?? [];

            foreach ($actions as $action) {
                $key = $module->code.'.'.$action;

                $permission = Permission::query()->firstOrNew(['key' => $key]);

                if (! $permission->exists) {
                    $created++;
                }

                $permission->module_id = $module->id;
                $permission->action = $action;
                $permission->name = Module::actionLabel($action).' '.$module->name;
                $permission->save();
            }

            if ($this->option('prune')) {
                $removed += Permission::query()
                    ->where('module_id', $module->id)
                    ->whereNotIn('action', $actions)
                    ->delete();
            }
        }

        $melekat = $this->segarkanAdministrator();

        $this->info("Izin baru: {$created}. Izin dihapus: {$removed}. Izin pada role administrator: {$melekat}.");

        return self::SUCCESS;
    }

    /**
     * Role administrator selalu memegang seluruh izin.
     *
     * Itu bukan kebetulan melainkan definisinya, dan definisi yang hanya ditegakkan di
     * seeder akan meleset setiap kali ada modul baru: izinnya terbuat, tetapi tidak
     * menempel pada siapa pun, dan menunya lenyap dari layar orang yang seharusnya
     * paling berhak membukanya. Ditegakkan di sini supaya satu perintah cukup.
     *
     * Role lain sengaja tidak disentuh. Modul baru berarti kewenangan baru, dan siapa
     * yang boleh memegangnya adalah keputusan orang, bukan keputusan perintah ini.
     */
    protected function segarkanAdministrator(): int
    {
        $administrator = Role::query()->where('code', 'administrator')->first();

        if ($administrator === null) {
            $this->warn('Role administrator belum ada. Jalankan php artisan db:seed --class=RoleSeeder lebih dulu.');

            return 0;
        }

        $semua = Permission::query()->pluck('id')->all();

        $administrator->permissions()->sync($semua);

        return count($semua);
    }
}
