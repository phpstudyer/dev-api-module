<?php
namespace Ly;
use Illuminate\Support\ServiceProvider;

class LyServiceProvider extends ServiceProvider
{

	protected $defer = false;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    	//加载模板
//	    $this->loadViewsFrom(realpath(__DIR__.'/../views'), 'contact');
	    //注册路由
	    $this->loadRoutesFrom(__DIR__.'/routes.php');
	    //注册迁移文件
		$this->loadMigrationsFrom(__DIR__.'/migrations');
	    //配置文件
	    $this->publishes([
		    __DIR__.'/config/contact.php' => config_path('contact.php'),
	    ]);

	    //载入文件
	    require __DIR__.'/helper.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
