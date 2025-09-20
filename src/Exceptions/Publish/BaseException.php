<?php

namespace App\Exceptions;

use Illuminate\Contracts\Debug\ShouldntReport;
use Xetreon\JsonResponse\Exceptions\BaseException as CoreException;

class BaseException extends CoreException implements ShouldntReport
{

}