<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncOnixMenu extends Command
{
    protected $signature = 'onix:menu {--role= : Rol al que se asignarán los módulos} {--dry-run : Mostrar los módulos sin guardar}';
    protected $description = 'Registra las pantallas de Onix en el menú existente sin duplicar entradas';

    public function handle()
    {
        $modules = [];
        foreach (app('router')->getRoutes() as $route) {
            $action = $route->getActionName();
            if (substr($action, -6) !== '@index' || strpos($route->uri(), '{') !== false) continue;
            $class = explode('@', $action)[0];
            if (strpos($class, 'App\\Http\\Controllers\\Auth\\') === 0 || $route->uri() === '/') continue;
            $uri = '/' . trim($route->uri(), '/');
            $modules[$uri] = ucwords(str_replace(['-', '_'], ' ', trim($route->uri(), '/')));
        }
        if ($this->option('dry-run')) {
            $this->table(['Ruta', 'Módulo'], collect($modules)->map(function ($name, $uri) { return [$uri, $name]; })->values()->all());
            return 0;
        }
        $role = $this->option('role');
        if ($role && !DB::table('rol')->where('id', $role)->exists()) {
            $this->error('El rol indicado no existe.');
            return 1;
        }
        DB::transaction(function () use ($modules, $role) {
            $order = (int) DB::table('menu')->max('orden');
            foreach ($modules as $uri => $name) {
                $menu = DB::table('menu')->whereIn('url', [$uri, ltrim($uri, '/')])->first();
                $id = $menu ? $menu->id : DB::table('menu')->insertGetId([
                    'menu_id' => 0, 'nombre' => mb_substr($name, 0, 50), 'url' => $uri,
                    'orden' => ++$order, 'icono' => 'fa fa-folder', 'created_at' => now(), 'updated_at' => now(),
                ]);
                if ($role && !DB::table('menu_rol')->where('menu_id', $id)->where('rol_id', $role)->exists()) {
                    DB::table('menu_rol')->insert(['menu_id' => $id, 'rol_id' => $role]);
                }
            }
        });
        $this->info(count($modules) . ' pantallas sincronizadas. Los permisos existentes se conservan.');
        return 0;
    }
}
