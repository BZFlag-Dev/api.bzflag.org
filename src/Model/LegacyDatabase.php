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

class LegacyDatabase
{
    private $link;

    public function __construct($settings)
    {
        $this->link = new \mysqli($settings['host'], $settings['username'], $settings['password'], $settings['database']);

        /*if ($this->link->connect_error) {

        }*/

        $this->link->query("SET NAMES 'utf8'");
    }

    public function getUserByBZID($bzid)
    {
        // Prepare the query
        $statement = $this->link->prepare('SELECT user_id as bzid, username, user_email as email FROM bzbb3_users WHERE user_inactive_reason = 0 AND user_type <> 2 AND user_id = ?');

        // Bind the BZID
        $statement->bind_param('s', $bzid);

        // Execute the statement
        $statement->execute();

        // Try to fetch a row
        $result = $statement->get_result();
        $row = $result->fetch_assoc();

        return $row;
    }

    public function getUserByUsername($username)
    {
        // Prepare the query
        $statement = $this->link->prepare('SELECT user_id as bzid, username, user_email as email FROM bzbb3_users WHERE user_inactive_reason = 0 AND user_type <> 2 AND username_clean = ?');

        // Bind the BZID
        // TODO: Clean up the value like phpBB3 does so this actually works
        $statement->bind_param('s', $username);

        // Execute the statement
        $statement->execute();

        // Try to fetch a row
        $result = $statement->get_result();
        $row = $result->fetch_assoc();

        return $row;
    }

    public function getUsersByUsername($username)
    {
        // Prepare the query
        $statement = $this->link->prepare('SELECT user_id as bzid, username, user_email as email FROM bzbb3_users WHERE user_inactive_reason = 0 AND user_type <> 2 AND username_clean LIKE ? ORDER BY LENGTH(username_clean), username_clean LIMIT 100');

        $username = "%{$username}%";

        // Bind the BZID
        // TODO: Clean up the value like phpBB3 does so this actually works
        $statement->bind_param('s', $username);

        // Execute the statement
        $statement->execute();

        // Try to fetch a row
        $result = $statement->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }
}
