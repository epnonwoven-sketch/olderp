<?php

namespace App\View\Components;

use App\Models\UserTypeMaster;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserForm extends Component
{
    /**
     * Create a new component instance.
     */
    public $user_type_list;
    public function __construct()
    {
        $this->user_type_list = UserTypeMaster::where("lock_status",false)->orderBy("id","ASC")->get();print_var("ok");
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-form');
    }
}
