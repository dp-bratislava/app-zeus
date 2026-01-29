<?php

namespace Deployer;

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

require 'recipe/laravel.php';
require 'recipe/deploy/push.php';

$dotenv = new Dotenv;
$dotenv->load(__DIR__ . '/.env');

// Global Config
set('repository', 'git@github.com:dp-bratislava/app-zeus.git');
set('php_fpm_version', '8.2');
set('keep_releases', 3);
set('nvm', 'source $HOME/.nvm/nvm.sh &&');
set('composer_options', '--ignore-platform-reqs');
set('bin/php', '/usr/bin/php8.2');
set('bin/composer', '/usr/bin/php8.2 /usr/local/bin/composer');

// Hosts
host('testing')
    ->setHostname(env('DEPLOYER_TESTING_HOSTNAME'))
    ->setRemoteUser(env('DEPLOYER_TESTING_REMOTE_USER'))
    ->setDeployPath(env('DEPLOYER_TESTING_DEPLOY_PATH'))
    ->set('branch', env('DEPLOYER_TESTING_BRANCH'))
    ->set('use_nvm', true);

host('staging')
    ->setHostname(env('DEPLOYER_STAGING_HOSTNAME'))
    ->setRemoteUser(env('DEPLOYER_STAGING_REMOTE_USER'))
    ->setDeployPath(env('DEPLOYER_STAGING_DEPLOY_PATH'))
    ->set('branch', env('DEPLOYER_STAGING_BRANCH'))
    ->set('use_nvm', true);

host('production')
    ->setHostname(env('DEPLOYER_PRODUCTION_HOSTNAME'))
    ->setRemoteUser(env('DEPLOYER_PRODUCTION_REMOTE_USER'))
    ->setDeployPath(env('DEPLOYER_PRODUCTION_DEPLOY_PATH'))
    ->set('branch', env('DEPLOYER_PRODUCTION_BRANCH'))
    ->set('use_nvm', false);

// Tasks
task('build', function () {
    $useNvm = get('use_nvm');
    if ($useNvm) {
        run('cd {{release_path}} && {{nvm}} npm install && {{nvm}} npm run build');
    } else {
        run('cd {{release_path}} && npm install && npm run build');
    }
});

// Disable caching for Blade components temporarily to avoid issues during deployment
task('artisan:view:cache', function () {
    run('{{bin/php}} {{release_or_current_path}}/artisan view:clear');
});

// Hooks
after('deploy:failed', 'deploy:unlock');
after('deploy:symlink', 'build');
after('deploy', 'artisan:cache:clear');

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'artisan:storage:link',
    'artisan:config:cache',
    'artisan:route:cache',
    'artisan:view:cache',
    'artisan:event:cache',
    'artisan:migrate',
    'deploy:publish',
]);