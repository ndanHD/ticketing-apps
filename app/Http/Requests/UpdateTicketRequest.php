<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Izinkan update untuk saat ini.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Aturan validasi untuk update tiket.
     */
    public function rules()
    {
        $rules = [
            // Allow partial updates: ticket_type_id and sla_id are optional when only updating status
            'ticket_type_id' => ['nullable', 'uuid', 'exists:tbl_ticket_types,id'],
            'sla_id' => ['nullable', 'uuid', 'exists:tbl_slas,id'],
            'assign_to' => ['nullable', 'exists:tbl_users,id'],
            // description can be nullable for partial updates (e.g., status change via modal)
            'description' => ['nullable', 'string', 'max:65535'],
            'status' => ['nullable', 'in:open,closed,pending,resolved'],
            'pending_reason' => ['nullable', 'string', 'max:65535'],
            'pending_until' => ['nullable', 'date'],
        ];

        // If the incoming status is pending and the current user is an admin,
        // require pending_reason and pending_until.
        $status = $this->input('status');
        $user = auth()->user();
        if ($status === 'pending' && $user && method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            $rules['pending_reason'] = ['required', 'string', 'max:65535'];
            $rules['pending_until'] = ['required', 'date', 'after_or_equal:today'];
        }

        return $rules;
    }
}
