<?php

namespace App\Services;

use App\Models\MessageCampaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UserSegmentService
{
    public function getUsersForCampaign(MessageCampaign $campaign): Collection
    {
        $query = User::query();

        // Apply filters if they exist
        if (!empty($campaign->filters)) {
            $query = $this->applyFilters($query, $campaign->filters);
        }

        // Only include users who can receive at least one campaign channel
        $query->where(function (Builder $q) use ($campaign) {
            foreach ($campaign->channels as $channel) {
                $q->orWhere(function (Builder $subQ) use ($channel) {
                    switch ($channel) {
                        case 'email':
                            $subQ->where('notify_email', true)->whereNotNull('email');
                            break;
                        case 'in_app':
                            $subQ->where('notify_in_app', true);
                            break;
                        case 'whatsapp':
                            $subQ->where('notify_whatsapp', true)->whereNotNull('wa_number');
                            break;
                        case 'sms':
                            $subQ->where('notify_sms', true)->whereNotNull('sms_number');
                            break;
                        case 'push':
                            $subQ->where('notify_push', true)->whereNotNull('fcm_token');
                            break;
                    }
                });
            }
        });

        return $query->get();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $key => $value) {
            $query = $this->applyFilter($query, $key, $value);
        }

        return $query;
    }

    protected function applyFilter(Builder $query, string $key, $value): Builder
    {
        return match ($key) {
            'role' => $this->filterByRole($query, $value),
            'active' => $this->filterByActive($query, $value),
            'email_verified' => $this->filterByEmailVerified($query, $value),
            'created_between' => $this->filterByCreatedBetween($query, $value),
            'tags' => $this->filterByTags($query, $value),
            'has_orders' => $this->filterByHasOrders($query, $value),
            'order_count_min' => $this->filterByOrderCountMin($query, $value),
            'total_spent_min' => $this->filterByTotalSpentMin($query, $value),
            'last_login_after' => $this->filterByLastLoginAfter($query, $value),
            'location' => $this->filterByLocation($query, $value),
            default => $query
        };
    }

    protected function filterByRole(Builder $query, string $role): Builder
    {
        return $query->whereHas('roles', function (Builder $q) use ($role) {
            $q->where('name', $role);
        });
    }

    protected function filterByActive(Builder $query, bool $active): Builder
    {
        if ($active) {
            return $query->whereNotNull('last_login_at')
                ->where('last_login_at', '>=', now()->subMonths(3));
        }
        return $query;
    }

    protected function filterByEmailVerified(Builder $query, bool $verified): Builder
    {
        if ($verified) {
            return $query->whereNotNull('email_verified_at');
        }
        return $query->whereNull('email_verified_at');
    }

    protected function filterByCreatedBetween(Builder $query, array $dates): Builder
    {
        if (count($dates) === 2) {
            return $query->whereBetween('created_at', $dates);
        }
        return $query;
    }

    protected function filterByTags(Builder $query, array $tags): Builder
    {
        return $query->whereJsonContains('tags', $tags);
    }

    protected function filterByHasOrders(Builder $query, bool $hasOrders): Builder
    {
        if ($hasOrders) {
            return $query->whereHas('orders');
        }
        return $query->whereDoesntHave('orders');
    }

    protected function filterByOrderCountMin(Builder $query, int $minCount): Builder
    {
        return $query->withCount('orders')->having('orders_count', '>=', $minCount);
    }

    protected function filterByTotalSpentMin(Builder $query, float $minAmount): Builder
    {
        return $query->whereHas('orders', function (Builder $q) use ($minAmount) {
            $q->selectRaw('user_id, SUM(total_amount) as total_spent')
                ->groupBy('user_id')
                ->having('total_spent', '>=', $minAmount);
        });
    }

    protected function filterByLastLoginAfter(Builder $query, string $date): Builder
    {
        return $query->where('last_login_at', '>=', $date);
    }

    protected function filterByLocation(Builder $query, string $location): Builder
    {
        return $query->where('address', 'like', "%{$location}%");
    }

    public function getAvailableFilters(): array
    {
        return [
            'role' => 'User Role',
            'active' => 'Active Users Only',
            'email_verified' => 'Email Verified',
            'created_between' => 'Created Between Dates',
            'tags' => 'Has Tags',
            'has_orders' => 'Has Orders',
            'order_count_min' => 'Minimum Order Count',
            'total_spent_min' => 'Minimum Total Spent',
            'last_login_after' => 'Last Login After',
            'location' => 'Location',
        ];
    }

    public function getFilterOptions(): array
    {
        return [
            'roles' => ['admin', 'customer', 'staff'],
            'tags' => ['vip', 'premium', 'new_user', 'loyal'],
            'locations' => ['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Kolkata'],
        ];
    }
}