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
use App\Model\BZFlag\PublicSchema\UsersModel;
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
            // TODO: Error message?
            return $response->withJson([], 404);
        }
    }

    public function getOrganizationsByBZID(Request $request, Response $response, array $args)
    {
        // Fetch the user from the legacy database by bzid
        $user = $this->legacydb->getUserByBZID($args['bzid']);

        // If the user doesn't exist or isn't active, bail out
        if (!$user) {
            // TODO: Error message?
            return $response->withJson([], 404);
        }

        // Get or create the glue record
        $user_glue = $this->db
            ->getModel(UsersModel::class)
            ->findByBZID($user['bzid'])
        ;

        // This shouldn't fail
        if (!$user_glue) {
            // TODO: Error message?
            return $response->withJson([], 500);
        }

        // Retrieve a list of all organizations that the logged in user is associated with
        $organizations = $this->db
            ->getModel(OrganizationsModel::class)
            ->findByOrganizationMember($user_glue['id'])
            ->extract()
        ;

        // Build our dataset
        $data = [];
        foreach ($organizations as &$organization) {
            $data[] = [
                'short_name' => $organization['short_name'],
                'display_name' => $organization['display_name'],
                'is_founder' => ($user_glue['id'] === $organization['founder']),
                'is_hosting_admin' => $organization['hosting_admin'],
                'is_group_admin' => $organization['group_admin'],
                'is_group_manager' => $organization['group_manager']
            ];
        }

        return $response->withJson($data);
    }
}
