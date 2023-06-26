<?php

namespace Lunar\Hub\Http\Livewire\Components\VendorSettings;

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

class VendorSettingsIndex extends Component implements Forms\Contracts\HasForms
{
    // use Tables\Concerns\InteractsWithTable;
    use Forms\Concerns\InteractsWithForms;
    use Notifies;

    public $document;
    public $button_name = '変更';

    public function mount()
    {
        $this->document = \App\Models\BrandDocument::where('brand_id', auth()->user()->brand_id)->first();
        if (empty($this->document)) {
            $this->document = new \App\Models\BrandDocument();
            //    $this->document->brand_id = auth()->user()->brand_id;
            //   ray($this->document->brand_id);
            $this->document->id = 0;
            $this->button_name = '登録';
        }
        //ray($this->document->brand_id);
        $this->form->fill([
            'str1' => $this->document->str1 ?? '',
            'str2' => $this->document->str2 ?? '',
            'str3' => $this->document->str3 ?? '',
            'str4' => $this->document->str4 ?? '',
            'str5' => $this->document->str5 ?? '',
            'str6' => $this->document->str6 ?? '',
            'str7' => $this->document->str7 ?? '',
            'str8' => $this->document->str8 ?? '',
            'str9' => $this->document->str9 ?? '',
            'str10' => $this->document->str10 ?? '',
            'str11' => $this->document->str11 ?? '',
            'str12' => $this->document->str12 ?? '',
            'str13' => $this->document->str13 ?? '',
            'str14' => $this->document->str14 ?? '',
            'str15' => $this->document->str15 ?? '',
            'str16' => $this->document->str16 ?? '',
            'str17' => $this->document->str17 ?? '',
            'str18' => $this->document->str18 ?? '',
        ]);

    }


    protected function getFormSchema(): array
    {
//販売業者
//本店所在地
//店舗運営責任者
//電話番号
//FAX 番号
//E-mail
//営業時間
//定休日
//消費税の取扱い
//販売価格について
//お支払金額について
//送料の取扱いについて
//注文方法について
//お支払方法について
//決済手数料の取扱い
//お支払期限
//引渡し時期
//個人情報保護の方針


        return [

            Forms\Components\TextInput::make('str1')
                ->label('販売業者'),
            Forms\Components\TextInput::make('str2')
                ->label('本店所在地'),
            Forms\Components\TextInput::make('str3')
                ->label('店舗運営責任者'),
            Forms\Components\TextInput::make('str4')
                ->label('電話番号'),
            Forms\Components\TextInput::make('str5')
                ->label('FAX 番号'),
            Forms\Components\TextInput::make('str6')
                ->label('E-mail'),
            Forms\Components\TextInput::make('str7')
                ->label('営業時間'),
            Forms\Components\TextInput::make('str8')
                ->label('定休日'),
            Forms\Components\TextInput::make('str9')
                ->label('消費税の取扱い'),
            Forms\Components\TextInput::make('str10')
                ->label('販売価格について'),
            Forms\Components\TextInput::make('str11')
                ->label('お支払金額について'),
            Forms\Components\TextInput::make('str12')
                ->label('送料の取扱いについて'),
            Forms\Components\TextInput::make('str13')
                ->label('注文方法について'),
            Forms\Components\TextInput::make('str14')
                ->label('お支払方法について'),
            Forms\Components\TextInput::make('str15')
                ->label('決済手数料の取扱い'),
            Forms\Components\TextInput::make('str16')
                ->label('お支払期限'),
            Forms\Components\TextInput::make('str17')
                ->label('引渡し時期'),
            Forms\Components\TextInput::make('str18')
                ->label('個人情報保護の方針'),


        ];
    }


    public function saveDocument(): void
    {
        $states = $this->form->getState();
        ray($this->document->brand_id)->label('brand_id');
        \App\Models\BrandDocument::updateOrCreate(
            ['id' => $this->document->id],
            [
                'str1' => $states['str1'],
                'str2' => $states['str2'],
                'str3' => $states['str3'],
                'str4' => $states['str4'],
                'str5' => $states['str5'],
                'str6' => $states['str6'],
                'str7' => $states['str7'],
                'str8' => $states['str8'],
                'str9' => $states['str9'],
                'str10' => $states['str10'],
                'str11' => $states['str11'],
                'str12' => $states['str12'],
                'str13' => $states['str13'],
                'str14' => $states['str14'],
                'str15' => $states['str15'],
                'str16' => $states['str16'],
                'str17' => $states['str17'],
                'str18' => $states['str18'],
                'brand_id' => auth()->user()->brand_id,
            ]
        );
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
        return view('adminhub::livewire.components.vendor-settings.index')
            ->layout('adminhub::layouts.app', [
                'title' => '販売元設定',
            ]);
    }
}
