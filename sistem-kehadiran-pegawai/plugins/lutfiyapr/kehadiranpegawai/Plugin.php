<?php

namespace Lutfiyapr\KehadiranPegawai;

use Backend\Facades\Backend;
use Backend\Models\UserRole;
use Illuminate\Support\Facades\Route;
use System\Classes\PluginBase;

/**
 * KehadiranPegawai Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => 'Kehadiran Pegawai',
            'description' => 'Sistem Kehadiran Pegawai dengan RESTful API',
            'author'      => 'Lutfiyapr',
            'icon'        => 'icon-users'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register(): void {}

    /**
     * Boot method, called right before the request route.
     */
    public function boot(): void {
        // Load routes dengan CORS middleware
        Route::middleware(['web', \Lutfiyapr\KehadiranPegawai\Middleware\Cors::class])
            ->group(function () {
                require __DIR__ . '/routes.php';
            });
    }

    /**
     * Registers any frontend components implemented in this plugin.
     */
    public function registerComponents(): array
    {
        return []; // Remove this line to activate

        return [
            // \Lutfiyapr\KehadiranPegawai\Components\MyComponent::class => 'myComponent',
        ];
    }

    /**
     * Registers any backend permissions used by this plugin.
     */
    public function registerPermissions(): array
    {
        return []; // Remove this line to activate

        return [
            'lutfiyapr.kehadiranpegawai.some_permission' => [
                'tab' => 'lutfiyapr.kehadiranpegawai::lang.plugin.name',
                'label' => 'lutfiyapr.kehadiranpegawai::lang.permissions.some_permission',
                'roles' => [UserRole::CODE_DEVELOPER, UserRole::CODE_PUBLISHER],
            ],
        ];
    }

    /**
     * Registers backend navigation items for this plugin.
     */
    public function registerNavigation(): array
    {
        return []; // Remove this line to activate

        return [
            'kehadiranpegawai' => [
                'label'       => 'lutfiyapr.kehadiranpegawai::lang.plugin.name',
                'url'         => Backend::url('lutfiyapr/kehadiranpegawai/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['lutfiyapr.kehadiranpegawai.*'],
                'order'       => 500,
            ],
        ];
    }
}
