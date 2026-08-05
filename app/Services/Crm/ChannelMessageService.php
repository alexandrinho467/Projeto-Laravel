<?php
namespace App\Services\Crm;

use App\Models\CrmChannelMessage;
use App\Models\CrmContact;
use App\Models\CrmDeal;

class ChannelMessageService
{
    public function resolveContactByWhatsapp(string $waId, ?string $profileName = null): CrmContact
    {
        return CrmContact::firstOrCreate(
            ['whatsapp_number' => $waId],
            ['name' => $profileName ?: $waId, 'source' => 'whatsapp', 'status' => 'lead']
        );
    }

    public function resolveContactByInstagram(string $igsid, ?string $username = null): CrmContact
    {
        $contact = CrmContact::firstOrCreate(
            ['instagram_user_id' => $igsid],
            ['name' => $username ?: $igsid, 'instagram_username' => $username, 'source' => 'instagram', 'status' => 'lead']
        );

        if ($username && $contact->instagram_username !== $username) {
            $contact->update(['instagram_username' => $username]);
        }

        return $contact;
    }

    public function resolveContactByEmail(string $email, ?string $name = null): CrmContact
    {
        return CrmContact::firstOrCreate(
            ['email' => $email],
            ['name' => $name ?: $email, 'source' => 'email', 'status' => 'lead']
        );
    }

    /**
     * @param array{channel:string,direction:string,content:string,occurred_at:\DateTimeInterface|string,external_message_id?:?string,subject?:?string,user_id?:?int,crm_deal_id?:?int,raw_payload?:?array} $data
     */
    public function log(CrmContact $contact, array $data): CrmChannelMessage
    {
        $data['crm_contact_id'] = $contact->id;
        $data['user_id']        = $data['user_id'] ?? $contact->assigned_to;
        $data['crm_deal_id']    = $data['crm_deal_id'] ?? $this->latestOpenDeal($contact)?->id;

        if (!empty($data['external_message_id'])) {
            return CrmChannelMessage::firstOrCreate(
                ['channel' => $data['channel'], 'external_message_id' => $data['external_message_id']],
                $data
            );
        }

        return CrmChannelMessage::create($data);
    }

    protected function latestOpenDeal(CrmContact $contact): ?CrmDeal
    {
        return $contact->deals()
            ->whereNotIn('stage', ['ganho', 'perdido'])
            ->latest('stage_changed_at')
            ->first();
    }
}
