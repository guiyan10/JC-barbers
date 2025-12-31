<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanoResource\Pages;
use App\Models\Plano;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class PlanoResource extends Resource
{
    protected static ?string $model = Plano::class;
    
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-credit-card';
    }

    protected static ?string $navigationLabel = 'Planos';

    protected static ?string $modelLabel = 'Plano';

    protected static ?string $pluralModelLabel = 'Planos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->required()
                    ->maxLength(255)
                    ->maxWidth('md'),
                Textarea::make('descricao')
                    ->rows(3)
                    ->maxWidth('md'),
                TextInput::make('valor')
                    ->label('Valor (R$)')
                    ->numeric()
                    ->prefix('R$')
                    ->required()
                    ->maxWidth('md'),
                TextInput::make('limite_mensal')
                    ->label('Limite Mensal (cortes)')
                    ->numeric()
                    ->required()
                    ->maxWidth('md'),
                Toggle::make('ativo')
                    ->default(true)
                    ->maxWidth('md'),
            ])
            ->columns([
                'default' => 1,
                'sm' => 2,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('limite_mensal')
                    ->label('Limite Mensal')
                    ->sortable(),
                Tables\Columns\IconColumn::make('ativo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('clientes_count')
                    ->label('Clientes')
                    ->counts('clientes'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('ativo')
                    ->label('Ativo'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlanos::route('/'),
            'create' => Pages\CreatePlano::route('/create'),
            'edit' => Pages\EditPlano::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}

