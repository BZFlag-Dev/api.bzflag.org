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

$container = $app->getContainer();

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Pomm
$container['db'] = function ($c) {
    $settings = $c->get('settings')['database'];
    $dsn = sprintf('pgsql://%s:%s@%s/%s', $settings['username'], $settings['password'], $settings['host'], $settings['database']);
    return (new \PommProject\Foundation\Pomm(['bzflag' =>
        [
            'dsn' => $dsn,
            'class:session_builder' => '\App\Model\MySessionBuilder',
        ]
    ]))['bzflag'];
};

// Legacy MySQL database
$container['legacydb'] = function ($c) {
    $settings = $c->get('settings')['legacy_database'];

    return new App\Model\LegacyDatabase($settings);
};
