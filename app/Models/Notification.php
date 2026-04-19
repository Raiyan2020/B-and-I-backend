<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class  Notification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends=['title','body'];
    protected $guarded = [''];

    protected $casts = [
        'payload' => 'array',
        'seen' => 'boolean',
    ];

    public function getTitleAttribute(){
        $title = app()->getLocale() == 'ar' ? $this->title_ar : $this->title_en;
        return $title;
    }
    public function getBodyAttribute(){
        $body = app()->getLocale() == 'ar' ? $this->body_ar : $this->body_en;
        return $body;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function targetUrl(): ?string
    {
        if ($this->order_id) {
            return route('admin.orders.show', $this->order_id);
        }

        if (! $this->model_type || ! $this->model_id) {
            return null;
        }

        return match ($this->model_type) {
            'User' => route('admin.users.show', $this->model_id),
            'Opportunity' => route('admin.opportunities.show', $this->model_id),
            'InvestmentSeat' => route('admin.investment-seats.show', $this->model_id),
            'InterestRequest' => route('admin.interest-requests.show', $this->model_id),
            'ProfileUpdateRequest' => route('admin.profile-update-requests.show', $this->model_id),
            'AccountDeletionRequest' => route('admin.account-deletion-requests.show', $this->model_id),
            'CompanyInvestorInterestRequest' => route('admin.company-investor-interest-requests.index', [
                'company_id' => data_get($this->payload, 'company_id'),
                'investor_id' => data_get($this->payload, 'investor_id'),
            ]),
            default => null,
        };
    }
}
