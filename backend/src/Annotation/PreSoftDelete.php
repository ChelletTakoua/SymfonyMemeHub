<?php

namespace App\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
#[\Attribute] final class PreSoftDelete
{
}