<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use App\Models\Task;
use Filament\Widgets;
use Livewire\Livewire;
use App\Models\TaskProject;
use Filament\PanelProvider;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Filament\Pages\TasksKanban;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Auth;
use App\Filament\Pages\Auth\Register;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use App\Filament\Topbar\Notifications;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Widgets\TopbarNotifications;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login()
            ->registration(Register::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Edit Profile')
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-cog-6-tooth')
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
                \App\Filament\Pages\TasksKanban::class
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->navigationItems([
                NavigationItem::make('New Project')
                    ->url('/task-projects/create')
                    ->icon('heroicon-o-plus-circle')
                    ->group('Projects')
                    ->badge('New')
            ])
            ->sidebarFullyCollapsibleOnDesktop()
            // ->renderHook(
            //     PanelsRenderHook::TOPBAR_END,
            //     fn (): string => Blade::render('@livewire(\'topbar-notifications\')')
            // )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentEditProfilePlugin::make()
                    ->shouldRegisterNavigation(false)
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars', // image will be stored in 'storage/app/public/avatars
                        rules: 'mimes:jpeg,png|max:1024', //only accept jpeg and png files with a maximum size of 1MB
                        
                    ),
            ]);
            
    }

    public function boot(): void
    {

        Filament::serving(function () {
            $user = Auth::user();
            if (! $user) return;

            $projects = TaskProject::whereHas('users', fn ($q) => $q->where('users.id', $user->id))->get();

            foreach ($projects as $project) {
                Filament::registerNavigationItems([
                    NavigationItem::make($project->name)
                        ->group('Projects')
                        // ->icon('heroicon-o-clipboard-document-list')
                        ->url('/tasks-kanban?project=' . $project->id)
                        ->badge(Task::where('task_project_id', $project->id)->count())
                        ->isActiveWhen(fn () => request()->query('project') == $project->id)
                ]);
            }
        });

        Livewire::component('notifications', Notifications::class);

        Filament::registerRenderHook(
            PanelsRenderHook::TOPBAR_END,
            fn () => Livewire::mount('notifications', [], 'topbar-notifications')
        );
    }
}
