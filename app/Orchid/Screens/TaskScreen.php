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
use Carbon\Carbon;

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
    
        $tasks = Task::latest()->get();
    
        $purchases = $user && $user->is_admin ? 
            Purchase::latest()->get() : 
            Purchase::where('user_id', $user->id)->latest()->get();
    
        return [
            'tasks' => $tasks,
            'purchases' => $purchases,
            'money' => $money,
        ];
    }
    

    /**
     * The name is displayed on the user's screen and in the headers.
     */
    public function name(): ?string
    {
        return 'Vending Machine @ Prom Planner';
    }

    /**
     * The description is displayed on the user's screen under the heading.
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
        $taskTableColumns = [
            TD::make('name', 'Name'),
            TD::make('cost', 'Cost'),
            TD::make('cnt', 'Count'),
            TD::make('descr', 'Description'),
            TD::make('Actions')->alignRight()->render(function (Task $task) {
                return ModalToggle::make('Buy Item')
                    ->modal('buyModal')
                    ->method('buy', ['task' => $task->id]);       
            }),
        ];

        if ($this->isAdmin()) {
            $taskTableColumns[] = TD::make('Admin Actions')
                ->alignRight()
                ->render(function (Task $task) {
                    return Button::make('Delete Item')
                            ->confirm('After deleting, the task will be gone forever.')
                            ->method('delete', ['task' => $task->id])
                        . ModalToggle::make('Restock Item')
                        ->modal('restockModal')
                        ->method('restock', ['task' => $task->id]);              
                });
        }

        return [ 
            Layout::rows([
                Input::make('money')
                    ->title('Current Money')
                    ->value($this->query()['money'])  
                    ->readonly()
                    ->help('Your current money balance.'),
            ]),
            Layout::modal('addFundsModal', Layout::rows([ 
                Input::make('amount')
                    ->title('Amount')
                    ->placeholder('Enter the amounts of money to add to your account')
                    ->type('number')
                    ->step('0.01') 
            ]))
            ->title('Add Funds')
            ->applyButton('Add Funds'), 
            Layout::table('tasks', $taskTableColumns),  
            Layout::table('purchases', [ 
                TD::make('item_id', 'Item Name')
                    ->render(function (Purchase $purchase) {
                        return $purchase->task ? $purchase->task->name : 'N/A';
                    }),
                TD::make('quantity'),
                TD::make('amount', 'Cost'),
                TD::make('user_id', 'User')
                    ->render(function (Purchase $purchase) {
                        return $purchase->user ? $purchase->user->name : 'N/A';
                    }),
                TD::make('date', 'Date') 
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

            Layout::modal('buyModal', Layout::rows([ 
                Input::make('cnt')
                    ->title('Buy Count')
                    ->placeholder('Enter item count to buy')
                    ->type('number')
                    ->help('The amount of items to buy.'),
            ]))
            ->title('Buy item')
            ->applyButton('Buy item')
            
        ];
    } 

    /**
     * Add funds to user's account.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addFunds(Request $request)
    {
        $user = Auth::user();
        $user->money += $request->input('amount');
        $user->save();
        return redirect()->back()->with('success', 'Funds added successfully!');
    }

    /**
     * Update user's profile information.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request){
        $user = Auth::user();
        $user->update($request->input('user'));
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Restock items in the inventory.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restock(Request $request)
    { 
        $task = Task::find($request->input('task'));  
        $restockCount = $request->input('cnt');
        if ($task) {
            $task->cnt += $restockCount;
            $task->save(); 

            return redirect()->route('platform.task')->with('success', 'Restock successful!');
        }

        return redirect()->route('platform.task')->with('error', 'Restock failed. Item not found.');
    } 

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $actions = [
            ModalToggle::make('Add Funds')
                ->modal('addFundsModal')
                ->method('addFunds')
                ->icon('plus'),
        ];

        if ($this->isAdmin()) {
            $actions[] = ModalToggle::make('Add Item')
                ->modal('taskModal')
                ->method('create')
                ->icon('plus');
        }

        return $actions;
    }

    /**
     * Create a new item in the inventory.
     *
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
     * Delete an item from the inventory.
     *
     * @param Task $task
     *
     * @return void
     */
    public function delete(Task $task)
    {
        $task->delete();
    }

    /**
     * Handle item purchase.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function buy(Request $request)
    { 
        $task = Task::find($request->input('task'));
        $user = Auth::user();
        
        if ($task && $user) {
            $purchaseQuantity = $request->input('cnt'); 
            
            if ($purchaseQuantity <= 0 || $purchaseQuantity > $task->cnt) {
                return redirect()->route('platform.task')->with('error', 'Invalid quantity.');
            }
            
            $totalCost = $task->cost * $purchaseQuantity;

            if ($user->money < $totalCost) {
                return redirect()->route('platform.task')->with('error', 'Insufficient funds.');
            }

            $task->cnt -= $purchaseQuantity;
            $task->save();

            $user->money -= $totalCost;
            $user->save();

            $purchase = new Purchase();
            $purchase->user_id = $user->id;
            $purchase->item_id = $task->id;
            $purchase->quantity = $purchaseQuantity;
            $purchase->amount = $totalCost;
            $purchase->date = Carbon::now();
            $purchase->save();

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
        return $user && $user->is_admin;
    }

    /**
     * Asynchronously get task data.
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
