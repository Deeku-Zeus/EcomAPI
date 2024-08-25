<?php

    namespace App\Providers;

    use Illuminate\Support\ServiceProvider;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Log;

    class AutoBindServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         *
         * @return void
         */
        public function register()
        {
            $this->autoBindBackendRepositories();
        }

        /**
         * Automatically bind interfaces to implementations.
         *
         * @return void
         */
        protected function autoBindBackendRepositories()
        {
            $namespace = 'App\\Repositories\\EcomBackend\\';
            $path = app_path('Repositories/EcomBackend');

            foreach (glob($path . '/*', GLOB_ONLYDIR) as $dir) {
                $repositoryNamespace = $namespace . basename($dir) . '\\';
                foreach (glob($dir . '/*.php') as $file) {
                    $className = basename($file, '.php');
                    if (Str::endsWith($className, 'RepositoryInterface')) {
                        $interface = $repositoryNamespace . $className;
                        $implementation = $repositoryNamespace . Str::replaceLast('Interface', '', $className);

                        if (class_exists($implementation)) {
                            $this->app->bind($interface, $implementation);
                            Log::info("Bound $interface to $implementation");
                        } else {
                            Log::warning("Class $implementation does not exist for interface $interface");
                        }
                    }
                }
            }
        }
    }
