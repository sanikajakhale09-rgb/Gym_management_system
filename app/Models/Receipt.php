<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Payment; // 🟢 Payment मॉडेल इथे इम्पोर्ट केले आहे

class Receipt extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // 🟢 नवीन पेमेंट रिलेशन इथे ऍड केले आहे
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}