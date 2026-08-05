<?php
namespace App\Livewire\Admin\Crm;

use Livewire\Component;

class EmailSettings extends Component
{
    public string $imapHost = '';
    public string $imapPort = '993';
    public string $imapUsername = '';
    public string $imapPassword = '';
    public string $imapEncryption = 'ssl';
    public string $imapFolder = 'INBOX';

    public bool $hasStoredPassword = false;
    public ?bool $testResult = null;
    public string $testMessage = '';

    public function mount()
    {
        $user = auth()->user();

        $this->imapHost = $user->imap_host ?? '';
        $this->imapPort = (string) ($user->imap_port ?? '993');
        $this->imapUsername = $user->imap_username ?? '';
        $this->imapEncryption = $user->imap_encryption ?? 'ssl';
        $this->imapFolder = $user->imap_folder ?? 'INBOX';
        $this->hasStoredPassword = !empty($user->imap_password);
    }

    protected function rules(): array
    {
        return [
            'imapHost'       => 'nullable|string|max:255',
            'imapPort'       => 'nullable|integer|min:1|max:65535',
            'imapUsername'   => 'nullable|string|max:255',
            'imapPassword'   => 'nullable|string|max:255',
            'imapEncryption' => 'required|in:ssl,tls,none',
            'imapFolder'     => 'nullable|string|max:255',
        ];
    }

    public function save()
    {
        $data = $this->validate();

        $user = auth()->user();

        $user->imap_host       = $data['imapHost'] ?: null;
        $user->imap_port       = $data['imapPort'] ? (int) $data['imapPort'] : null;
        $user->imap_username   = $data['imapUsername'] ?: null;
        $user->imap_encryption = $data['imapEncryption'];
        $user->imap_folder     = $data['imapFolder'] ?: 'INBOX';

        if (!empty($data['imapPassword'])) {
            $user->imap_password = $data['imapPassword'];
        }

        $user->save();

        $this->imapPassword = '';
        $this->hasStoredPassword = !empty($user->imap_password);
        $this->testResult = null;

        session()->flash('success', 'Configurações de e-mail salvas!');
    }

    public function disconnect()
    {
        auth()->user()->update([
            'imap_host'       => null,
            'imap_port'       => null,
            'imap_username'   => null,
            'imap_password'   => null,
            'imap_encryption' => null,
            'imap_folder'     => 'INBOX',
        ]);

        $this->imapHost = '';
        $this->imapUsername = '';
        $this->imapPassword = '';
        $this->hasStoredPassword = false;
        $this->testResult = null;

        session()->flash('success', 'Sincronização de e-mail desativada.');
    }

    public function testConnection()
    {
        if (!function_exists('imap_open')) {
            $this->testResult = false;
            $this->testMessage = 'A extensão IMAP do PHP não está habilitada neste servidor.';
            return;
        }

        $host = $this->imapHost;
        $port = $this->imapPort ?: 993;
        $username = $this->imapUsername;
        $password = $this->imapPassword ?: auth()->user()->imap_password;
        $folder = $this->imapFolder ?: 'INBOX';

        if (!$host || !$username || !$password) {
            $this->testResult = false;
            $this->testMessage = 'Preencha servidor, usuário e senha antes de testar.';
            return;
        }

        $encryption = match($this->imapEncryption) {
            'ssl' => '/imap/ssl',
            'tls' => '/imap/tls',
            default => '/imap/novalidate-cert',
        };

        $mailbox = "{{$host}:{$port}{$encryption}}{$folder}";

        $conn = @imap_open($mailbox, $username, $password);

        if ($conn) {
            $this->testResult = true;
            $this->testMessage = 'Conexão bem-sucedida!';
            imap_close($conn);
        } else {
            $this->testResult = false;
            $this->testMessage = 'Falha na conexão: ' . imap_last_error();
        }
    }

    public function render()
    {
        return view('livewire.admin.crm.email-settings')
            ->extends('layouts.admin', ['title' => 'Meu E-mail | CRM']);
    }
}
