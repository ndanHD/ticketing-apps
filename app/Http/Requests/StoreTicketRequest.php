<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat tiket.
     * Untuk sekarang kita izinkan semua (no auth middleware required per instruksi).
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Aturan validasi untuk menyimpan tiket baru.
     */
    public function rules()
    {
        return [
            'ticket_type_id' => ['required', 'uuid', 'exists:tbl_ticket_types,id'],
            'sla_id' => ['required', 'uuid', 'exists:tbl_slas,id'],
            'assign_to' => ['nullable', 'uuid', 'exists:tbl_users,id'],
            'created_by' => ['nullable', 'uuid', 'exists:tbl_users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:65535'],
            'priority' => ['nullable', 'in:low,medium,high'],
        ];
    }
}
