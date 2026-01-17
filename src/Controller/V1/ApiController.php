<?php

namespace App\Controller\V1;

use App\Shared\HasPagination;
use App\Shared\HasResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    use HasResponse, HasPagination;
}