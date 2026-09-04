<?php

namespace App\Filament\Resources\MenuCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class MenuCategoryForm
{
    private const LANGS = ['fr' => 'Français', 'nl' => 'Nederlands', 'en' => 'English', 'al' => 'Shqip'];

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Section')->columns(2)->schema([
                Select::make('surface')->label('Surface')->required()
                    ->options(['food' => 'Nourriture', 'desserts' => 'Desserts', 'drinks' => 'Boissons', 'event' => 'Événement'])
                    ->live(),
                TextInput::make('banner')->label('Bandeau illustré (événement)')
                    ->placeholder('titre-cocktails.jpg')
                    ->helperText('Nom du fichier dans le dossier event/ du menu. Laisser vide pour un titre typographié.')
                    ->visible(fn ($get) => $get('surface') === 'event'),
                TextInput::make('slug')->label('Identifiant (clé)')->required(),
                TextInput::make('position')->label('Ordre')->numeric()->default(0),
                Toggle::make('has_note')->label('Afficher une note de bas de section'),
            ]),

            Tabs::make('Traductions')->tabs(
                collect(self::LANGS)->map(fn ($label, $code) => Tab::make($label)->schema([
                    TextInput::make("title.$code")->label('Titre'),
                    TextInput::make("subtitle.$code")->label('Sous-titre (petit label doré)'),
                    Textarea::make("note.$code")->label('Note de section (optionnel, HTML autorisé)')->rows(2),
                ]))->values()->all()
            )->columnSpanFull(),
        ]);
    }
}
