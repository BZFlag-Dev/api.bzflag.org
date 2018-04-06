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

namespace App\Model;

use \PommProject\Foundation\Converter\PgString;
use \PommProject\Foundation\Session\Session;
use \PommProject\ModelManager\SessionBuilder;

class MySessionBuilder extends SessionBuilder
{
    public function postConfigure(Session $session)
    {
        parent::postConfigure($session);

        $session
            ->getPoolerForType('converter')
            ->getConverterHolder()
            ->registerConverter('group_visibility', new PgString(), ['public.group_visibility'])
        ;
    }
}
