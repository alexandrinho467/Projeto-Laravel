<?php
namespace App\Livewire\Admin;

use App\Models\CrmContact;
use App\Models\Order;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $q = '';

    public function render()
    {
        $contacts = collect();
        $orders   = collect();

        $term = trim($this->q);
        $isNumeric = ctype_digit($term);

        if (strlen($term) >= 2 || ($isNumeric && $term !== '')) {
            $user = auth()->user();

            if (strlen($term) >= 2) {
                $contactsQuery = CrmContact::query()
                    ->where(function ($query) use ($term) {
                        $query->where('name', 'like', "%{$term}%")
                              ->orWhere('email', 'like', "%{$term}%")
                              ->orWhere('phone', 'like', "%{$term}%");
                    })
                    ->limit(6);

                if ($user->isVendedor()) {
                    $contactsQuery->where('assigned_to', $user->id);
                }

                $contacts = $contactsQuery->get();
            }

            $orders = Order::query()
                ->where(function ($query) use ($term, $isNumeric) {
                    if ($isNumeric) $query->where('id', $term);
                    $query->orWhere('guest_name', 'like', "%{$term}%")
                          ->orWhere('guest_email', 'like', "%{$term}%");
                })
                ->limit(6)
                ->get();
        }

        return view('livewire.admin.global-search', [
            'contacts' => $contacts,
            'orders'   => $orders,
        ]);
    }
}
