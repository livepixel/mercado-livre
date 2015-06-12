<?php namespace Livepixel\MercadoLivre;

use Illuminate\Support\ServiceProvider;
use Livepixel\MercadoLivre\Meli;

class MeliServiceProvider extends ServiceProvider
{

	protected $client_id;
	protected $client_secret;
	protected $urls;
	protected $curl_opts;

	public function boot()
	{
		$this->publishes([__DIR__.'/config/mercadolivre.php' => config_path('mercadolivre.php')]);

		$this->client_id     = config('mercadolivre.client_id');
		$this->client_secret = config('mercadolivre.client_secret');
		$this->urls          = config('mercadolivre.urls');
		$this->curl_opts     = config('mercadolivre.curl_opts');

	}

	public function register()
	{
		$this->app->singleton('meli', function(){
			return new Meli($this->client_id, $this->client_secret, $this->urls, $this->curl_opts);
		});
	}

}