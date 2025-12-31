<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendamentoResource\Pages;
use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Servico;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
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

class AgendamentoResource extends Resource
{
    protected static ?string $model = Agendamento::class;
    
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-calendar';
    }

    protected static ?string $navigationLabel = 'Agendamentos';

    protected static ?string $modelLabel = 'Agendamento';

    protected static ?string $pluralModelLabel = 'Agendamentos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Agendamento')
                    ->schema([
                        Select::make('cliente_id')
                            ->label('Cliente')
                            ->relationship('cliente', 'nome')
                            ->searchable(['nome', 'telefone'])
                            ->preload()
                            ->required()
                            ->maxWidth('md')
                            ->createOptionForm([
                                TextInput::make('nome')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('telefone')
                                    ->required()
                                    ->unique('clientes', 'telefone')
                                    ->mask('(99) 99999-9999')
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                return Cliente::create($data)->id;
                            }),
                        Select::make('servico_id')
                            ->label('Serviço')
                            ->relationship('servico', 'nome', fn (Builder $query) => $query->where('ativo', true))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->maxWidth('md'),
                        DateTimePicker::make('data_hora')
                            ->label('Data e Hora')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->minDate(now())
                            ->maxWidth('md'),
                    ])
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'md' => 3,
                    ]),
                
                Section::make('Status do Atendimento')
                    ->schema([
                        Select::make('status')
                            ->options([
                                0 => 'Agendado',
                                1 => 'Em Atendimento',
                                2 => 'Finalizado',
                            ])
                            ->default(0)
                            ->required()
                            ->maxWidth('md')
                            ->disabled(fn ($record) => $record && $record->status === 2),
                        DateTimePicker::make('hora_inicio')
                            ->label('Hora Início')
                            ->disabled()
                            ->maxWidth('md')
                            ->visible(fn ($get) => $get('status') >= 1),
                        DateTimePicker::make('hora_fim')
                            ->label('Hora Fim')
                            ->disabled()
                            ->maxWidth('md')
                            ->visible(fn ($get) => $get('status') == 2),
                    ])
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'md' => 3,
                    ])
                    ->collapsible(),
                
                Section::make('Observações')
                    ->schema([
                        Textarea::make('observacoes')
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
                Tables\Columns\TextColumn::make('cliente.nome')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cliente.telefone')
                    ->label('Telefone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('servico.nome')
                    ->label('Serviço')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_hora')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        0 => 'Agendado',
                        1 => 'Em Atendimento',
                        2 => 'Finalizado',
                        default => 'Desconhecido',
                    })
                    ->color(fn ($state) => match($state) {
                        0 => 'warning',
                        1 => 'info',
                        2 => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('descontado_plano')
                    ->label('Descontado do Plano')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        0 => 'Agendado',
                        1 => 'Em Atendimento',
                        2 => 'Finalizado',
                    ]),
                Tables\Filters\Filter::make('data_hora')
                    ->form([
                        DatePicker::make('data_de')
                            ->label('Data De'),
                        DatePicker::make('data_ate')
                            ->label('Data Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['data_de'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_hora', '>=', $date),
                            )
                            ->when(
                                $data['data_ate'],
                                fn (Builder $query, $date): Builder => $query->whereDate('data_hora', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Action::make('iniciar')
                    ->label('Iniciar Atendimento')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->visible(fn (Agendamento $record) => $record->status === 0)
                    ->requiresConfirmation()
                    ->action(function (Agendamento $record) {
                        $record->iniciarAtendimento();
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Atendimento Iniciado')
                            ->body('O atendimento foi iniciado e o corte foi descontado do plano do cliente.')
                            ->send();
                    }),
                Action::make('finalizar')
                    ->label('Finalizar Atendimento')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Agendamento $record) => $record->status === 1)
                    ->requiresConfirmation()
                    ->action(function (Agendamento $record) {
                        $record->finalizarAtendimento();
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Atendimento Finalizado')
                            ->body('O atendimento foi finalizado com sucesso.')
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('data_hora', 'desc');
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
            'index' => Pages\ListAgendamentos::route('/'),
            'create' => Pages\CreateAgendamento::route('/create'),
            'edit' => Pages\EditAgendamento::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}

