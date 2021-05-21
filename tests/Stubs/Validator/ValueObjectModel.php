<?php
declare(strict_types=1);

namespace Dogado\JsonApi\Server\Tests\Stubs\Validator;

use Dogado\JsonApi\Annotations\Attribute;
use Symfony\Component\Validator\Constraints as Assert;

class ValueObjectModel
{
    /**
     * @Attribute("jsonApiEmail")
     * @Assert\NotBlank()
     * @Assert\Length(min=5)
     * @Assert\Email()
     */
    private ?string $email = 'sawdaw@@awdawcom';
}