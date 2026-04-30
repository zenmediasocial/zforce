<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StoryPageResource\Pages;

use App\Filament\Admin\Resources\StoryPageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStoryPages extends ListRecords
{
    protected static string $resource = StoryPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
