<?php

namespace App\Filament\Admin\Resources\Projects\Resources\Boards\Pages;

use App\Filament\Admin\Resources\Projects\Resources\Boards\BoardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBoard extends CreateRecord
{
    protected static string $resource = BoardResource::class;
}
