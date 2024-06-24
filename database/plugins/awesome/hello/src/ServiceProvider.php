<?php

namespace {{ author_class }}\{{ name_class }};

use Illuminate\Support\ServiceProvider as Base;
use Acelle\Library\Facades\Hook;

class ServiceProvider extends Base
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Define the constants to use in the plugin source (optional)
        define('{{ NAME }}_PLUGIN_FULL_NAME', '{{ plugin }}');
        define('{{ NAME }}_PLUGIN_SHORT_NAME', '{{ name }}');

        // IMPORTANT: the following must be in register() method
        // Register translation folder for Laravel to load from
        // Important: the translation folder's path must be different from the master translation folder's one, notice the "/data/" part in the path
        $translationFolder = storage_path('app/data/plugins/{{ author }}/{{ name }}/lang/');

        // A plugin can register its translation folder like this:
        //
        //     $this->loadTranslationsFrom($translationFolder, '{{ name }}');
        //
        // However, in this sample plugin, just register it to the 'add_translation_file' hook to as follows
        // By registering the hook, the translation shall be managed by the hosting application (create instances for languages, register translation folder with Laravel...)
        Hook::register('add_translation_file', function() use ($translationFolder) {
            return [
                "id" => '#{{ plugin }}_translation_file',
                "plugin_name" => "{{ plugin }}",
                "file_title" => "Translation for {{ plugin }} plugin",
                "translation_folder" => $translationFolder,
                "translation_prefix" => "{{ name }}",
                "file_name" => "messages.php",
                "master_translation_file" => realpath(__DIR__.'/../resources/lang/en/messages.php'),
            ];
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Register views path
        $this->loadViewsFrom(__DIR__.'/../resources/views', '{{ name }}');

        // Register routes file
        $this->loadRoutesFrom(__DIR__.'/../routes.php');

        // Publish plugin assets, remember to tag them with 'plugin' tag
        // Acelle shall execute the following command after installing any plugin:
        //
        //     php artisan vendor:publish --tag=plugin --force
        //
        $this->publishes([
            // any
        ], 'plugin');
    }
}
