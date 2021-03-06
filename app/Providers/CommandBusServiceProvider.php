<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use League\Tactician\CommandBus;
use League\Tactician\Container\ContainerLocator;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor;
use League\Tactician\Handler\Locator\HandlerLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use League\Tactician\Handler\MethodNameInflector\MethodNameInflector;
use League\Tactician\Logger\Formatter\ClassNameFormatter;
use League\Tactician\Logger\Formatter\Formatter;
use League\Tactician\Logger\LoggerMiddleware;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use TheRestartProject\RepairDirectory\Application\CommandBus\LaravelContainerAdapter;
use TheRestartProject\RepairDirectory\Application\CommandBus\Middleware\TransactionMiddleware;
use TheRestartProject\RepairDirectory\Tactician\Validator;
use TheRestartProject\RepairDirectory\Tactician\Authorizer;

class CommandBusServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->setupMiddleware();
        $this->setupCommandBus();
    }

    /**
     * Sets up the Command Bus Handler middleware
     *
     * This middleware allows handlers to be fetched from the container instead of
     * created in memory. Makes things faster.
     *
     * @link http://tactician.thephpleague.com/plugins/container/
     */
    public function handlerMiddleware()
    {
        $this->app->singleton(HandlerLocator::class, function ($app) {
            return new ContainerLocator(
                new LaravelContainerAdapter($app),
                app('config')->get('tactician.handlers')
            );
        });

        $this->app->singleton(CommandNameExtractor::class, ClassNameExtractor::class);
        $this->app->singleton(MethodNameInflector::class, HandleInflector::class);
        $this->app->singleton(CommandHandlerMiddleware::class, CommandHandlerMiddleware::class);
    }


    /**
     * Sets up the Command Bus Validator middleware
     *
     * This middleware allows Validators to be fetched from the container instead of
     * created in memory. Makes things faster.
     *
     * @link http://tactician.thephpleague.com/plugins/container/
     */
    public function validatorMiddleware()
    {
        $this->app->singleton(Validator\Locators\ValidatorLocator::class, function ($app) {
            return new Validator\Locators\ContainerLocator(
                new LaravelContainerAdapter($app),
                app('config')->get('tactician.validators')
            );
        });

        $this->app->singleton(
            Validator\Extractors\CommandNameExtractor::class,
            Validator\Extractors\ClassNameExtractor::class
        );

        $this->app->singleton(
            Validator\Inflectors\MethodNameInflector::class,
            Validator\Inflectors\ValidateInflector::class
        );

        $this->app->singleton(
            Validator\CommandValidatorMiddleware::class,
            Validator\CommandValidatorMiddleware::class
        );
    }



    /**
     * Sets up the Command Bus Authorizer middleware
     *
     * This middleware allows authorizers to be fetched from the container instead of
     * created in memory. Makes things faster.
     *
     * @link http://tactician.thephpleague.com/plugins/container/
     */
    public function authorizerMiddleware()
    {
        $this->app->singleton(
            Authorizer\Locators\AuthorizerLocator::class,
            function ($app) {
                return new Authorizer\Locators\ContainerLocator(
                    new LaravelContainerAdapter($app),
                    app('config')->get('tactician.authorizers')
                );
            }
        );

        $this->app->singleton(
            Authorizer\Extractors\CommandNameExtractor::class,
            Authorizer\Extractors\ClassNameExtractor::class
        );

        $this->app->singleton(
            Authorizer\Inflectors\MethodNameInflector::class,
            Authorizer\Inflectors\AuthorizeInflector::class
        );

        $this->app->singleton(
            Authorizer\CommandAuthorizerMiddleware::class,
            Authorizer\CommandAuthorizerMiddleware::class
        );
    }

    /**
     * Sets up LoggerMiddleware for Command Bus
     *
     * This logs all commands that pass through the CommandBus and records their
     * success.
     *
     * @link http://tactician.thephpleague.com/plugins/logger/
     */
    public function loggerMiddleware()
    {
        $this->app->singleton(Formatter::class, ClassNameFormatter::class);
        $this->app->singleton(LoggerMiddleware::class, LoggerMiddleware::class);
    }

    /**
     * Sets up the Command Bus Middleware for the Doctrine Orm in the container
     *
     * Ensures that the EntityManager is flushed after each command is handled.
     *
     * @link http://tactician.thephpleague.com/plugins/doctrine/
     */
    public function doctrineMiddleware()
    {
        $this->app->singleton(TransactionMiddleware::class, TransactionMiddleware::class);
    }

    /**
     * Sets up the Command Bus in the container
     *
     * @link http://tactician.thephpleague.com/
     */
    public function setupCommandBus()
    {
        $this->app->singleton(CommandBus::class, function (Application $app) {
            $middleware = $app->make('config')->get('tactician.middleware');

            $middlewareCollection = collect($middleware)->map(function ($className) use ($app) {
                return $app->make($className);
            });

            return new CommandBus($middlewareCollection->toArray());
        });
    }

    /**
     * Sets up the middleware needed for the CommandBus
     */
    public function setupMiddleware()
    {
        $this->handlerMiddleware();
        $this->validatorMiddleware();
        $this->authorizerMiddleware();
        $this->loggerMiddleware();
        $this->doctrineMiddleware();
    }
}
