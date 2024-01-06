<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;

/**
 * @method static string banner()
 */
enum WithEnum: string
{
    use InvokableCases;

    case banner = 'banner:id,model_type,model_id,disk,file_name,manipulations,custom_properties,generated_conversions,responsive_images';
}
