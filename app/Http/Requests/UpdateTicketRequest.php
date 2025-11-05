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
        return [
            'ticket_type_id' => ['required', 'string'],
            'sla_id' => ['required', 'string'],
            'assign_to' => ['nullable', 'exists:tbl_users,id'],
            'detail' => ['required', 'string', 'max:65535'],
            'status' => ['nullable', 'in:open,closed,pending,resolved'],
        ];
    }
}
