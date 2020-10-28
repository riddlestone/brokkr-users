<?php

namespace Riddlestone\Brokkr\Users\Acl;

use Laminas\Permissions\Acl\Role\RoleInterface;
use Riddlestone\Brokkr\Acl\PluginManager\RoleRelationshipProviderInterface;
use Riddlestone\Brokkr\Users\Entity\User;

class RoleRelationshipFactory implements RoleRelationshipProviderInterface
{
    /**
     * @param RoleInterface $role
     * @return string[]
     */
    public function getRoleParents(RoleInterface $role): array
    {
        if ($role instanceof User) {
            return [User::class];
        }
        return [];
    }
}
