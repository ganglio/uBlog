<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace uBlog\Constraints;

use Symfony\Component\Validator\Constraint;

class CreationDate extends Constraint
{
	public $message = 'Invalid creation date.';
}
