<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\City;
use Filament\Tables;
use App\Models\State;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Employee;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EmployeeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmployeeResource\RelationManagers;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Employees';

    protected static ?string $modelLabel = 'Employee';

    protected static ?string $navigationGroup = 'Employee Management';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Relations')
                ->description('User relationship')
                ->schema
                ([
                    Forms\Components\Select::make('country_id')
                    ->relationship(name:'country',titleAttribute:'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('state_id', null);
                        $set('city_id', null);
                    })
                    ->required(),
                    Forms\Components\Select::make('state_id')
                    ->options(fn (Get $get): Collection => State::query()
                    ->where('country_id', $get('country_id'))
                    ->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('city_id', null))
                    ->required(),
                    Forms\Components\Select::make('city_id')
                    ->relationship(name:'city',titleAttribute:'name')
                    //->searchable()
                    //->preload()
                    ->options(fn(Get $get):Collection =>City::query()
                    ->where('state_id', $get('state_id'))
                    ->pluck('name','id')
                    )
                    ->required()
                    ,
                    Forms\Components\Select::make('department_id')
                    ->relationship(name:'department',titleAttribute:'name')

                    //->searchable()
                    //->preload()
                    ->required()
                ])->columns(2),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('middle_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('zip_code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date_of_birth')
                    ->required(),
                Forms\Components\DatePicker::make('date_hired')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('country_id')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('state_id')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('city_id')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('department_id')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_hired')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
