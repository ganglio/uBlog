<?php

namespace uBlog\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CreationDateValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{
		if (null === $value || '' === $value) {
			return;
		}

		if ("DateTime" !== get_class($value)) {
			throw new UnexpectedTypeException($value, "DateTime");
		}

		$timeDiff = time()-$value->getTimestamp();

		if (0 > $timeDiff) {
			$this->context->addViolation($constraint->message, array('{{ value }}' => $value));
		}
	}
}
