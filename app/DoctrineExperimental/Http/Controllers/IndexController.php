<?php

namespace Angelov\Eestec\Platform\DoctrineExperimental\Http\Controllers;

use Angelov\Eestec\Platform\Core\Http\Controllers\BaseController;
use Doctrine\ORM\EntityManager;

class IndexController extends BaseController
{
    public function index(EntityManager $em)
    {
    }
}
