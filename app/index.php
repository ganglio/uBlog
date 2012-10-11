<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Loader\YamlFileLoader;

$app['debug'] = TRUE;
$app['all_locales'] = getLocales();

$checkBefore = (function() use($app) {
	if (!$app["session"]->get("user") && "/admin/login" != $app["request"]->getRequestURI() ) {
		return $app->redirect($app["url_generator"]->generate("login"));
	}
});

/**
 * Before action
 */
$app->before(function () use ($app) {

	// Registering Twig
	$app->register(new Silex\Provider\TwigServiceProvider(), array(
		'twig.path'         => __DIR__.'/views',
		'twig.class_path'   => __DIR__.'../vendor/twig/lib',
		// 'twig.options'      => array('cache' => __DIR__ .'/cache'),
	));

	// Registering Translation
	$app->register(new Silex\Provider\TranslationServiceProvider(), array(
		'locale'                    => $app['locale'], // Current locale
		'locale_fallback'           => 'en', // Default locale
		'translation.class_path'    => __DIR__.'/../vendor/symfony/src',
	));

	$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
		$translator->addLoader('yaml', new YamlFileLoader());

		foreach ($app["all_locales"] as $locale)
			$translator->addResource('yaml', __DIR__.'/locales/'.$locale.'.yml', $locale);

		return $translator;
	}));

	$app->register(new Silex\Provider\SessionServiceProvider());
	$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
	$app->register(new Silex\Provider\FormServiceProvider());
	$app->register(new Silex\Provider\ValidatorServiceProvider());
	$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__."/config.yml"));

	$app->register(new uBlog\Providers\UsersProvider());
	$app->register(new Silex\Provider\DoctrineServiceProvider());
	$app->register(new uBlog\Providers\ORMProvider());

	$app["twig"]->addExtension(new Misd\TwigMarkdowner\Twig\Extension\MarkdownerExtension(new \dflydev\markdown\MarkdownParser()));

});

// 404
$app->error(function (\Exception $e, $code) use ($app) {
	if (404 == $code)
		return $app->redirect("/");
});

// Mounting the various controllers
$app->mount("/", include 'controllers/home.php');
$app->mount("/admin", include 'controllers/admin.php');
$app->mount("/widgets", include 'controllers/widgets.php');

// Run
$app->run();

// Helpers
function getLocales(){
	if ($dh = opendir(__DIR__."/locales")) {
		while ($fname = readdir($dh))
			if (preg_match("/\.yml/",$fname)) {
				$fname = explode(".",$fname);
				$out[]=$fname[0];
			}
	}
	sort($out);
	return $out;
};
