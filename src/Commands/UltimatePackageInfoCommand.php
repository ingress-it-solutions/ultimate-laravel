<?php

namespace Ultimate\Laravel\Commands;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Ultimate\Laravel\UltimateServiceProvider;

class UltimatePackageInfoCommand extends Command
{
    protected $hidden = true;

    protected $signature = 'ultimate:package-info';

    protected $description = 'Send package info to palzin.app';

    protected $key;

    protected $enabled;

    protected $guzzle;

    protected $env_file;

    public $route;

    public function __construct()
    {
        parent::__construct();

        $this->key = config('ultimate.key');

        $this->enabled = config('ultimate.package_enable');

        $this->route = (version_compare(app()->version(), '5.6.12') >= 0) ? \Illuminate\Support\Facades\URL::signedRoute('version-info') : route('version-info');

        $this->guzzle = new Client([
            'base_uri'    =>  config('ultimate.url') . '/api/1/package-info/store',
            'http_errors' => false,
            'verify'      => false,
            'headers' 	  => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Ultimate-Key' => $this->key,
                'X-Ultimate-Version' => UltimateServiceProvider::VERSION,
            ],
        ]);
    }

    public function handle()
    {
        if ($this->enabled && $this->key && $this->key !== '') {
            $this->guzzle->post('heartbeat', [
                'json' => [
                    'env.example'     => $this->getEnvironmentVariableNames('.env.example'),
                    'env'             => $this->getEnvironmentVariableNames('.env'),
                    'composer.json'   => $this->getComposer(),
                    'composer.lock'   => $this->getComposerLock(),
                    'laravel_version' => app()->version(),
                    'php_version' 	  => $this->getPhpVersion(),
                    'php_version_cli' => phpversion(),
                    'environment' 	  => $_ENV['APP_ENV'],
                    'debug' 	  	  => $_ENV['APP_DEBUG'],
                    'package.json'	  => $this->getPackage(),
                    'package.lock'	  => $this->getPackageLock(),
                ],
            ]);
        }
    }

    public function getPhpVersion()
    {
        $response = $this->guzzle->get($this->route);

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents());
        }
    }

    public function getEnvironmentVariableNames($file)
    {
        if (! is_file(base_path($file))) {
            return;
        }

        $this->env_file = $file;

        foreach ([5, 4, 3, 2] as $function) {
            $dotenv = $this->{'dotenv_' . $function}();

            if ($dotenv) {
                return $dotenv;
            }
        }
    }

    public function getPackageDetail($list)
    {
        $data = [];

        foreach($list as $name => $version) {
            if(\Composer\InstalledVersions::isInstalled($name)) {
                $data[$name] =  \Composer\InstalledVersions::getPrettyVersion($name);

            } else {
                $data[$name] = $version;
            }
        }
        return $data;
    }

    public function getComposer()
    {
        $data = $this->readFileContents('composer.json');

        return [
            'require' 	   => ($this->getPackageDetail($data['require']) ?? null),
            'require-dev'  => ($this->getPackageDetail($data['require-dev']) ?? null),
            'repositories' => ($data['repositories'] ?? null),
        ];
    }

    public function getComposerLock()
    {
        $data = $this->readFileContents('composer.lock');

        $packages = collect(($data['packages'] ?? []))->mapWithKeys(function ($package) {
            return [$package['name'] => $package['version']];
        })->toArray();

        $packages_dev = collect(($data['packages-dev'] ?? []))->mapWithKeys(function ($package) {
            return [$package['name'] => $package['version']];
        })->toArray();

        return [
            'packages' 	   => $packages,
            'packages-dev' => $packages_dev,
        ];
    }

    public function getPackage()
    {
        $data = $this->readFileContents('package.json');

        return [
            'require'     => ($data['dependencies'] ?? null),
            'require-dev' => ($data['devDependencies'] ?? null),
        ];
    }

    public function getPackageLock()
    {
        $data = $this->readFileContents('package-lock.json');

        $packages = collect(($data['packages'] ?? ($data['dependencies'] ?? [])))->mapWithKeys(function ($package, $key) {
            if ($key === '') {
                return [];
            }

            return [str_replace('node_modules/', '', $key) => $package['version']];
        })->toArray();

        return [
            'packages' => ($packages ?? null),
        ];
    }

    public function readFileContents($file)
    {
        if (! is_file(base_path($file))) {
            return;
        }

        return json_decode(file_get_contents(base_path($file)), true);
    }

    protected function dotenv_2()
    {
        try {
            $dotenv = new Dotenv(base_path(), $this->env_file);
            $dotenv->load();

            return $dotenv->getEnvironmentVariableNames();
        } catch (\TypeError $error) {
            return;
        } catch (\Error $error) {
            return;
        }
    }

    protected function dotenv_3()
    {
        try {
            $dotenv = Dotenv::create(base_path(), $this->env_file);
            $dotenv->load();

            return $dotenv->getEnvironmentVariableNames();
        } catch (\TypeError $error) {
            return;
        } catch (\Error $error) {
            return;
        }
    }

    protected function dotenv_4()
    {
        try {
            $repository = \Dotenv\Repository\RepositoryBuilder::create()->withReaders([
                new \Dotenv\Repository\Adapter\EnvConstAdapter(),
                new \Dotenv\Repository\Adapter\ServerConstAdapter(),
            ])->make();

            $dotenv = Dotenv::create($repository, base_path(), $this->env_file);

            return array_keys($dotenv->load());
        } catch (\TypeError $error) {
            return;
        } catch (\Error $error) {
            return;
        }
    }

    protected function dotenv_5()
    {
        try {
            $repository = \Dotenv\Repository\RepositoryBuilder::createWithDefaultAdapters()->make();
            $dotenv     = Dotenv::create($repository, base_path(), $this->env_file);

            return array_keys($dotenv->load());
        } catch (\TypeError $error) {
            return;
        } catch (\Error $error) {
            return;
        }
    }
}