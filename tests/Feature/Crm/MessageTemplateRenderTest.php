<?php

namespace Tests\Feature\Crm;

use App\Models\CrmContact;
use App\Models\CrmDeal;
use App\Models\CrmMessageTemplate;
use Tests\TestCase;

class MessageTemplateRenderTest extends TestCase
{
    public function test_renders_all_variables(): void
    {
        $contact = CrmContact::factory()->create(['name' => 'Mohamed']);
        $deal = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'title' => 'Air Jordan 1', 'value' => 1200]);

        $template = CrmMessageTemplate::factory()->create([
            'body' => 'Hi {{cliente}}, your {{sneaker}} is ready for {{valor}}!',
        ]);

        $result = $template->render($contact, $deal);

        $this->assertSame('Hi Mohamed, your Air Jordan 1 is ready for AED 1,200.00!', $result);
    }

    public function test_missing_deal_leaves_deal_variables_empty_without_error(): void
    {
        $contact = CrmContact::factory()->create(['name' => 'Sara']);
        $template = CrmMessageTemplate::factory()->create(['body' => 'Hi {{cliente}}, about your {{sneaker}}']);

        $result = $template->render($contact);

        $this->assertSame('Hi Sara, about your ', $result);
    }

    public function test_uses_most_recent_deal_when_none_given(): void
    {
        $contact = CrmContact::factory()->create();
        CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'title' => 'Older Deal', 'created_at' => now()->subDays(2)]);
        $recent = CrmDeal::factory()->create(['crm_contact_id' => $contact->id, 'title' => 'Recent Deal']);

        $template = CrmMessageTemplate::factory()->create(['body' => '{{sneaker}}']);

        $this->assertSame('Recent Deal', $template->render($contact));
    }

    public function test_unknown_variable_is_left_untouched(): void
    {
        $contact = CrmContact::factory()->create(['name' => 'Ana']);
        $template = CrmMessageTemplate::factory()->create(['body' => 'Hi {{cliente}}, {{desconhecido}}']);

        $this->assertSame('Hi Ana, {{desconhecido}}', $template->render($contact));
    }
}
