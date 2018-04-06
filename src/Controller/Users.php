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

use App\Model\BZFlag\PublicSchema\OrganizationsModel;
use Slim\Http\Request;
use Slim\Http\Response;

class Users extends Controller
{
    public function getByBZID(Request $request, Response $response, array $args)
    {
        // TODO: Provide access to the email address for private API use?

        $user = $this->legacydb->getUserByBZID($args['bzid']);

        // Return the user if we have it, or a blank result with a 404 if we don't
        if ($user) {
            return $response->withJson([
                'username' => $user['username']
            ]);
        } else {
            return $response->withJson([], 404);
        }
    }

    public function getOrganizationsByBZID(Request $request, Response $response, array $args)
    {
        // Retrieve a list of all organizations that the logged in user is associated with
        $organizations = $this->db
            ->getModel(OrganizationsModel::class)
            ->findByOrganizationMember($args['bzid'])
            ->extract()
        ;

        foreach ($organizations as &$organization) {
            $isFounder = ($args['bzid'] === $organization['founder']);
            unset($organization['founder']);
            $organization['founder'] = $isFounder;
        }

        return $response->withJson($organizations);
    }
}
