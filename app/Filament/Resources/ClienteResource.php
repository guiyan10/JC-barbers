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
use Carbon\Carbon;

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
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                if ($state) {
                                    $plano = Plano::find($state);
                                    if ($plano) {
                                        $duracaoDias = (int) ($plano->duracao_dias ?? 30);
                                        $inicioAtual = $get('data_inicio_plano');
                                        $inicio = $inicioAtual ? Carbon::parse($inicioAtual) : Carbon::today();

                                        if (! $inicioAtual) {
                                            $set('data_inicio_plano', $inicio->toDateString());
                                        }

                                        $set('data_fim_plano', $inicio->copy()->addDays($duracaoDias)->toDateString());
                                        $set('data_pagamento_previsto', $inicio->copy()->addDays($duracaoDias)->toDateString());
                                    }
                                }
                            }),
                        DatePicker::make('data_inicio_plano')
                            ->label('Data Início do Plano')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $planoId = $get('plano_id');

                                if (! $state || ! $planoId) {
                                    return;
                                }

                                $plano = Plano::find($planoId);

                                if (! $plano) {
                                    return;
                                }

                                $inicio = Carbon::parse($state);
                                $duracaoDias = (int) ($plano->duracao_dias ?? 30);

                                $set('data_fim_plano', $inicio->copy()->addDays($duracaoDias)->toDateString());
                                $set('data_pagamento_previsto', $inicio->copy()->addDays($duracaoDias)->toDateString());
                            })
                            ->maxWidth('md'),
                        TextInput::make('dia_pagamento')
                            ->label('Dia de Pagamento')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(31)
                            ->helperText('Use o dia do mês para controle (1 a 31).')
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
                Tables\Columns\TextColumn::make('data_inicio_plano')
                    ->label('Início do Plano')
                    ->date('d/m/Y')
                    ->placeholder('-')
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('data_fim_plano')
                    ->label('Vencimento do Plano')
                    ->date('d/m/Y')
                    ->placeholder('-')
                    ->badge()
                    ->color(function (Cliente $record) {
                        if (! $record->data_fim_plano) {
                            return 'gray';
                        }

                        return Carbon::today()->lte(Carbon::parse($record->data_fim_plano))
                            ? 'success'
                            : 'danger';
                    }),
                Tables\Columns\TextColumn::make('data_pagamento_previsto')
                    ->label('Pagamento Previsto')
                    ->date('d/m/Y')
                    ->placeholder('-')
                    ->badge()
                    ->color(function (Cliente $record) {
                        if (! $record->data_pagamento_previsto) {
                            return 'gray';
                        }

                        return Carbon::today()->lte(Carbon::parse($record->data_pagamento_previsto))
                            ? 'info'
                            : 'danger';
                    }),
                Tables\Columns\TextColumn::make('dia_pagamento')
                    ->label('Dia Pgto')
                    ->placeholder('-')
                    ->badge()
                    ->color('warning'),
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
                Action::make('marcar_pagamento_plano')
                    ->label('Marcar Pagamento')
                    ->icon('heroicon-o-banknotes')
                    ->color('warning')
                    ->visible(fn (Cliente $record) => (bool) $record->plano_id)
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar pagamento do plano')
                    ->modalDescription('Ao confirmar, o vencimento será renovado somando a duração do plano.')
                    ->action(function (Cliente $record) {
                        $record->renovarPlano();
                        $record->refresh();

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Pagamento registrado')
                            ->body('Novo vencimento: ' . optional($record->data_fim_plano)->format('d/m/Y'))
                            ->send();
                    }),
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

