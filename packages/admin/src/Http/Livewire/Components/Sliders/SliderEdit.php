<?php

namespace Lunar\Hub\Http\Livewire\Components\Sliders;

use Closure;
use DB;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Filament\Tables;
use Illuminate\Contracts\Pagination\Paginator;
use Lunar\Hub\Models\Staff;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Webbingbrasil\FilamentDateFilter\DateFilter;

use Carbon\Carbon;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Layout;
use Filament\Forms;
use Filament\Tables\Filters\Filter;
use Lunar\Hub\Http\Livewire\Traits\Notifies;

class SliderEdit extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    use Notifies;

    public $slide;
    public $button_name = '変更';

    public function mount($id)
    {
        $this->slide = \App\Models\Slider::find($id);
        // ray($this->slide->id)->label('s最初');
        if (empty($this->slide)) {
            return null;
        }
        //ray($this->document->brand_id);
        $this->form->fill([
            'image' => $this->slide->image ?? '',
            'is_show' => $this->slide->is_show,
            'link' => $this->slide->link ?? '',
            'is_newtab' => $this->slide->is_newtab,
        ]);

    }


    protected function getFormSchema(): array
    {


        return [

            Forms\Components\FileUpload::make('image')->label('画像')
                ->placeholder('画像をドラッグ＆ドロップ or ここをクリックして画像を選択')
                ->imagePreviewHeight('250')
                ->panelAspectRatio('2:1')
                ->image()
                ->imageCropAspectRatio('16:9')
                ->imageResizeTargetWidth('1920')
                ->imageResizeTargetHeight('1080')
                ->imageResizeMode('cover')
                ->helperText('1920X1080の画像をアップロードしてください。解像度が違う場合トリミングされます')
                ->directory('sliders'),

            Forms\Components\TextInput::make('link')->url()
                ->label('リンクURL'),
            Forms\Components\Toggle::make('is_newtab')
                ->label('新しいタブで開く'),
            Forms\Components\Toggle::make('is_show')
                ->label('表示状態'),

        ];
    }


    public function saveSlide(): void
    {
        $states = $this->form->getState();
        ray($this->slide)->label('slider id');
        ray($states)->label('$states ');
        $slide = \App\Models\Slider::find($this->slide->id);
        $slide->image = $states['image'];
        $slide->is_show = $states['is_show'];
        $slide->link = $states['link'];
        $slide->is_newtab = $states['is_newtab'];
        $slide->save();

        $this->notify('保存しました');
//        Notification::make()
//            ->title('保存しました')
//            ->success()
//            ->send();
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.components.sliders.edit')
            ->layout('adminhub::layouts.app', [
                'title' => 'スライダー画像編集',
            ]);
    }
}
