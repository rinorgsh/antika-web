<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use App\Models\MenuCategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class MenuItemForm
{
    private const LANGS = ['fr' => 'Français', 'nl' => 'Nederlands', 'en' => 'English', 'al' => 'Shqip'];

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Rattachement')->columns(2)->schema([
                Select::make('category_id')
                    ->label('Catégorie')
                    ->options(fn () => MenuCategory::orderBy('surface')->orderBy('position')->get()
                        ->mapWithKeys(fn ($c) => [$c->id => ucfirst($c->surface).' · '.($c->title['fr'] ?? $c->slug)]))
                    ->searchable()->required(),
                TextInput::make('slug')->label('Identifiant (clé)')->required()
                    ->helperText('Technique — à ne pas changer sans raison.'),
                Toggle::make('is_active')->label('Actif (visible sur la carte)')->default(true)
                    ->helperText('Désactive pour masquer temporairement le plat, sans le supprimer.')
                    ->columnSpanFull(),
            ]),

            Tabs::make('Traductions')->tabs(
                collect(self::LANGS)->map(fn ($label, $code) => Tab::make($label)->schema([
                    TextInput::make("name.$code")->label('Nom'),
                    Textarea::make("description.$code")->label('Description')->rows(2),
                ]))->values()->all()
            )->columnSpanFull(),

            Section::make('Prix & options')->columns(3)->schema([
                TextInput::make('price')->label('Prix')->placeholder('12,50')
                    ->helperText('Sans le €. Laisser vide si variantes.'),
                Toggle::make('per_person')->label('Par personne (/pers.)'),
                Toggle::make('is_zero')->label('Badge 0% (mocktail)'),
                Toggle::make('has_description')->label('Afficher la description'),
                Toggle::make('is_subheader')->label('Ligne sous-titre'),
                TextInput::make('default_name')->label('Nom de marque')
                    ->helperText('Identique dans toutes les langues (ex. Aperol Spritz).'),
                TextInput::make('hint')->label('Mention (événement)')
                    ->placeholder('rhum, vodka…')
                    ->helperText('Affichée en orange à côté du nom, sur la page événement uniquement.'),
            ]),

            Section::make('Photo & logo')->columns(2)->collapsible()->schema([
                FileUpload::make('photo_upload')->label('Photo du plat')
                    ->image()->imageEditor()->disk('public')->directory('menu-uploads')
                    ->helperText(fn ($record) => $record?->photo
                        ? 'Photo actuelle : '.$record->photo.' — déposez une image pour la remplacer.'
                        : 'Déposez une image (elle sera publiée avec la carte).'),
                FileUpload::make('logo_upload')->label('Logo de marque')
                    ->image()->disk('public')->directory('menu-uploads')
                    ->helperText(fn ($record) => $record?->logo
                        ? 'Logo actuel : '.$record->logo
                        : 'Optionnel (boissons de marque).'),
            ]),

            Section::make('Tags & lien')->columns(2)->collapsible()->collapsed()->schema([
                TagsInput::make('tags')->label('Badges')
                    ->helperText('ex. min2, sundayonly'),
                TextInput::make('link')->label('Lien externe')->url(),
            ]),

            Repeater::make('variants')->label('Variantes (formats)')->collapsible()->schema([
                TextInput::make('0')->label('Format (clé)')->placeholder('glass, bottle…'),
                TextInput::make('1')->label('Prix'),
            ])->columns(2)->columnSpanFull()
                ->helperText('Pour vins & bulles : plusieurs formats/prix.'),
        ]);
    }
}
