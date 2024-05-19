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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
 
use Illuminate\Support\Facades\Log;

use Orchid\Screen\Fields\Hidden;
 
use App\Models\User;

// cat storage/logs/laravel.log

class TaskScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */

    
     public function query(): iterable
     {
         $user = Auth::user();
         $money = $user ? $user->money : 0.00;
     
         // Retrieve purchase history for the current user
         $purchaseHistory = $user ? $user->purchaseHistory : [];
     
         return [
            'tasks' => Task::latest()->get(), 
            'money' => $money, 
         ];
     }
     
    

    /**
     * The name is displayed on the user's screen and in the headers
     */
    public function name(): ?string
    {
        return 'Vending Machine @ Prom Planner';
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return 'Manage vending machine items';
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [  
            Layout::rows([
                Input::make('money')
                    ->title('Current Money')
                    ->value($this->query()['money'])  
                    ->readonly()
                    ->help('Your current money balance.'),
            ])
            , 
            Layout::modal('addFundsModal', Layout::rows([ 
                
                Input::make('amount')
                    ->title('Amount')
                    ->placeholder('Enter the amounts of money to add to your account')
                    ->type('number')
                    ->step('0.01') 
            ]))
            ->title('Restock item')
            ->applyButton('Add Funds'),

            Layout::table('tasks', [
                TD::make('name'),
                TD::make('cost'),
                TD::make('cnt'),
                TD::make('descr'),
                TD::make('Actions')
                    ->alignRight()
                    ->render(function (Task $task) {
                        return Button::make('Buy')
                            ->confirm('Do you want to buy this item?')
                            ->method('buy', ['task' => $task->id]);
                    }),
                TD::make('Admin Actions')
                    ->alignRight()
                    ->render(function (Task $task) {
                        if ($this->isAdmin()) {
                            return Button::make('Delete Item')
                                    ->confirm('After deleting, the task will be gone forever.')
                                    ->method('delete', ['task' => $task->id])
                                .  ModalToggle::make('Restock Item')
                                ->modal('restockModal')
                                ->method('restock', ['task' => $task->id]);              
                        }
                        return '';
                    }),
            ]),
            Layout::modal('taskModal', Layout::rows([
                Input::make('task.name')
                    ->title('Name')
                    ->placeholder('Enter item name')
                    ->help('The name of the item.'),
                Input::make('task.cost')
                    ->title('Cost')
                    ->placeholder('Enter item cost')
                    ->type('number')
                    ->step('0.01')
                    ->help('The cost of the item.'),
                Input::make('task.cnt')
                    ->title('Count')
                    ->placeholder('Enter item count')
                    ->type('number')
                    ->help('The amount of items in stock.'),
                Input::make('task.descr')
                    ->title('Description')
                    ->placeholder('Enter item description')
                    ->help('Description of the item.'),
            ]))
            ->title('Create item')
            ->applyButton('Add item'),

            Layout::modal('restockModal', Layout::rows([ 
                Input::make('cnt')
                    ->title('Restock Count')
                    ->placeholder('Enter item count to add')
                    ->type('number')
                    ->help('The amount of items to add to stock.'),
            ]))
            ->title('Restock item')
            ->applyButton('Restock item'),
            
            Layout::table('purchase_history', [
                TD::make('name'),
            ]),


            // Layout::table('purchase_history', [
            //     TD::make('user_id'),
            //     TD::make('item_id'),
            //     TD::make('quantity'),
            //     TD::make('amount'), 
            //     TD::make('created_at')
            // ]), 
        ];
    } 
    public function addFunds(Request $request)
    {
        $user = Auth::user();
        $user->money += $request->input('amount');
        $user->save();
        return redirect()->back()->with('success', 'Funds added successfully!');
    }
    public function updateProfile(Request $request){
        $user = Auth::user();
        $user->update($request->input('user'));
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function restock(Request $request)
    {
        // $request->validate([
        //     'task_id' => 'required|exists:tasks,id', // Validate task ID exists
        //     'cnt' => 'required|integer', // Validate count
        // ]);
    
        // Retrieve the task by ID

        
        $task = Task::find($request->input('task'));  
        $restockCount = $request->input('cnt');
        if ($task) {
            $task->cnt += $restockCount;
            $task->save(); 

            return redirect()->route('platform.task')->with('success', 'Purchase successful!');
        }

        return redirect()->route('platform.task')->with('error', 'Purchase failed. Check stock or balance.');
    } 
    
    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
{
    return [
        ModalToggle::make('Add Item')
            ->modal('taskModal')
            ->method('create')
            ->icon('plus'),
            
        ModalToggle::make('Add Funds')
            ->modal('addFundsModal')
            ->method('addFunds')
            ->icon('plus'),
    ];
}

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function create(Request $request)
    {
        $request->validate([
            'task.name' => 'required',
            'task.cost' => 'required|numeric',
            'task.cnt' => 'required|integer',
            'task.descr' => 'required',
        ]);

        $task = new Task();
        $task->name = $request->input('task.name');
        $task->cost = $request->input('task.cost');
        $task->cnt = $request->input('task.cnt');
        $task->descr = $request->input('task.descr');
        $task->save();
    }

    /**
     * @param Task $task
     *
     * @return void
     */
    public function delete(Task $task)
    {
        $task->delete();
    }

    /**
     * Method to handle item purchase.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function buy(Request $request)
    {
        $task = Task::find($request->input('task'));
        $user = Auth::user();
        
        if ($task && $task->cnt > 0 && $user && $user->money >= $task->cost) {
            $task->cnt -= 1;
            $task->save();

            $user->money -= $task->cost; // Deduct the cost from user's money
            $user->save(); // Save the updated user's money

            return redirect()->route('platform.task')->with('success', 'Purchase successful!');
        }

        return redirect()->route('platform.task')->with('error', 'Purchase failed. Check stock or balance.');
    } 

    /**
     * Check if the current user is an admin.
     *
     * @return bool
     */
    protected function isAdmin(): bool
    {
        $user = Auth::user();
        return $user && $user->is_admin; // Adjust based on your admin check logic
    }

    /**
     * Method to asynchronously get task data.
     *
     * @param Task $task
     *
     * @return array
     */
    public function asyncGetTask(Task $task): array
    {
        return [
            'task.cnt' => $task->cnt,
        ];
    }

}
