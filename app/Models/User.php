<?php
namespace App\Models;

use App\Mail\ResetPasswordMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','id_document','phone','birth_date','role','token','address_cep','address_street','address_number','address_complement','address_neighborhood','address_city','address_state','imap_host','imap_port','imap_username','imap_password','imap_encryption','imap_folder'];
    protected $hidden   = ['password','remember_token','imap_password'];
    protected $casts    = ['birth_date' => 'date', 'password' => 'hashed', 'imap_password' => 'encrypted'];

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isVendedor(): bool { return $this->role === 'vendedor'; }
    public function hasCrmAccess(): bool { return in_array($this->role, ['admin', 'vendedor'], true); }
    public function hasEmailSyncConfigured(): bool { return !empty($this->imap_host); }

    public function orders() { return $this->hasMany(Order::class); }
    public function assignedContacts() { return $this->hasMany(CrmContact::class, 'assigned_to'); }
    public function assignedDeals() { return $this->hasMany(CrmDeal::class, 'assigned_to'); }
    public function crmActivities() { return $this->hasMany(CrmActivity::class); }
    public function channelMessages() { return $this->hasMany(CrmChannelMessage::class); }

    public function sendPasswordResetNotification($token): void
    {
        $url = route('senha.redefinir.form', ['token' => $token, 'email' => $this->email]);
        Mail::to($this->email)->send(new ResetPasswordMail($url, $this->name));
    }
}
