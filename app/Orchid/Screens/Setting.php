<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\ModalToggle;
use App\Models\Task;
use Illuminate\Http\Request;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Purchase;
use App\Models\UserSetting;
use Carbon\Carbon;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Facades\Dashboard;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;

class Setting extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $user = Auth::user();  
         
        $userSettings = UserSetting::where('user_id', $user->id)->latest()->get(); 
        dd($userSettings);
        return [ 
            'userSettingss' => $userSettings, 
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Setting';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('userSettings', [
            TD::make('company_name', 'Company Name'),
            TD::make('company_region', 'Company Region'),
            TD::make('phone', 'Phone'),
            TD::make('website', 'Website'),
            TD::make('facebook_url', 'Facebook URL'),
            TD::make('instagram_url', 'Instagram URL'),
            TD::make('twitter_url', 'Twitter URL'),
            TD::make('pinterest_url', 'Pinterest URL'),
            TD::make('youtube_url', 'YouTube Channel URL'),
            TD::make('tiktok_url', 'TikTok URL'),
            ]),
        ];
    }
}
