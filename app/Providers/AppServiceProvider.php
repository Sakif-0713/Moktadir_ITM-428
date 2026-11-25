<?php

namespace App\Providers;

use App\Services\Facades\EvaluationFacade;
use App\Services\Facades\EvaluationFacadeInterface;
use App\Services\RAG\Providers\GeminiProvider;
use App\Services\RAG\Providers\LLMProviderInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LLMProviderInterface::class, GeminiProvider::class);
        $this->app->bind(EvaluationFacadeInterface::class, EvaluationFacade::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
