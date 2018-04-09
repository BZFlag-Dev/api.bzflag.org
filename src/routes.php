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

// Routes

// API v1
$app->group('/v1dev', function () {
    // Users

    // Search for one or more users by name
    $this->get('/users', 'App\Controller\Users:search');

    // Get information about a user by BZID
    $this->get('/users/{bzid}', 'App\Controller\Users:getByBZID');

    // Get organization membership information for a specific user by BZID
    $this->get('/users/{bzid}/organizations', 'App\Controller\Users:getOrganizationsByBZID');



    $this->group('/organizations', function () {
        // Organizations

        // Create a new organization
        $this->post('', 'App\Controller\Organizations:create');

        // Get information about an organization
        $this->get('/{orgshortname}', 'App\Controller\Organizations:get')->setName('organizations:get');

        // Update information about an organization
        $this->put('/{orgshortname}', 'App\Controller\Organizations:update');

        // Delete an organization
        $this->delete('/{orgshortname}', 'App\Controller\Organizations:delete');


        // Organization Membership

        // Add a user to an organization
        $this->post('{orgshortname}/members', 'App\Controller\OrganizationMembers:create');

        // Get members of an organization
        $this->get('{orgshortname}/members', 'App\Controller\OrganizationMembers:get')->setName('organization_members:get');

        // Update a user's membership to an organization
        $this->put('{orgshortname}/members/{bzid}', 'App\Controller\OrganizationMembers:update');

        // Remove a user from an organization
        $this->delete('{orgshortname}/members/{bzid}', 'App\Controller\OrganizationMembers:delete');


        // Groups

        // Create a new group within an organization
        $this->post('{orgshortname}/groups', 'App\Controller\Groups:create');

        // Get information about an organization's groups
        $this->get('{orgshortname}/groups', 'App\Controller\Groups:getByOrg');

        // Get information about a specific group
        // TODO: Include members?
        $this->get('{orgshortname}/groups/{groupshortname}', 'App\Controller\Groups:get');

        // Update information about a group
        $this->put('{orgshortname}/groups/{groupshortname}', 'App\Controller\Groups:update');

        // Delete a group
        $this->delete('{orgshortname}/groups/{groupshortname}', 'App\Controller\Groups:delete');


        // Group Members

        // Add a user to a group
        $this->post('{orgshortname}/groups/{groupshortname}/members', 'App\Controller\GroupMembers:create');

        // Get the members of a group
        $this->get('{orgshortname}/groups/{groupshortname}/members', 'App\Controller\GroupMembers:getByGroup');

        // Get a member of a group
        // TODO: Is this necessary?
        $this->get('{orgshortname}/groups/{groupshortname}/members/{bzid}', 'App\Controller\GroupMembers:get');

        // Update a member of a group
        $this->put('{orgshortname}/groups/{groupshortname}/members/{bzid}', 'App\Controller\GroupMembers:update');

        // Remove a user from a group
        $this->delete('{orgshortname}/groups/{groupshortname}/members/{bzid}', 'App\Controller\GroupMembers:delete');


        // Hosting Keys

        // Create a hosting key
        $this->post('{orgshortname}/hostingkeys', 'App\Controller\HostingKeys:create');

        // Get hosting keys for an organization
        $this->get('{orgshortname}/hostingkeys', 'App\Controller\HostingKeys:getByOrg');

        // Get hosting key
        $this->get('{orgshortname}/hostingkeys/{id}', 'App\Controller\HostingKeys:get');

        // Delete a hosting key
        $this->delete('{orgshortname}/hostingkeys/{id}', 'App\Controller\HostingKeys:delete');
    });
});
