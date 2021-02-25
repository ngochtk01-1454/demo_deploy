<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'demo_deploy_app');

// Project repository
set('repository', 'git@github.com:ngochtk01-1454/demo_deploy.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false); 
set('writable_mode', 'chmod');
set('http_user', 'deploy');
// set('writable_use_sudo', true);

// Shared files/dirs between deploys 
add('shared_files', [
    '.env'
]);
add('shared_dirs', [
    'storage'
]);

// Writable dirs by web server 
add('writable_dirs', [
    'storage',
]);


// Hosts

host('143.110.209.111')
    ->user('deploy')
    ->stage('development')
    ->set('deploy_path', '~/{{application}}')
    ->forwardAgent(false); 
    
// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

desc('Reload php-fpm');
task('reload:php-fpm', function () {
    run('sudo /usr/sbin/service php8.0-fpm reload');
});

/**
 * Main task
 */
desc('Deploy project');
task('deployer', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:cache:clear',
    'artisan:config:cache',
    'artisan:optimize',
    'deploy:symlink',
    // 'reload:php-fpm',
    'deploy:unlock',
    'cleanup',
]);
// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

// before('deploy:symlink');

