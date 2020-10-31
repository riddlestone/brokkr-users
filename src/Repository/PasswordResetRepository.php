<?php

namespace Riddlestone\Brokkr\Users\Repository;

use Doctrine\ORM\EntityRepository;
use Riddlestone\Brokkr\Users\Entity\PasswordReset;

/**
 * Class PasswordResetRepository
 *
 * @package Riddlestone\Brokkr\Users
 * @method PasswordReset|null find($id, int $lockMode = null, int $lockVersion = null)
 */
class PasswordResetRepository extends EntityRepository
{

}
