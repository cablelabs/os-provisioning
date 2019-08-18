<?php

namespace Acme\html;

use Collective\Html\HtmlServiceProvider as CollectiveHtmlServiceProvider;

class HtmlServiceProvider extends CollectiveHtmlServiceProvider
{
    /**
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder()
    {
        $this->app->singleton('form', function ($app) {
            $form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->token());

            return $form->setSessionStore($app['session.store']);
        });
    }
}
