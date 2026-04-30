<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StoryPageResource\Pages;

use App\Filament\Admin\Resources\StoryPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStoryPage extends CreateRecord
{
    protected static string $resource = StoryPageResource::class;
}
