<?php

namespace TrainBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TrainBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
