<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use SoftDeletes, LogsActivity;

    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAWAL = 'withdrawal';
    public const TYPE_PAYMENT = 'payment';

    public const DEPOSIT_TYPE_AUTO = 'auto';
    public const DEPOSIT_TYPE_MANUAL = 'manual';

    public const STATUS_TEXT_PROCESSING = 100;
    public const STATUS_TEXT_COMPLETED = 101;
    public const STATUS_TEXT_CANCELLED = 102;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'customer_id',
        'type',
        'deposit_type',
        'amount',
        'balance',
        'description',
        'raw_date',
        'raw_content',
        'image',
        'note',
        'telegram_message_id',
        'status',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'status_text',
        'status_text_css_class',
        'css_class',
        'mark',
        'datetime',
        'telegram_message_text',
        'amount_format',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusTextAttribute()
    {
        if ($this->status === self::STATUS_TEXT_PROCESSING) {
            return 'Đang xử lý';
        } else if ($this->status === self::STATUS_TEXT_COMPLETED) {
            return 'Đã hoàn thành';
        } else if ($this->status === self::STATUS_TEXT_CANCELLED) {
            return 'Đã hủy';
        }

        return null;
    }

    public function getStatusTextCssClassAttribute()
    {
        if ($this->status === self::STATUS_TEXT_PROCESSING) {
            return 'text-blue fw-bolder';
        } else if ($this->status === self::STATUS_TEXT_COMPLETED) {
            return 'text-green fw-bolder';
        } else if ($this->status === self::STATUS_TEXT_CANCELLED) {
            return 'text-red fw-bolder';
        }
    }

    public function getCssClassAttribute()
    {
        if ($this->type === self::TYPE_DEPOSIT) {
            return 'text-green fw-bold';
        } else if ($this->type === self::TYPE_WITHDRAWAL) {
            return 'text-red fw-bold';
        } else if ($this->type === self::TYPE_PAYMENT) {
            return 'text-orange fw-bold';
        }
    }

    public function getMarkAttribute()
    {
        if ($this->type === self::TYPE_DEPOSIT) {
            return '+';
        } else {
            return '-';
        }
    }

    public function getDatetimeAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->updated_at)->format('d-m-Y H:i:s');
    }

    public function getTelegramMessageTextAttribute()
    {
        $image = "Hình ảnh: [Link](" . asset('files/' . $this->image) . ")";

        if ($this->type === self::TYPE_DEPOSIT && $this->deposit_type === self::DEPOSIT_TYPE_AUTO) {
            $image = 'Autobank';
        }

        return
            "Invoice : `{$this->id}`*" . PHP_EOL .
            "Mã khách hàng : `{$this->customer->code}`*" . PHP_EOL .
            "Tên khách hàng: `{$this->customer->name}`*" . PHP_EOL .
            'Số tiền nạp: `' . number_format($this->amount, 0, '', '.') . '`*' . PHP_EOL .
            $image . PHP_EOL .
            "Trạng thái: " . __('app.transaction.status.' . $this->status);
    }

    public function getAmountFormatAttribute()
    {
        return number_format($this->amount, 0, ',', '.');
    }

    public function getQrLinkAttribute()
    {
        return genQRQuickLink($this->amount, $this->description);
    }

    public function getCodeAttribute()
    {
        if ($this->type === self::TYPE_DEPOSIT && $this->deposit_type === self::DEPOSIT_TYPE_AUTO) {
            return "{$this->raw_date}-{$this->description}-{$this->amount}";
        }

        return $this->id;
    }

    public function getTypeTextAttribute()
    {
        if ($this->type === self::TYPE_DEPOSIT && $this->deposit_type === self::DEPOSIT_TYPE_AUTO) {
            return "Nạp tiền - Auto";
        }

        if ($this->type === self::TYPE_DEPOSIT) {
            return "Nạp tiền";
        } elseif ($this->type === self::TYPE_WITHDRAWAL) {
            return "Rút tiền";
        } elseif ($this->type === self::TYPE_PAYMENT) {
            return "Thanh toán công nợ";
        } else {
            return null;
        }
    }

    public function getCreatedByAttribute()
    {
        if (!is_null($this->user)) {
            return $this->user->email;
        }

        if ($this->type === self::TYPE_DEPOSIT && $this->deposit_type === self::DEPOSIT_TYPE_AUTO) {
            return "Autobank";
        }

        return null;
    }
}
