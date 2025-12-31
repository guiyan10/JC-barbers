<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\AgendamentoResource;
use App\Models\Cliente;
use App\Models\Plano;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;
    
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-user-group';
    }

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados Pessoais')
                    ->schema([
                        TextInput::make('nome')
                            ->required()
                            ->maxLength(255)
                            ->maxWidth('md'),
                        TextInput::make('telefone')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->mask('(99) 99999-9999')
                            ->maxLength(255)
                            ->maxWidth('md'),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->maxWidth('md'),
                        DatePicker::make('data_nascimento')
                            ->maxWidth('md'),
                    ])
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                
                Section::make('Plano')
                    ->schema([
                        Select::make('plano_id')
                            ->label('Plano')
                            ->relationship('plano', 'nome')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->maxWidth('md')
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $plano = Plano::find($state);
                                    if ($plano) {
                                        $set('data_inicio_plano', now());
                                        $set('data_fim_plano', now()->addMonth());
                                    }
                                }
                            }),
                        DatePicker::make('data_inicio_plano')
                            ->label('Data Início do Plano')
                            ->maxWidth('md'),
                        DatePicker::make('data_fim_plano')
                            ->label('Data Fim do Plano')
                            ->maxWidth('md'),
                    ])
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'md' => 3,
                    ])
                    ->collapsible(),
                
                Section::make('Informações Adicionais')
                    ->schema([
                        Textarea::make('observacoes')
                            ->label('Observações')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('telefone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('plano.nome')
                    ->label('Plano')
                    ->default('Não possui')
                    ->badge()
                    ->color(fn ($record) => $record->plano ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('data_ultima_visita')
                    ->label('Última Visita')
                    ->getStateUsing(function (Cliente $record) {
                        $ultimaVisita = $record->getDataUltimaVisita();
                        return $ultimaVisita ? $ultimaVisita->format('d/m/Y') : 'Nunca';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('cortes_utilizados_mes')
                    ->label('Cortes Usados (Mês)')
                    ->getStateUsing(function (Cliente $record) {
                        if (!$record->plano) {
                            return '-';
                        }
                        $record->resetarContadorSeNecessario();
                        return "{$record->cortes_utilizados_mes}/{$record->plano->limite_mensal}";
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plano_id')
                    ->label('Plano')
                    ->relationship('plano', 'nome'),
            ])
            ->actions([
                Action::make('agendar')
                    ->label('Agendar')
                    ->icon('heroicon-o-calendar')
                    ->color('success')
                    ->url(fn (Cliente $record) => AgendamentoResource::getUrl('create', ['cliente_id' => $record->id])),
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
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}

