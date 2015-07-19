<?php

function kill_that_archive_html_plugin_cache() {
    File::open(CACHE . DS . 'plugin.archive.cache')->delete();
}

$hooks = array(
    // Delete archive HTML cache on article, page and comment update
    'on_article_update',
    'on_page_update',
    'on_comment_update',
    // Delete archive HTML cache on plugin eject and destruct
    'on_plugin_' . md5(File::B(__DIR__)) . '_eject',
    'on_plugin_' . md5(File::B(__DIR__)) . '_destruct'
);

foreach($hooks as $hook) {
    Weapon::add($hook, 'kill_that_archive_html_plugin_cache');
}


/**
 * Plugin Updater
 * --------------
 */

Route::accept($config->manager->slug . '/plugin/' . File::B(__DIR__) . '/update', function() use($config, $speak) {
    if($request = Request::post()) {
        Guardian::checkToken($request['token']);
        File::write($request['slug'])->saveTo(PLUGIN . DS . File::B(__DIR__) . DS . 'states' . DS . 'slug.txt');
        Notify::success(Config::speak('notify_success_updated', $speak->plugin));
        Guardian::kick(File::D($config->url_current));
    }
});