<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class CustomerExport implements FromView,WithEvents
{
    public $customers;
    public function __construct($customers)
    {
        $this->customers = $customers;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view():View
    {
        return view('dashboard.users.export',['customers'=>$this->customers]);
    }


    public function registerEvents(): array
    {
        return app()->getLocale()=='ar'?[
            BeforeSheet::class  =>function(BeforeSheet $event){
                $event->getDelegate()->setRightToLeft(true);
            }
        ]:[];
    }
}
