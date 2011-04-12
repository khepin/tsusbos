<?php
namespace Khepin;

use Silex\ExtensionInterface;
use Silex\Application;
/**
 * Description of newPHPClass
 *
 * @author sebastien.armand
 */
class ShortenerExtension implements ExtensionInterface {

    public function register(Application $app){
        $app['shortener'] = $app->share(function() use($app){
            return new UrlShortener($app['url_file_name']);
        });
    }
}
?>
