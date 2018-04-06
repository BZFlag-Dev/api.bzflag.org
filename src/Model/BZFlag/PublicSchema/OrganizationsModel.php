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

namespace App\Model\BZFlag\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;


use App\Model\BZFlag\PublicSchema\AutoStructure\Organizations as OrganizationsStructure;

/**
 * OrganizationsModel
 *
 * Model class for table organizations.
 *
 * @see Model
 */
class OrganizationsModel extends Model
{
    use WriteQueries;

    /**
     * __construct()
     *
     * Model constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->structure = new OrganizationsStructure;
        $this->flexible_entity_class = '\App\Model\BZFlag\PublicSchema\Organizations';
    }

    public function findByOrganizationMember($user_id)
    {
        $users_organizations_model = $this
            ->getSession()
            ->getModel(UsersOrganizationsModel::class)
        ;

        $sql = <<<SQL
SELECT
    id, founder, short_name, display_name, false as owner, false as hosting_admin, false as group_admin, false as group_manager
FROM
    {organizations}
WHERE
    founder = $*
UNION
SELECT 
    {projection}
FROM
    {organizations} org
    INNER JOIN {users_organizations} uo on org.id = uo.organization
WHERE
    uo.user = $*
SQL;

        $projection = $this->createProjection()
            ->setField('owner', 'uo.owner', 'bool')
            ->setField('hosting_admin', 'uo.hosting_admin', 'bool')
            ->setField('group_admin', 'uo.group_admin', 'bool')
            ->setField('group_manager', 'uo.group_manager', 'bool')
        ;

        $sql = strtr(
            $sql,
            [
                '{organizations}' => $this->structure->getRelation(),
                '{users_organizations}' => $users_organizations_model->getStructure()->getRelation(),
                '{projection}' => $projection->formatFields('org'),
            ]
        );

        return $this->query($sql, [$user_id, $user_id], $projection);
    }
}
