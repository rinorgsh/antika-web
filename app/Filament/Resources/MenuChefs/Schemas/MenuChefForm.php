<?php

namespace App\Filament\Resources\MenuChefs\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class MenuChefForm
{
    private const LANGS = ['fr' => 'Français', 'nl' => 'Nederlands', 'en' => 'English', 'al' => 'Shqip'];

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Suggestion du chef')->columns(2)->schema([
                TextInput::make('image')->label('Photo (chemin fichier)')
                    ->helperText('ex. photos/moules-frites.jpg'),
                TextInput::make('price')->label('Prix')->placeholder('27'),
            ]),

            Tabs::make('Traductions')->tabs(
                collect(self::LANGS)->map(fn ($label, $code) => Tab::make($label)->schema([
                    TextInput::make("eyebrow.$code")->label('Petit label (ex. Suggestion de la semaine)'),
                    TextInput::make("title.$code")->label('Titre du plat'),
                    TextInput::make("description.$code")->label('Description'),
                    TextInput::make("plabel.$code")->label('Libellé des préparations'),
                    Repeater::make("preps.$code")->label('Préparations (au choix)')
                        ->schema([
                            TextInput::make('0')->label('Nom')->placeholder('Nature'),
                            TextInput::make('1')->label('Description'),
                        ])->columns(2)->collapsible(),
                ]))->values()->all()
            )->columnSpanFull(),
        ]);
    }
}
