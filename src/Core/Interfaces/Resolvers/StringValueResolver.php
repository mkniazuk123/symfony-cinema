<?php

namespace App\Core\Interfaces\Resolvers;

use App\Core\Domain\InvalidValueException;
use App\Core\Domain\StringValue;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class StringValueResolver implements ValueResolverInterface
{
    /**
     * @throws InvalidValueException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if (
            !$argumentType
            || !is_subclass_of($argumentType, StringValue::class)
        ) {
            return [];
        }

        $value = $request->attributes->get($argument->getName());

        return [new $argumentType($value)];
    }
}
