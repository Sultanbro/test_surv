<?php

namespace App\Jobs\Bitrix;

use App\Api\HeadHunter;
use App\DTO\Courses\UpdateDealDTO;
use App\Models\Bitrix\Lead;
use App\Service\Department\UserService;
use App\Service\Integrations\BitrixIntegrationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\DayType;
use App\User;

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
     * @return void
     */
    public function handle(BitrixIntegrationService $bitrix, UserService $user_service)
    {
        $deal = $this->deal;

        $contact = $bitrix->getContact($deal['CONTACT_ID']);
        
        $user = User::where(match (true) {
            isset($contact['EMAIL']) => [['email' => $contact['EMAIL']['VALUE']]],
            isset($contact['PHONE']) => [['phone' => $contact['PHONE']['VALUE']]],
        })->first();

        if (is_null($user)) {
            return;
        }
        
        $stage_id = $deal['STAGE_ID'];
        $now = now()->setTimezone('Asia/Almaty')->toDateString();

        switch (true) {
            case $stage_id == 'C4:WON': { # Поступил на работу
                break;
            }
            case array_key_exists($stage_id, DayType::STAGE_TO_STATUS): { # Обучается или пропал
                $day = DayType::where('user_id', $user->getKey())
                    ->where('created_at', $now)
                    ->first();

                if ($day == null) {
                    $day = DayType::create([
                        'user_id' => $user->getKey(),
                        'type' => DayType::STAGE_TO_STATUS[$stage_id],
                        'email' => $user->email,
                        'date' => $now,
                        'admin_id' => $user->id,
                    ]);
                } else {
                    $day->update(['type' => DayType::STAGE_TO_STATUS[$stage_id]]);
                }
        
                break;
            }
            case $deal['CLOSED'] == 'Y': { # Если не C4:WON значит увольнение
                $lead = $user->lead;
                if($lead) {
                    $lead->update([
                        'status' => 'LOSE', 
                        'invited' => 0,
                    ]);
                } else {
                    $lead = Lead::create([
                        'invited' => 0,
                        'deal_id' => $deal['ID'],
                        'name' => $user->full_name,
                        'phone' => $user->phone,
                        'status' => 'LOSE',
                        'segment' => Lead::getSegmentAlt(Headhunter::SEGMENT),
                        'hash' => md5(uniqid().mt_rand())
                    ]);
                }
                break;
            }
        }

        return;
    }
}
