<?php

namespace App\Models\Admin;

use App\Models\Admin\ObtainedBonus;
use Illuminate\Database\Eloquent\Model;
use App\Models\Analytics\Activity;
use App\User;
use App\Salary;
use App\AnalyticsSettingsIndividually;
use Carbon\Carbon;
use App\ProfileGroup;
use App\Models\Analytics\UserStat;
use App\Models\Analytics\RecruiterStat;
use DB;

class Bonus extends Model
{   
    protected $table = 'kpi_bonuses';

    public $timestamps = false;

    /**
     * Unit 
     */
    CONST FOR_ONE = 'one';
    CONST FOR_ALL = 'all';

    /**
     * Daypart
     */
    CONST FULL_DAY = 0;
    CONST FIRST_HALF = 1;
    CONST SECOND_HALF = 2;

    protected $fillable = [
        'title',
        'sum',
        'group_id',
        'activity_id',
        'unit',
        'quantity',
        'daypart',
        'text',
    ];

    
    public static function obtained_in_group($group_id, $date) {
        $group = ProfileGroup::find($group_id);
        
        $user_ids = json_decode($group->users);
        $bonuses = self::where('group_id', $group_id)->get();
        
        $awards = []; // bonuses
        $comments = []; // bonuses
        
        $users = \DB::table('users')
            ->whereNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->whereIn('users.id', $user_ids)
            ->where('is_trainee', 0)
            ->get(['users.id'])
            ->pluck('id')
            ->toArray();

        foreach ($users as $user_id) { //  fill $awards array
            $awards[$user_id] = 0;
            $comments[$user_id] = '';
        }
        
  
        foreach ($bonuses as $bonus) {
            
            if($bonus->sum == 0) continue;
            if($bonus->activity_id == 0) continue;
            
            if($bonus->unit == self::FOR_ALL) {
        
                $best_user = 0;
                $best_value = 0;

                if(in_array($group_id, [53,57])) {
                    $best_user = self::fetch_best_user_from_callibro($bonus, $group_id, $date);
                    // save award to the best user
                    if($best_user != 0) {
                        ObtainedBonus::createOrUpdate([
                            'user_id' => $best_user,
                            'date' => $date,
                            'bonus_id' => $bonus->id,
                            'amount' => $bonus->sum,
                            'comment' => $bonus->title . ' : ' . $best_value . ';'
                        ]);
                    } 

                } else {

            
                    foreach ($users as $user_id) {
                        if($group_id == 48) {
                            $val = self::fetch_value_from_activity_for_recruting($bonus->activity_id, $user_id, $date, $bonus->daypart);
                        } else {
                            $val = self::fetch_value_from_activity_new($bonus->activity_id, $user_id, $date);
                        }
                        
                        if((int)$val >= $bonus->quantity && (int)$val >= $best_value) {
                            $best_user = $user_id;
                            $best_value = (int)$val;
                        }
                    }

                    // nullify awards if they are not actual
                    ObtainedBonus::where('bonus_id', $bonus->id)
                        ->where('date', $date)
                        ->delete();

                    // save award to the best user
                    if($best_user != 0 && (int)$best_value >= $bonus->quantity) {
                        ObtainedBonus::createOrUpdate([
                            'user_id' => $best_user,
                            'date' => $date,
                            'bonus_id' => $bonus->id,
                            'amount' => $bonus->sum,
                            'comment' => $bonus->title . ' : ' . $best_value . ';'
                        ]);
                    } 
                }
                
            }
            
            if($bonus->unit == self::FOR_ONE) {
                foreach ($users as $user_id) {
                    
                    if($group_id == 48) {
                        $val = self::fetch_value_from_activity_for_recruting($bonus->activity_id, $user_id, $date);
                    } else if(in_array($group_id, [53,57]) && in_array($bonus->activity_id, [16,37,18,38])) { // Минуты и согласия
                        $val = self::fetch_value_from_callibro($bonus, $group_id, $date, $user_id);
                       // dump($val);
                    } else {
                        $val = self::fetch_value_from_activity_new($bonus->activity_id, $user_id, $date);
                    }

                    $summy = (float)$val * $bonus->sum;
                    //if($summy > 0) {
                        ObtainedBonus::createOrUpdate([
                            'user_id' => $user_id,
                            'date' => $date,
                            'bonus_id' => $bonus->id,
                            'amount' => (int)$summy,
                            'comment' => $bonus->title . ' : ' . $val . ';'
                        ]);
                   // }
                    
                    
                }
            }
        }

 
        // Save awards to salaries table 
        // foreach ($awards as $user_id => $award) {
        //     $salary = Salary::where([
        //         'date' => $date,
        //         'user_id' => $user_id
        //     ])->first();
            
        //     $comm = $comments[$user_id];
        //     if($salary) {
        //         $salary->comment_award = mb_substr($comm,0,255);
        //         $salary->award = (int)$salary->award  + (int)$award;
        //         $salary->update();
        //     } else {
        //         Salary::create([
        //             'user_id' => $user_id,
        //             'award' => $award,
        //             'comment_award' => mb_substr($comm,0,255),
        //             'date' => $date,
        //             'bonus' => 0,
        //             'paid' => 0,
        //             'amount' => 0,
        //         ]);
        //     }
        // }

        return $awards;
    }

    /**
     * Fetch value number from callibro
     */
    public static function fetch_value_from_callibro($bonus, $group_id, $date, $user_id) {
        $vars = self::prepare_callibro_vars($bonus, $group_id, $date);

        // FIND USER
        $user = User::withTrashed()->find($user_id);
        if(!$user) return 0;

        $account = DB::connection('callibro')->table('call_account')
            ->where('email', $user->email)
            ->first();

        if(!$account) return 0;
        $vars['account_id'] = $account->id;

        // get calls
        $items = self::callibro_query($vars);
        
        return $items->count();
    }

    /**
     * Prepare vars for calls table in callibro
     */
    public static function prepare_callibro_vars($bonus, $group_id, $date) {
        $vars = [];
        if($bonus->daypart == 1) {
            $vars['start_date'] = $date . ' 09:00:00';
            $vars['end_date'] = $date . ' 13:00:00';
        } else if($bonus->daypart == 2) {
            $vars['start_date'] = $date . ' 14:00:00';
            $vars['end_date'] = $date . ' 19:00:00';
        } else if($bonus->daypart == 3) {
            $vars['start_date'] = $date . ' 19:00:00';
            $vars['end_date'] = $date . ' 23:00:00';
        } else {
            $vars['start_date'] = $date . ' 00:00:00';
            $vars['end_date'] = $date . ' 23:59:59';
        }

        if($group_id == 53) {
            $vars['dialer_id'] = 398;
            $vars['script_status_ids'] = [2519]; // Cтатус в скрипте: Дата Визита
        }

        if($group_id == 57) {
            $vars['dialer_id'] = 250;
            $vars['script_status_ids'] = [
                13111,13112
            ]; 
        }

        $vars['type'] = 'calls';
        if(in_array($bonus->activity_id, [18,38])) {
            $vars['type'] = 'aggrees';
        }

        return $vars;
    }
    /**
     *  query to calls table
     */
    public static function callibro_query($vars) {
        if($vars['type'] == 'calls') {
            $items = DB::connection('callibro')->table('calls')
                    ->select('call_account_id as account_id', 'billsec', 'start_time')
                    ->whereBetween('start_time', [$vars['start_date'], $vars['end_date']])
                    ->where('billsec', '>=', 10)
                    ->where('call_dialer_id', $vars['dialer_id'])
                    ->where('cause', '!=', 'SYSTEM_SHUTDOWN');
        } else { // aggrees
            $items = DB::connection('callibro')->table('calls')
                ->select('call_account_id as account_id', 'billsec', 'start_time')
                ->whereBetween('start_time', [$vars['start_date'], $vars['end_date']])
                ->where('correct_or_not', '!=', 2)
                ->where('call_dialer_id', $vars['dialer_id'])
                ->whereIn('script_status_id', $vars['script_status_ids']);
        }

        if(array_key_exists('account_id', $vars)) {
            $items = $items->where('call_account_id', $vars['account_id']);
        }

        return $items->get();
    }

    /**
     * String $type 'calls' or 'aggrees'
     * Bonus $bonus
     * int $group_id
     * String $date Y-m-d
     */
    public static function fetch_best_user_from_callibro($bonus, $group_id, $date) {

        $vars = self::prepare_callibro_vars($bonus, $group_id, $date);

        $items = self::callibro_query($vars);
        
        // get leader
        $callibro_account_id = self::getLeader($items, $bonus->quantity);


        // find leadr email
        $leader = DB::connection('callibro')->table('call_account')
            ->find($callibro_account_id);

        // find user id in lara
        $leader_id = 0;
        if($leader) {
            $user = User::withTrashed()->where('email', $leader->email)->first();
            if($user) {
               $leader_id = $user->id; 
            }
        }
        
        return $leader_id;
    }

    /**
     * Determine who first reached the quantity from collection of calls
     */
    public static function getLeader($calls, $quantity) {

		$accounts = [];
		$last_calls = [];

		foreach ($calls as $call) {
			if(!array_key_exists($call->account_id, $accounts)) $accounts[$call->account_id] = 0;
			$accounts[$call->account_id]++;
			if($accounts[$call->account_id] <= $quantity) {
				$last_calls[$call->account_id] = $call->start_time;
			}
		}

		$filteredAccounts = array_filter($accounts, function($value) use ($quantity){
			return ($value >= $quantity);
		}); 
		
		$keys = array_keys($filteredAccounts);
		
		$filteredLastCalls = [];
		foreach($last_calls as $account_id => $last_call) {
			if(in_array($account_id, $keys)) {
				$filteredLastCalls[$account_id] = $last_call; 
			}  
		} 

		$first = 0; 
		$first_time = 0; 

		if(count($filteredAccounts) > 0) {
			foreach($filteredLastCalls as $account_id => $time) {
				$time = Carbon::parse($time)->timestamp;
				if($first_time == 0 || $time < $first_time) {
					$first_time = $time;
					$first = $account_id;
				}
			}
		}

        return $first;
	}  

    /**
     * Fetch value from AnalyticsSettingsIndividually
     */
    public static function fetch_value_from_activity($activity_id, $user_id, $date) {
        if($activity_id == 0) return 0;
        
        $date = Carbon::parse($date);
        $day = $date->day;
        $date->startOfMonth();

        $activity = AnalyticsSettingsIndividually::where([
            'date' => $date->format('Y-m-d'),
            'employee_id' => $user_id,
            'type' => $activity_id
        ])->first();

        if($activity) { 
            $data = json_decode($activity->data, true);
            if(array_key_exists($day, $data)) {
                return $data[$day];
            }
        } 
            
        return 0;
    }

    /**
     * Fetch value from UserStat
     */
    public static function fetch_value_from_activity_new($activity_id, $user_id, $date) {
        if($activity_id == 0) return 0;
    
        $stat = UserStat::where('date', $date)
            ->where('user_id', $user_id)
            ->where('activity_id', $activity_id)
            ->first();
        
        return $stat ? $stat->value : 0;
    }

    /**
     * Fetch value from AnalyticsSettingsIndividually for recruting TEMPORARY
     */
    public static function fetch_value_from_activity_for_recruting($activity_id, $user_id, $date, $daypart = 0) {
        $indexes = [
            22 => 0,
            45 => 1,
            49 => 2,
            50 => 3,
            51 => 4,
            52 => 5,
            53 => 6,
            54 => 7,
        ]; // activity => index


        if(!array_key_exists($activity_id, $indexes)) return 0;
        
        
        if($activity_id == 45) {
            
            $records = RecruiterStat::where('date', $date)
                ->where('calls', '>', 0)
                ->where('user_id', $user_id);
             
            if($daypart == 1) {
                $records = $records->where('hour', '>=', 9)->where('hour', '<', 13);
            }

            if($daypart == 2) {
                $records = $records->where('hour', '>=', 14)->where('hour', '<', 19);
            }
            
            return $records->get()->sum('calls');
        }
        
        $date = Carbon::parse($date);
        $day = $date->day;
        $date->startOfMonth();

        $activity = AnalyticsSettingsIndividually::where([
            'date' => $date->format('Y-m-d'),
            'employee_id' => $user_id,
        ])->first();

        if($activity) { 
            $data = json_decode($activity->data, true);
            
            $index = $indexes[$activity_id];
            if(array_key_exists($index, $data) && array_key_exists($day, $data[$index])) {
                return $data[$index][$day];
            }
        } 
            
        
        return 0;
    }

    public static function getPotentialBonusesHtml($group_id) {
        // 'title',
        // 'sum',
        // 'group_id',
        // 'activity_id',
        // 'unit',
        // 'quantity',
        // 'daypart',

        $group = ProfileGroup::find($group_id);

        $bonuses = self::where('group_id', $group_id)->get();

        $html = '<b>';
        $html .= $group ? $group->name : 'Группа';
        $html .= '</b><br>';

        //me($bonuses);
        if($bonuses->count() > 0) {
            foreach ($bonuses as $key => $bonus) {
                $html .= '- <b>'. $bonus->sum . ' KZT:</b> ' .  $bonus->text . '<br>';  
            }
        } else {
            $html .= 'К сожалению, пока по данному проекту не предусмотрены бонусы<br>';
        }

        return $html;
        
    }
}
