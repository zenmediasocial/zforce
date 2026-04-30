<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StoryPageResource\Pages;

use App\Filament\Admin\Resources\StoryPageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStoryPage extends EditRecord
{
    protected static string $resource = StoryPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
