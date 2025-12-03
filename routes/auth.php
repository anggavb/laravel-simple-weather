<?php
use Lorisleiva\Actions\Facades\Actions;

Actions::registerRoutes([
    'App\Actions\Register',
    'App\Actions\Login',
    'App\Actions\Password',
    'App\Actions\Email',
]);
