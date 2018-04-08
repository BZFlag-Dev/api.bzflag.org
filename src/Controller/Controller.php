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

namespace App\Controller;

use App\Model\BZFlag\PublicSchema\UsersModel;
use Monolog\Logger;
use PommProject\ModelManager\Session;
use Slim\Container;

/**
 * @property Logger $logger
 * @property Session $db
 */
class Controller
{
    private $container;

    public function __construct(Container $c)
    {
        $this->container = $c;
    }

    // Allow constructor methods to easily access properties from the container
    public function &__get(string $name)
    {
        return $this->container->$name;
    }

    // Retrieve the internal user glue ID from a legacy BZID
    public function getUserIDFromBZID($bzid)
    {
        // Fetch the user from the legacy database by bzid
        $user = $this->legacydb->getUserByBZID($bzid);

        // If the user doesn't exist or isn't active, bail out
        if (!$user) {
            return null;
        }

        // Get or create the glue record
        $user_glue = $this->db
            ->getModel(UsersModel::class)
            ->findByBZID($user['bzid'])
        ;

        // This shouldn't fail
        if (!$user_glue) {
            return null;
        }

        return $user_glue['id'];
    }
}
