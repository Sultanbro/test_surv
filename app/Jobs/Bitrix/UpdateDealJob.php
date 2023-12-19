<?php

namespace App\Jobs\Bitrix;

use App\DayType;
use App\Models\Bitrix\Lead;
use App\Models\Bitrix\Segment;
use App\Service\Integrations\BitrixIntegrationService;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateDealJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $deal;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $deal)
    {
        $this->deal = $deal;
    }

    /**
     * Execute the job.
     *
     * @param BitrixIntegrationService $bitrix
     * @return void
     * @throws HttpClientException
     */
    public function handle(BitrixIntegrationService $bitrix): void
    {
        $deal = $this->deal;

        $contact = $bitrix->getContact($deal['CONTACT_ID']);

        /** @var User $user */
        $user = User::query()->where(match (true) {
            isset($contact['EMAIL']) => [['email', $contact['EMAIL'][0]['VALUE']]],
            isset($contact['PHONE']) => [['phone', $contact['PHONE'][0]['VALUE']]],
        })->first();
        if ($user) return;

        $stage_id = $deal['STAGE_ID'];
        $now = now()->setTimezone('Asia/Almaty')->toDateString();

        switch (true) {
            case array_key_exists($stage_id, DayType::STAGE_TO_STATUS):
            {
                # Обучается или пропал
                DayType::query()
                    ->updateOrCreate([
                        'user_id' => $user->getKey(),
                        'date' => $now,
                    ], [
                        'type' => DayType::STAGE_TO_STATUS[$stage_id],
                        'email' => $user->email,
                        'admin_id' => $user->id,
                    ]);

                break;
            }
            case $deal['CLOSED'] == 'Y':
            { # Если не C4:WON значит увольнение
                $group = $user->groups()->first();
                $lead = $user->lead()->first();
                if ($lead) {
                    $lead->update([
                        'status' => 'LOSE',
                        'invited' => 0,
                    ]);
                } else {
                    $lead_data = [
                        'user_id' => $user->getKey(),
                        'invited' => 0,
                        'deal_id' => $deal['ID'],
                        'name' => $user->full_name,
                        'phone' => $user->phone,
                        'status' => 'LOSE',
                        'segment' => Segment::query()->where('name', 'like', '%Уволенные%')
                            ->first()
                            ?->getKey(),
                        'hash' => md5(uniqid() . mt_rand()),
                    ];

                    switch ($user->user_type) {
                        case User::USER_TYPE_OFFICE:
                        {
                            $lead_data['inhouse'] = now();
                            break;
                        }
                        default:
                        case User::USER_TYPE_REMOTE:
                        {
                            $lead_data['skyped'] = now();
                            break;
                        }
                    }

                    if ($group) {
                        $lead_data['project'] = $group->name;
                        $lead_data['invite_at'] = $group->pivot->created_at;
                        $lead_data['invite_group_id'] = $group->getKey();
                    }

                    Lead::query()
                        ->updateOrCreate([
                            'deal_id' => $deal['ID']
                        ],
                            $lead_data
                        );
                }
                break;
            }
        }
    }
}
