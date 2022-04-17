<?php

namespace App\Http\Requests;

use Illuminate\Validation\Factory;

abstract class JsonRequest extends Request
{

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Validation\Validator
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getValidatorInstance()
    {
        $factory = $this->container->make(Factory::class);

        if (method_exists($this, 'validator')) {
            return $this->container->call([$this, 'validator'], compact('factory'));
        }

        return $factory->make(
            $this->json()->all(),
            $this->container->call([$this, 'rules']),
            $this->messages(),
            $this->attributes()
        );
    }

}
