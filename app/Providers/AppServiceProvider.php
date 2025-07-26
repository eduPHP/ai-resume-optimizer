<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use App\Contracts\ResumeParser;
use App\Services\SpatieResumeParser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Collection::macro('deepMerge', function (Collection|array $override): Collection {
            /** @var Collection $this */
            foreach (collect($override) as $key => $value) {
                if (is_array($value) && $this->has($key) && is_array($this->get($key))) {
                    $this->put($key, collect($this->get($key))->deepMerge(collect($value)));
                } else {
                    $this->put($key, $value);
                }
            }

            return $this;
        });

        Model::unguard();

        $this->app->singleton(ResumeParser::class, SpatieResumeParser::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
