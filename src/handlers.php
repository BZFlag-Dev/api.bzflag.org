<?php declare(strict_types=1);
/*
 * BZFlag Player Portal provides an interface for managing BZFlag
 * organizations, groups, and hosting keys.
 * Copyright (C) 2018  BZFlag & Associates
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

use Slim\Http\Request;
use Slim\Http\Response;

// Various Slim errors
$container['errorHandler'] = function ($c) {
    return function (Request $request, Response $response, $exception) use ($c) {
        return $c['response']
            ->withJson([
                'errors' => ['Something bad happened.']
            ], 500)
        ;
    };
};

// PHP errors
$container['phpErrorHandler'] = function ($c) {
    return function (Request $request, Response $response, $error) use ($c) {
        return $c['response']
            ->withJson([
                'errors' => ['Something bad happened.']
            ], 500)
            ;
    };
};

// 405 Method Not Allowed
$container['notAllowedHandler'] = function ($c) {
    return function (Request $request, Response $response, $methods) use ($c) {
        return $c['response']
            ->withHeader('Allow', implode(', ', $methods))
            ->withJson([
                'errors' => [
                    'Method must be one of: ' . implose(', ', $methods)
                ]
            ], 405)
        ;
    };
};

// 404 Page Not Found
$container['notFoundHandler'] = function ($c) {
    return function (Request $request, Response $response) use ($c) {
        return $c['response']
            ->withJson([
                'errors' => ['Page not found']
            ], 404)
        ;
    };
};
