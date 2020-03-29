<?php
namespace CovidTrack\Application\Bootstrappers\Http\Views;

use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Views\Factories\IViewFactory;
use Opulence\Views\IView;
use CovidTrack\Application\Http\Views\Builders\HomeBuilder;
use CovidTrack\Application\Http\Views\Builders\HtmlErrorBuilder;
use CovidTrack\Application\Http\Views\Builders\MasterBuilder;

/**
 * Defines the view builders bootstrapper
 */
class BuildersBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $viewFactory = $container->resolve(IViewFactory::class);

        $viewFactory->registerBuilder('Master', function (IView $view) {
            return (new MasterBuilder())->build($view);
        });
        $viewFactory->registerBuilder('Home', function (IView $view) {
            return (new HomeBuilder())->build($view);
        });
        $viewFactory->registerBuilder('errors/html/Error', function (IView $view) {
            return (new HtmlErrorBuilder())->build($view);
        });
    }
}
