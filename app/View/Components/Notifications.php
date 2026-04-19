<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Notifications extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $admin = auth('admin')->user();

        return view('components.notifications',[
            'notifications' => $admin
                ? $admin->notifications()->latest()->get()
                : collect(),
        ]);
    }
}
