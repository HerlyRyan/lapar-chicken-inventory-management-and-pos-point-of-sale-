<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'is_active',
        'type'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function branchStocks()
    {
        return $this->hasMany(BranchStock::class);
    }

    public function materialStocks()
    {
        return $this->hasMany(BranchStock::class)->materials();
    }

    public function finishedProductStocks()
    {
        return $this->hasMany(BranchStock::class)->finishedProducts();
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function managedMaterials()
    {
        return $this->hasMany(Material::class, 'managing_branch_id');
    }

    public function managedFinishedProducts()
    {
        return $this->hasMany(FinishedProduct::class, 'managing_branch_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope only production centers
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProduction($query)
    {
        return $query->where('type', 'production');
    }

    /**
     * Scope only retail branches
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRetail($query)
    {
        return $query->where('type', 'branch');
    }

    // Helper methods
    public function getStatusLabel()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    public function getWhatsAppLink($message = null)
    {
        if (!$this->phone) {
            return null;
        }

        // Format nomor telepon untuk WhatsApp (hapus karakter non-digit)
        $phoneNumber = preg_replace('/[^0-9]/', '', $this->phone);
        
        // Jika nomor dimulai dengan 0, ganti dengan 62
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '62' . substr($phoneNumber, 1);
        }
        
        // Jika nomor tidak dimulai dengan 62, tambahkan 62
        if (substr($phoneNumber, 0, 2) !== '62') {
            $phoneNumber = '62' . $phoneNumber;
        }

        $defaultMessage = "Halo, saya ingin menghubungi cabang {$this->name}";
        $message = $message ?: $defaultMessage;
        
        return "https://wa.me/{$phoneNumber}?text=" . urlencode($message);
    }

    public function getFormattedPhone()
    {
        if (!$this->phone) {
            return null;
        }

        // Format nomor telepon dengan pemisah
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        
        if (strlen($phone) >= 10) {
            return preg_replace('/(\d{4})(\d{4})(\d+)/', '$1-$2-$3', $phone);
        }
        
        return $this->phone;
    }
}
