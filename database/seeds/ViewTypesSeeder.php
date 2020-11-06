<?php

use Illuminate\Database\Seeder;

class ViewTypesSeeder extends Seeder
{
    /**
     * @throws Throwable
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            $viewTypeList = \App\Models\Field::create(['type' => 'view_type', 'key' => 'list', 'name' => 'List', 'is_public' => 1, 'is_default' => 1]);
            $viewTypeKanban = \App\Models\Field::create(['type' => 'view_type', 'key' => 'kanban', 'name' => 'Kanban', 'is_public' => 1]);
            $viewTypeCalendar = \App\Models\Field::create(['type' => 'view_type', 'key' => 'calender', 'name' => 'Calendar', 'is_public' => 1]);
            $viewTypeCommunication = \App\Models\Field::create(['type' => 'view_type', 'key' => 'communication', 'name' => 'Communication', 'is_public' => 1]);
            $viewTypeGantt = \App\Models\Field::create(['type' => 'view_type', 'key' => 'gantt', 'name' => 'Gantt', 'is_public' => 1]);

            \App\Models\Board::where('view_type_id', 1)->update(['view_type_id' => $viewTypeList->id]);
            \App\Models\Board::where('view_type_id', 2)->update(['view_type_id' => $viewTypeKanban->id]);
            \App\Models\Board::where('view_type_id', 3)->update(['view_type_id' => $viewTypeCalendar->id]);
            \App\Models\Board::where('view_type_id', 4)->update(['view_type_id' => $viewTypeCommunication->id]);
            \App\Models\Board::where('view_type_id', 5)->update(['view_type_id' => $viewTypeGantt->id]);
            
            \App\Models\Filter::where('view_type_id', 1)->update(['view_type_id' => $viewTypeList->id]);
            \App\Models\Filter::where('view_type_id', 2)->update(['view_type_id' => $viewTypeKanban->id]);
            \App\Models\Filter::where('view_type_id', 3)->update(['view_type_id' => $viewTypeCalendar->id]);
            \App\Models\Filter::where('view_type_id', 4)->update(['view_type_id' => $viewTypeCommunication->id]);
            \App\Models\Filter::where('view_type_id', 5)->update(['view_type_id' => $viewTypeGantt->id]);
        });
    }
}
