<?php

declare(strict_types=1);

namespace App\Http\Request;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Serializer;

class DtoConverter implements ParamConverterInterface
{
    private Serializer $serializer;
    private string $env;

    public function __construct(Serializer $serializer, ParameterBagInterface $bag)
    {
        $this->serializer = $serializer;
        $this->env = $bag->get('kernel.environment');
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $body = $request->getContent();

        if ($request->getContentType() !== 'json') {
            throw new HttpException(
                Response::HTTP_NOT_ACCEPTABLE,
                'Invalid request. Make sure that you are using application/json content type'
            );
        }

        try {
            $className = $configuration->getClass();

            $dto = new $className((array)json_decode($body) ?? []);
            $request->attributes->set($configuration->getName(), $dto);
        } catch (\Throwable $ex) {
            if (in_array($this->env, ['dev', 'test'])) {
                throw $ex;
            }

            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid request');
        }

        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        if ($configuration->getConverter() !== 'dto_converter') {
            return false;
        }

        if (str_ends_with($configuration->getClass(), 'Dto')) {
            return true;
        }

        return false;
    }
}