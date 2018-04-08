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
use Particle\Validator\Validator;
use Particle\Validator\Exception\InvalidValueException;

class Organizations extends Controller
{
    public function create(Request $request, Response $response, array $args)
    {
        $validator = new Validator;

        $user_id = null;

        // Establish rules
        $validator->required('founder', 'Founder')
            ->alnum()
            ->callback(function ($bzid) use (&$user_id) {
                $user_id = $this->getUserIDFromBZID($bzid);

                if ($user_id === null) {
                    throw new InvalidValueException('Specified founder does not exist.', 'founder');
                }

                // Otherwise, we're good to go!
                return true;
            })
        ;
        $validator->required('short_name', 'Short name')
            ->lengthBetween(2, 32)
            ->alnum()
            ->callback(function ($short_name) {
                // Check if the organization already exists
                $organization = $this->db
                    ->getModel(OrganizationsModel::class)
                    ->findWhere('short_name ~* $*', compact('short_name'))
                ;

                // If an organization with this short name exists, throw an exception
                if ($organization->count() > 0) {
                    throw new InvalidValueException('An organization already exists with this short name. Please pick a different name.', 'short_name');
                }

                // Otherwise we are good to go!
                return true;
            })
        ;
        $validator->required('display_name', 'Display name')
            ->lengthBetween(2, 64)
            ->alnum(true)
        ;

        // Grab data
        $data = $request->getParsedBody();

        // If the data didn't parse, throw a 400
        // TODO: Add error message
        if (!$data) {
            return $response->withJson([], 400);
        }

        // Validate form data against rules
        $result = $validator->validate($data);

        // Did all the rules pass validation?
        if ($result->isValid()) {
            // Try to add it to the DB
            $organization = $this->db
                ->getModel(OrganizationsModel::class)
                ->createAndSave([
                    'founder' => $user_id,
                    'short_name' => $data['short_name'],
                    'display_name' => $data['display_name']
                ])
            ;

            // If the organization was created, redirect
            if ($organization) {
                return $response->withRedirect(
                    $this->router->pathFor('organizations:get', [
                        'orgshortname' => $organization['short_name']
                    ])
                );
            } else {
                return $response->withJson([
                    'errors' => ['There was an error creating the organization.']
                ], 500);
            }
        } else {
            // Get the errors
            $errors = $result->getMessages();

            // TODO: Format the errors
            return $response->withJson($errors, 400);
        }
    }

    public function get(Request $request, Response $response, array $args)
    {
        // Retrieve a specific organization by the short name
        $organization = $this->db
            ->getModel(OrganizationsModel::class)
            ->findWhere('short_name ~* $*', ['short_name' => $args['orgshortname']])
            ->extract()
        ;

        // If we have an organization, return it
        if ($organization) {
            return $response->withJson($organization);
        } else {
            // TODO: Add error message
            return $response->withJson([], 404);
        }
    }

    public function update(Request $request, Response $response, array $args)
    {
    }

    public function delete(Request $request, Response $response, array $args)
    {
    }
}
