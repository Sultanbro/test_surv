<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Payment;
use App\SmsDailyReport;
use App\User;
use App\Partner;
use App\PartnerRate;
use App\PartnerUsers;
use App\PartnerInfo;
use App\PartnerPayment;
use App\PartnerInvoice;
use App\PartnerFile;
use App\Setting;
use App\Tarrif;
use App\RentNumber;
use App\MessageTarrif;
use App\Notification;
use App\Autocall;
use App\Statistic;
use App\Contact;
use App\Jobs\ProcessEmail;
use App\GetResponceApi;
use Illuminate\Http\Request;
use App\Mail as Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use File;


class IndexController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        View::share('title', 'Админ панель');
        $this->middleware('auth');
        $this->middleware('admin');

        // $this->middleware('admin.basic.auth', ['only' => [
        //     'balance',
        //     'sip',
        //     'report',
        //     'balanceUpdate',
        // ]]);
/*
        $notifications = [
            'exchangers' => DB::connection('infobank')->table('exchangers')->where('status', 0)->count(),
            'posts' => DB::connection('infobank')->table('posts')->where('status', 0)->count(),
            'glossary' => DB::connection('infobank')->table('glossary')->where('status', 0)->count(),
            'reviews' => DB::connection('infobank')->table('reviews')->where('status', 0)->count() + DB::connection('infobank')->table('exchanger_reviews')->where('status', 0)->count(),
            'comments' => DB::connection('infobank')->table('comments')->where('status', 0)->count(),
        ];

        View::share('notifications', $notifications);*/
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::bitrixUser();
        return redirect('/');
    }

    public function timetracking(Request $request, $domain, $tld, $type = null)
    {
        return view('admin.timetracking');
    }


    public function settingtimetracking()
    {
        return view('admin.settingtimetracking');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request, $domain, $tld, $type = null)
    {
        View::share('title', SmsDailyReport::type($type));

        $year = $request->has('year') ? (int)$request->year : (int)date('Y');
        $month = $request->has('month') ? (int)$request->month : (int)date('n');
        $show_days = date('j');

        if($year!=(int)date('Y') || $month!=(int)date('n')) {
            $show_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }


        if($type=='user') {
            $stats = DB::select("SELECT IFNULL(users.email, 0) as email, gate, SUM(count) as count, DAY(day) as day
							FROM sms_gate_daily_report
							LEFT JOIN users ON users.ID = user_id
							WHERE  YEAR(day) = ? AND MONTH(day) = ?  GROUP BY email, gate, day  ORDER BY gate", [$year,$month]);


            $gates = [];

            if(!isset($gates['TOTAL'])) {
                $gates['TOTAL'] = [
                    SmsDailyReport::$beeline => 0,
                    SmsDailyReport::$beeline_gate => 0,
                    SmsDailyReport::$kcell => 0,
                    SmsDailyReport::$kcell_gate => 0,
                    SmsDailyReport::$tele2_gate => 0,
                    SmsDailyReport::$altel_gate => 0,
                ];
            }
            foreach ($stats as $stat) {
                if(!isset($gates[$stat->email][$stat->day])) {
                    $gates[$stat->email][$stat->day] = [
                        SmsDailyReport::$beeline => 0,
                        SmsDailyReport::$beeline_gate => 0,
                        SmsDailyReport::$kcell => 0,
                        SmsDailyReport::$kcell_gate => 0,
                        SmsDailyReport::$tele2_gate => 0,
                        SmsDailyReport::$altel_gate => 0,
                    ];
                }
                if(!isset($gates['TOTAL'][$stat->day])) {
                    $gates['TOTAL'][$stat->day] = [
                        SmsDailyReport::$beeline => 0,
                        SmsDailyReport::$beeline_gate => 0,
                        SmsDailyReport::$kcell => 0,
                        SmsDailyReport::$kcell_gate => 0,
                        SmsDailyReport::$tele2_gate => 0,
                        SmsDailyReport::$altel_gate => 0,
                    ];
                }
                if(!isset($gates['TOTAL'][$stat->email])) {
                    $gates['TOTAL'][$stat->email] = [
                        SmsDailyReport::$beeline => 0,
                        SmsDailyReport::$beeline_gate => 0,
                        SmsDailyReport::$kcell => 0,
                        SmsDailyReport::$kcell_gate => 0,
                        SmsDailyReport::$tele2_gate => 0,
                        SmsDailyReport::$altel_gate => 0,
                    ];
                }


                if($stat->gate == SmsDailyReport::$beeline) {
                    $gates[$stat->email][$stat->day][SmsDailyReport::$beeline] += $stat->count;
                    $gates['TOTAL'][$stat->day][SmsDailyReport::$beeline] += $stat->count;
                    $gates['TOTAL'][$stat->email][SmsDailyReport::$beeline] += $stat->count;
                    $gates['TOTAL'][SmsDailyReport::$beeline] += $stat->count;

                } else if($stat->gate == SmsDailyReport::$beeline_gate) {
                    $gates[$stat->email][$stat->day][SmsDailyReport::$beeline_gate] += $stat->count;
                    $gates['TOTAL'][$stat->day][SmsDailyReport::$beeline_gate] += $stat->count;
                    $gates['TOTAL'][SmsDailyReport::$beeline_gate] += $stat->count;

                } else if($stat->gate == SmsDailyReport::$kcell) {
                    $gates[$stat->email][$stat->day][SmsDailyReport::$kcell] += $stat->count;
                    $gates['TOTAL'][$stat->day][SmsDailyReport::$kcell] += $stat->count;
                    $gates['TOTAL'][$stat->email][SmsDailyReport::$kcell] += $stat->count;
                    $gates['TOTAL'][SmsDailyReport::$kcell] += $stat->count;

                }else if($stat->gate == SmsDailyReport::$kcell_gate) {
                    $gates[$stat->email][$stat->day][SmsDailyReport::$kcell_gate] += $stat->count;
                    $gates['TOTAL'][$stat->day][SmsDailyReport::$kcell_gate] += $stat->count;
                    $gates['TOTAL'][$stat->email][SmsDailyReport::$kcell_gate] += $stat->count;
                    $gates['TOTAL'][SmsDailyReport::$kcell_gate] += $stat->count;

                } else if($stat->gate == SmsDailyReport::$tele2_gate) {
                    $gates[$stat->email][$stat->day][SmsDailyReport::$tele2_gate] += $stat->count;
                    $gates['TOTAL'][$stat->day][SmsDailyReport::$tele2_gate] += $stat->count;
                    $gates['TOTAL'][$stat->email][SmsDailyReport::$tele2_gate] += $stat->count;
                    $gates['TOTAL'][SmsDailyReport::$tele2_gate] += $stat->count;

                } else if($stat->gate == SmsDailyReport::$altel_gate) {
                    $gates[$stat->email][$stat->day][SmsDailyReport::$altel_gate] += $stat->count;
                    $gates['TOTAL'][$stat->day][SmsDailyReport::$altel_gate] += $stat->count;
                    $gates['TOTAL'][$stat->email][SmsDailyReport::$altel_gate] += $stat->count;
                    $gates['TOTAL'][SmsDailyReport::$altel_gate] += $stat->count;

                }

            }
            return view('admin.report_user')->with('gates', $gates)
                ->with('show_days', $show_days)
                ->with('year', $year)
                ->with('month', $month);
        }

        if($type == 'old') {
            $type_query = ' type is null AND ';
        } else {
            $type_query = ' type!="' . SmsDailyReport::SMS_FORWARD . '" AND ';
            if ($type == SmsDailyReport::SMS_FORWARD) {
                $type_query = ' type="' . SmsDailyReport::SMS_FORWARD . '" AND ';
            }
        }


        $stats = DB::select("SELECT gate, SUM(count) as count, DAY(day) as day
							FROM sms_gate_daily_report
							WHERE ".$type_query." YEAR(day) = ? AND MONTH(day) = ? GROUP BY gate, day ORDER BY gate ", [$year,$month ]);
        $gates = [];
        foreach ($stats as $stat) {
            if($stat->count == 0) continue;

            if(!isset($gates[$stat->gate][$stat->day])) {
                $gates[$stat->gate][$stat->day] = 0;
            }
            $gates[$stat->gate][$stat->day] += $stat->count;

            if(!isset($gates[$stat->gate]['total'])) {
                $gates[$stat->gate]['total'] = 0;
            }
            $gates[$stat->gate]['total']+=$stat->count;
        }

        return view('admin.report')->with('gates', $gates)
            ->with('show_days', $show_days)
            ->with('year', $year)
            ->with('month', $month);
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function sip(Request $request)
    {
        View::share('title', 'Отчет по SIP(минуты)');

        $year = $request->has('year') ? (int)$request->year : (int)date('Y');
        $month = $request->has('month') ? (int)$request->month : (int)date('n');
        $user_ids = $request->has('user_id') ? $request->user_id : [];

        $show_days = date('j');

        if($year!=(int)date('Y') || $month!=(int)date('n')) {
            $show_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }

        if ($request->isMethod('post')) {

            $data = [];

            $sum = DB::select("SELECT sum(d1_seconds) d1_sum,  sum(d2_seconds) d2_sum, sum(d3_seconds) d3_sum, sum(d4_seconds) d4_sum,
					      sum(d5_seconds) d5_sum, sum(d6_seconds) d6_sum, sum(d7_seconds) d7_sum, sum(d8_seconds) d8_sum,
					      sum(d9_seconds) d9_sum, sum(d10_seconds) d10_sum, sum(d11_seconds) d11_sum, sum(d12_seconds) d12_sum,
					      sum(d13_seconds) d13_sum, sum(d14_seconds) d14_sum, sum(d15_seconds) d15_sum, sum(d16_seconds) d16_sum,
					      sum(d17_seconds) d17_sum, sum(d18_seconds) d18_sum, sum(d19_seconds) d19_sum, sum(d20_seconds) d20_sum,
					      sum(d21_seconds) d21_sum, sum(d22_seconds) d22_sum, sum(d23_seconds) d23_sum, sum(d24_seconds) d24_sum,
					      sum(d25_seconds) d25_sum, sum(d26_seconds) d26_sum, sum(d27_seconds) d27_sum, sum(d28_seconds) d28_sum,
					      sum(d29_seconds) d29_sum, sum(d30_seconds) d30_sum, sum(d31_seconds) d31_sum
					FROM sip_daily_report
          WHERE sip_daily_report.yearmonth=".$year.$month."
          ");

            $data[] = [
                '',
                'Итого минут',
                round($sum[0]->d1_sum/60),
                round($sum[0]->d2_sum/60),
                round($sum[0]->d3_sum/60),
                round($sum[0]->d4_sum/60),
                round($sum[0]->d5_sum/60),
                round($sum[0]->d6_sum/60),
                round($sum[0]->d7_sum/60),
                round($sum[0]->d8_sum/60),
                round($sum[0]->d9_sum/60),
                round($sum[0]->d10_sum/60),
                round($sum[0]->d11_sum/60),
                round($sum[0]->d12_sum/60),
                round($sum[0]->d13_sum/60),
                round($sum[0]->d14_sum/60),
                round($sum[0]->d15_sum/60),
                round($sum[0]->d16_sum/60),
                round($sum[0]->d17_sum/60),
                round($sum[0]->d18_sum/60),
                round($sum[0]->d19_sum/60),
                round($sum[0]->d20_sum/60),
                round($sum[0]->d21_sum/60),
                round($sum[0]->d22_sum/60),
                round($sum[0]->d23_sum/60),
                round($sum[0]->d24_sum/60),
                round($sum[0]->d25_sum/60),
                round($sum[0]->d26_sum/60),
                round($sum[0]->d27_sum/60),
                round($sum[0]->d28_sum/60),
                round($sum[0]->d29_sum/60),
                round($sum[0]->d30_sum/60),
                round($sum[0]->d31_sum/60),
            ];


            $offset = $request->has('start') ? (int)$request->start : 0;
            $limit = $request->get('length') ? (int)$request->length : 10;
            $search = $request->get('search') ? $request->search['value'] : '';

            $order = '';
            if(isset($request->order[0]['column'])) {
                $column_index = (int)$request->order[0]['column'];
                $colum = '';
                switch ($column_index) {
                    case 0:
                        $colum = 'id';
                        break;
                    case 1:
                        $colum = 'email';
                        break;
                }

                if($column_index>=2) {
                    $colum = 'd' . ( $column_index - 1 ) . '_seconds';
                }

                $dir = ' ASC';
                if(isset($request->order[0]['dir']) && $request->order[0]['dir']=='desc') {
                    $dir = ' DESC';
                }
                if($colum) {
                    $order = ' ORDER BY '.$colum.$dir;
                }
            }

            $where = '';

            if($search) {
                $where = " AND users.email like '%".trim($search)."%' ";
            }

            if(!empty($user_ids)) {
                $where = " AND users.ID IN (".implode(',', $user_ids).") ";
            }

            $users = DB::select("SELECT SQL_CALC_FOUND_ROWS users.ID, users.email, sip_daily_report.*
								   FROM users
								   LEFT JOIN sip_daily_report ON sip_daily_report.user_id = users.ID
								   WHERE sip_daily_report.yearmonth=".$year.$month."
								    ".$where.$order."
								   LIMIT ? OFFSET ?", [$limit,$offset ]);

            foreach ($users as $user) {
                $data[] = [
                    $user->id,
                    $user->email,
                    round($user->d1_seconds/60),
                    round($user->d2_seconds/60),
                    round($user->d3_seconds/60),
                    round($user->d4_seconds/60),
                    round($user->d5_seconds/60),
                    round($user->d6_seconds/60),
                    round($user->d7_seconds/60),
                    round($user->d8_seconds/60),
                    round($user->d9_seconds/60),
                    round($user->d10_seconds/60),
                    round($user->d11_seconds/60),
                    round($user->d12_seconds/60),
                    round($user->d13_seconds/60),
                    round($user->d14_seconds/60),
                    round($user->d15_seconds/60),
                    round($user->d16_seconds/60),
                    round($user->d17_seconds/60),
                    round($user->d18_seconds/60),
                    round($user->d19_seconds/60),
                    round($user->d20_seconds/60),
                    round($user->d21_seconds/60),
                    round($user->d22_seconds/60),
                    round($user->d23_seconds/60),
                    round($user->d24_seconds/60),
                    round($user->d25_seconds/60),
                    round($user->d26_seconds/60),
                    round($user->d27_seconds/60),
                    round($user->d28_seconds/60),
                    round($user->d29_seconds/60),
                    round($user->d30_seconds/60),
                    round($user->d31_seconds/60),
                ];

            }

            $totalCount = DB::select(DB::raw("SELECT FOUND_ROWS() AS 'total';"))[0]->total;

            $sending = [
                "recordsTotal" => $totalCount,
                "recordsFiltered" => $totalCount,
                "data" => $data
            ];
            return response()->json($sending);
        }

        $users = DB::select("SELECT users.ID, users.email FROM users");


        return view('admin.sip')->with('year', $year)->with('month', $month)->with('show_days', $show_days)->with('users', $users)->with('user_ids', $user_ids);
    }

    public function asterisk(Request $request) {

        $date1 = \Carbon\Carbon::now()->firstOfMonth()->format('Y-m-d');
        $date2 = \Carbon\Carbon::now()->addDay()->format('Y-m-d');
        $year = \Carbon\Carbon::now()->year;
        $month = \Carbon\Carbon::now()->month;

        if($request->year && $request->month) {
            $year = $request->year;
            $month = $request->month;
            $date1 = \Carbon\Carbon::createFromDate($year, $month, 01);
            $date2 = \Carbon\Carbon::createFromDate($year, $month, 01)->endOfMonth()->format('Y-m-d');
        }

        $tele2 = DB::table('reports')->selectRaw("date(date) as date, value")
            ->where('type', 'tele2')
            ->where('date', '>', $date1)
            ->where('date', '<', $date2)
            ->get()->keyBy('date');

        $ktk = DB::table('reports')->selectRaw("date(date) as date, value")
            ->where('type', 'ktk')
            ->where('date', '>', $date1)
            ->where('date', '<', $date2)
            ->get()->keyBy('date');

        $telphin = DB::table('reports')->selectRaw("date(date) as date, value")
            ->where('type', 'telphin')
            ->where('date', '>', $date1)
            ->where('date', '<', $date2)
            ->get()->keyBy('date');

        $hr = DB::connection('asterisk')->table('cdr')->selectRaw("date(calldate) as date, sum(billsec) as billsec")
            ->where('calldate', '>', $date1)
            ->where('calldate', '<', $date2)
            ->where('dstchannel', 'like', '%7470945114%')
            ->groupBy('date')
            ->get()->keyBy('date');

        $dm = DB::connection('asterisk')->table('cdr')->selectRaw("date(calldate) as date, sum(billsec) as billsec")
            ->where('calldate', '>', $date1)
            ->where('calldate', '<', $date2)
            ->where('dstchannel', 'like', '%7072028800%')
            ->groupBy('date')
            ->get()->keyBy('date');

        $bitrix = DB::connection('asterisk')->table('cdr')->selectRaw("date(calldate) as date, sum(billsec) as billsec")
            ->where('calldate', '>', $date1)
            ->where('calldate', '<', $date2)
            ->where('dstchannel', 'like', '%21ktrnng%')
            ->groupBy('date')
            ->get()->keyBy('date');

        $autocalls = DB::connection('asterisk')->table('cdr')->selectRaw("date(calldate) as date, sum(billsec) as billsec")
            ->where('calldate', '>', $date1)
            ->where('calldate', '<', $date2)
            ->where('dstchannel', 'like', '%11ktrening%')
            ->groupBy('date')
            ->get()->keyBy('date');

        $taxi = DB::connection('asterisk')->table('cdr')->selectRaw("date(calldate) as date, sum(billsec) as billsec")
            ->where('calldate', '>', $date1)
            ->where('calldate', '<', $date2)
            ->where('dstchannel', 'like', '%7470941512%')
            ->groupBy('date')
            ->get()->keyBy('date');

        $ucalls = DB::connection('asterisk')->table('cdr')->selectRaw("date(calldate) as date, sum(billsec) as billsec")
            ->where('calldate', '>', $date1)
            ->where('calldate', '<', $date2)
            ->where('dstchannel', 'like', '%7470941513%')
            ->groupBy('date')
            ->get()->keyBy('date');

        $sip = DB::connection('asterisk')->table('cdr')->selectRaw("date(calldate) as date, sum(billsec) as billsec")
            ->where('calldate', '>', $date1)
            ->where('calldate', '<', $date2)
            ->where('dstchannel', 'like', '%77719365396%')
            ->groupBy('date')
            ->get()->keyBy('date');

        $callibro = DB::connection('asterisk')->table('cdr')->selectRaw("date(calldate) as date, sum(billsec) as billsec")
            ->where('calldate', '>', $date1)
            ->where('calldate', '<', $date2)
            ->where('dstchannel', 'like', '%77719365397%')
            ->groupBy('date')
            ->get()->keyBy('date');


        $begin = new \DateTime($date1);
        $end = new \DateTime($date2);

        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($begin, $interval, $end);
        $rows = [];
        $total_tele2 = 0;
        $total_ktk = 0;
        $total_telphin = 0;
        $total_hr = 0;
        $total_dm = 0;
        $total_bitrix = 0;
        $total_autocalls = 0;
        $total_taxi = 0;
        $total_ucalls = 0;
        $total_sip = 0;
        $total_callibro = 0;

        foreach ($period as $dt) {
            $d = $dt->format("Y-m-d");
            $rows[] = [
                $d,
                isset($tele2[$d])?$tele2[$d]->value:'0',
                isset($ktk[$d])?$ktk[$d]->value:'0',
                isset($telphin[$d])?$telphin[$d]->value:'0',
                isset($hr[$d])?$hr[$d]->billsec:'0',
                isset($dm[$d])?$dm[$d]->billsec:'0',
                isset($bitrix[$d])?$bitrix[$d]->billsec:'0',
                isset($autocalls[$d])?$autocalls[$d]->billsec:'0',
                isset($taxi[$d])?$taxi[$d]->billsec:'0',
                isset($ucalls[$d])?$ucalls[$d]->billsec:'0',
                isset($sip[$d])?$sip[$d]->billsec:'0',
                isset($callibro[$d])?$callibro[$d]->billsec:'0',
            ];

            $total_tele2 += isset($tele2[$d])?$tele2[$d]->value:'0';
            $total_ktk += isset($ktk[$d])?$ktk[$d]->value:'0';
            $total_telphin += isset($telphin[$d])?$telphin[$d]->value:'0';
            $total_hr += isset($hr[$d])?$hr[$d]->billsec:'0';
            $total_dm += isset($dm[$d])?$dm[$d]->billsec:'0';
            $total_bitrix += isset($bitrix[$d])?$bitrix[$d]->billsec:'0';
            $total_autocalls += isset($autocalls[$d])?$autocalls[$d]->billsec:'0';
            $total_taxi += isset($taxi[$d])?$taxi[$d]->billsec:'0';
            $total_ucalls += isset($ucalls[$d])?$ucalls[$d]->billsec:'0';
            $total_sip += isset($sip[$d])?$sip[$d]->billsec:'0';
            $total_callibro += isset($callibro[$d])?$callibro[$d]->billsec:'0';
        }

        // array_unshift($rows, ['Total', $total_bitrix, $total_autocalls, $total_sip, $total_callibro]);

        $total = ['Total', $total_tele2, $total_ktk, $total_telphin, $total_hr, $total_dm, $total_bitrix, $total_autocalls, $total_taxi, $total_ucalls, $total_sip, $total_callibro];

        return view('admin.asterisk')->with(['rows' => $rows, 'year' => $year, 'month' => $month, 'total' => $total]);
    }

    public function asteriskSet(Request $request) {

        $value = $request->value;

        if(!$value) {
            abort();
        }

        $date = $request->date;
        $type = $request->type;

        $record = DB::table('reports')->where('type', $type)->where('date', $date)->first();


        if($record) {
            // return 'exist';
            $record->value = $value;
            DB::table('reports')->where('type', $type)->where('date', $date)->update([
                'value' => $value
            ]);
        } else {
            // return 'new';
            DB::table('reports')->insert([
                'type' => $type,
                'date' => $date,
                'value' => $value,
            ]);
        }

        return response()->json([
            'success' => $request->type
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function balance(Request $request)
    {
        $year = $request->has('year') ? (int)$request->year : (int)date('Y');
        $month = $request->has('month') ? (int)$request->month : (int)date('n');
        $user_ids = $request->has('user_id') ? $request->user_id : [];

        $show_days = date('j');

        if($year!=(int)date('Y') || $month!=(int)date('n')) {
            $show_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }

        if ($request->isMethod('post')) {
            $offset = $request->has('start') ? (int)$request->start : 0;
            $limit = $request->get('length') ? (int)$request->length : 10;
            $search = $request->get('search') ? $request->search['value'] : '';

            $order = '';
            if(isset($request->order[0]['column'])) {
                $column_index = (int)$request->order[0]['column'];
                $colum = '';
                switch ($column_index) {
                  case 0:
                  $colum = 'id';
                  break;

                  case 1:
                  $colum = 'email';
                  break;
                        /*case 2:
					    $colum = 'UF_BALANCE';
					    break;*/
                }

                if($column_index>=2) {
                    if($year==(int)date('Y') && $month==(int)date('n')) {
                        $colum = 'UF_BALANCE';
                    } else {
                        $colum = 'd' . ( $column_index - 1 ) . '_balance';
                    }
                }

                $dir = ' ASC';
                if(isset($request->order[0]['dir']) && $request->order[0]['dir']=='desc') {
                    $dir = ' DESC';
                }
                if($colum) {
                    $order = ' ORDER BY '.$colum.$dir;
                }
            }

            $where = '';

            if($search) {
                $where = " AND users.email like '%".trim($search)."%' ";
            }

            if(!empty($user_ids)) {
                $where = " AND users.ID IN (".implode(',', $user_ids).") ";
            }

            $users = DB::select("SELECT SQL_CALC_FOUND_ROWS users.ID, users.created_at, users.email, CAST(users.UF_BALANCE AS  DECIMAL(12,2)) UF_BALANCE, balance_daily_report.*
								   FROM users
								   LEFT JOIN balance_daily_report ON balance_daily_report.user_id = users.ID
								   WHERE (balance_daily_report.yearmonth=".$year.$month." OR balance_daily_report.yearmonth IS NULL)
								    ".$where.$order."
								   LIMIT ? OFFSET ?", [$limit,$offset ]);

            $data = [];

            $day =  date( 'j' );
            foreach ($users as $user) {

                if($year==(int)date('Y') && $month==(int)date('n')) {
                    $user->{'d'.$day.'_balance'} = $user->UF_BALANCE;
                }

                $data[] = [
                    '<input class="delete-checkbox" type="checkbox" value="'.$user->id.'">',
                    $user->id,
                    $user->created_at,
                    $user->email.' <a  target="_blank" href="/login-as/'.$user->id.'">Вход</a> <a href="/balance/update/'.$user->id.'">Добавить баланс</a> <a href="/user/delete/'.$user->id.'" onclick="return confirm(\'Удалить?\')">Удалить</a>',
                    $user->d1_balance,
                    $user->d2_balance,
                    $user->d3_balance,
                    $user->d4_balance,
                    $user->d5_balance,
                    $user->d6_balance,
                    $user->d7_balance,
                    $user->d8_balance,
                    $user->d9_balance,
                    $user->d10_balance,
                    $user->d11_balance,
                    $user->d12_balance,
                    $user->d13_balance,
                    $user->d14_balance,
                    $user->d15_balance,
                    $user->d16_balance,
                    $user->d17_balance,
                    $user->d18_balance,
                    $user->d19_balance,
                    $user->d20_balance,
                    $user->d21_balance,
                    $user->d22_balance,
                    $user->d23_balance,
                    $user->d24_balance,
                    $user->d25_balance,
                    $user->d26_balance,
                    $user->d27_balance,
                    $user->d28_balance,
                    $user->d29_balance,
                    $user->d30_balance,
                    $user->d31_balance,
                ];

            }


            $totalCount = DB::select(DB::raw("SELECT FOUND_ROWS() AS 'total';"))[0]->total;
            //$totalCount = User::count();

            $sending = [
                "recordsTotal" => $totalCount,
                "recordsFiltered" => $totalCount,
                "data" => $data
            ];
            return response()->json($sending);
        }

        $users = DB::select("SELECT users.ID, users.created_at, users.email FROM users");

        View::share('title', 'Баланс пользователей');

        return view('admin.balance')->with('year', $year)->with('month', $month)->with('show_days', $show_days)->with('users', $users)->with('user_ids', $user_ids);
    }

    public function deleteUser(Request $request, $domain, $tld, $id) {

        if($id == 'all') {
            DB::table('users')->whereIn('id', $request->checked)->delete();

            return response()->json([
                'success' => true
            ]);
        }
        $user = User::find($id);
        $user->delete();

        $partner_user = PartnerUsers::where('user_id', $id)->first();

        if($partner_user) {
            $partner_user->delete();
        }

        return redirect()->back();
    }


    public function bonus(Request $request)
    {
        $users = DB::select("SELECT users.ID, users.email, users.name, users.bonus FROM users");

        View::share('title', 'Бонус пользователей');

        return view('admin.bonus')->with('users', $users);
    }

    public function bonusUpdate(Request $request, $domain, $tld, $id)
    {
        View::share('title', 'Бонус пользователей');

        $user = User::user($id);

        if ($request->isMethod('post')) {
            User::updateBonus($request->bonus, $user->id);

            $quantity = ($request->bonus + $user->bonus); // старое значение и новое

            GetResponceApi::ChangedFieldsVal(array(
            'email' => $user->email,
            'fields' => array(
                    [
                        "customFieldId" => "bEuJa", // pole_bonus
                        "value" => [
                            $quantity
                        ]
                    ],
                    [
                        "customFieldId" => "ep1mn", // pole_bonus+
                        "value" => [
                             $request->bonus
                        ]
                    ],
                )
            ));

            return redirect( '/bonus/' );
        }



        $user = User::user($id);

        return view('admin.bonus_update')->with('user', $user);



    }

    public function maxSession(Request $request)
    {
        if($request->isMethod('post')) {
            $user = User::where('id', $request->user_id)->first();
            $user->max_sessions = $request->max_sessions;
            $user->save();
        }

        $users = User::select('id', 'email', 'name', 'max_sessions')->get();

        View::share('title', 'Лимит линий');

        return view('admin.max-session')->with('users', $users);
    }

    public function partner(Request $request)
    {
        $users = User::select('id', 'email', 'name')->with('partner')->get();

        $partner_users = [];

        foreach ($users as $key =>$user){
            $counts = PartnerUsers::where('partner_id', (isset($user->partner) && !empty($user->partner)) ? $user->partner->id : '0')->with('user:ID,name,email,created_at')->count();
            if ($counts <> 0) {
                $partner_users[$user->id] = $counts;
            }
        }


        View::share('title', 'Партнеры');

        return view('admin.partner')->with('users', $users)->with('partner_users', $partner_users);
    }

    public function partnerView(Request $request, $domain, $tld, $id) {

        $partner = Partner::where('id', $id)->with('user:ID,name,email')->firstOrFail();
        $partner_users = PartnerUsers::where('partner_id', $id)->with('user:ID,name,email,created_at')->get();
        $partner_info = PartnerInfo::where('partner_id', $id)->first();
        $invoices = PartnerInvoice::where('partner_id', $id)->with('partner.user:ID,name')->with('partnerUser.user:ID,name')->get();
        $files = PartnerFile::where('partner_id', $id)->get();

        if ($request->isMethod('post')) {

            $service = $request->service;
            $rate = PartnerRate::where('partner_id', $id)->where('service', $service);

            if ($rate->first() === null) {
                PartnerRate::create([
                    'partner_id' => $id,
                    'service' => $service,
                    'first_rate' => $request->first,
                    'second_rate' => $request->second,
                    'third_rate' => $request->third
                ]);
            } else {
                $rate->update(['first_rate' => $request->first, 'second_rate' => $request->second, 'third_rate' => $request->third]);
            }
        }

        View::share('title', 'Партнер '.$partner->user->name);
        $callibro_rates = PartnerRate::where('partner_id', $id)->where('service', 'callibro')->first();
        $ivr_rates = PartnerRate::where('partner_id', $id)->where('service', 'ivr')->first();
        $sms_rates = PartnerRate::where('partner_id', $id)->where('service', 'sms')->first();
        return view('admin.partner-view')->with(['partner' => $partner, 'users' => $partner_users, 'partner_info' => $partner_info, 'invoices' => $invoices, 'files' => $files, 'callibro_rates' => $callibro_rates, 'ivr_rates' => $ivr_rates, 'sms_rates' => $sms_rates]);
    }

    public function partnerUpdate(Request $request, $domain, $tld, $id) {
        $partner = Partner::where('user_id', $id)->with('user:ID,email')->first();

        if ($partner === null) {
            $partner = Partner::create(['user_id' => $id, 'is_active' => 1]);

            PartnerRate::create(['partner_id' => $partner->id, 'service' => 'ivr', 'first_rate' => 50, 'second_rate' => 20, 'third_rate' => 10]);
            PartnerRate::create(['partner_id' => $partner->id, 'service' => 'sms', 'first_rate' => 10, 'second_rate' => 3, 'third_rate' => 0]);
            PartnerRate::create(['partner_id' => $partner->id, 'service' => 'callibro', 'first_rate' => 100, 'second_rate' => 40, 'third_rate' => 20]);
        } else {
            $partner->is_active = !$partner->is_active;
            $partner->save();
        }


        if($partner->is_active) {
            $template = 'admin.partner_activation_email';
            $mmTo = $partner->user->email;
            $subject = 'Добро пожаловать в команду единомышленников “U-Marketing.org”!';
            $from = [
                'address' => 'partner@u-marketing.org',
                'name' => 'U-marketing.org',
            ];

            $transport = (new \Swift_SmtpTransport('smtp.mail.ru', '465'))
                ->setEncryption('ssl')
                ->setUsername('partner@u-marketing.org')
                ->setPassword('Asd123102030');

            $mailer = app(\Illuminate\Mail\Mailer::class);
            $mailer->setSwiftMailer(new \Swift_Mailer($transport));

            $mail = $mailer
                ->to($mmTo)
                ->send(new Mailable($template, $subject, null, $from));
        }

        return redirect()->back();
    }

    public function partnerPay(Request $request)
    {

        $partner_users = PartnerUsers::where('partner_id', $request->partner_id)->with('user:ID,name,email,created_at')->get();

        $amount = $request->amount;

        foreach($partner_users as $user) {

            if($amount == 0) {
                break;
            }

            $transactions = $user->transactions();

            $share = $transactions->partner_share_total;
            $payment = $transactions->partner_payment;
            $d = $share - $payment;

            if($d <= 0) {
                continue;
            } else {

                if($d >= $amount) {
                    PartnerPayment::create([
                        'partner_id' => $request->partner_id,
                        'partner_user' => $user->id,
                        'type' => $request->type,
                        'amount' => $amount,
                        'contact_name' => $request->contact_name,
                        'card_number' =>$request->card_number,
                        'bic' => $request->bic
                    ]);

                    $amount = 0;
                } else {
                    PartnerPayment::create([
                        'partner_id' => $request->partner_id,
                        'partner_user' => $user->user_id,
                        'type' => $request->type,
                        'amount' => $d,
                        'contact_name' => $request->contact_name,
                        'card_number' =>$request->card_number,
                        'bic' => $request->bic
                    ]);

                    $amount -= $d;
                }
            }
        }

        $user = Partner::with('user')->find($request->partner_id)->user;

        $template = 'admin.payment_email';
        $mmTo = $user->email;
        $from = [
            'address' => 'partner@u-marketing.org',
            'name' => 'Mediasend',
        ];
        $subject = 'Выплата Партнеру '.$user->name;
        $data = [
            'name' => $user->name,
            'type' => $request->type,
            'amount' => $request->amount,
        ];

        Mail::to($mmTo)->send(new Mailable($template, $subject, $data));

        return redirect()->back();
    }

    public function partnerPayments(Request $request, $domain, $tld, $partner_user)
    {
        $user = PartnerUsers::where('id', $partner_user)->with('user:ID,name')->with('partner.user:ID,name')->first();

        $payments = PartnerPayment::where('partner_id', $user->partner->id)->where('partner_user', $partner_user)->get();

        View::share('title', 'Выплаты партнеру '.$user->partner->user->name.' за пользователя '.$user->user->name);

        return view('admin.partner-payments')->with('payments', $payments);
    }

    public function partnerPurchases(Request $request, $domain, $tld, $partner_user) {

        $user = PartnerUsers::where('id', $partner_user)->with('user:ID,name')->with('partner.user:ID,name')->first();

        $purchases = Payment::where('id_user', $user->user->id)->orderBy('time', 'DESC')->get();

        $title = 'Покупки пользователя '.$user->user->name;

        return view('admin.partner-purchases')->with('title', $title)->with('purchases', $purchases);
    }

    public function partnerTransactions(Request $request, $domain, $tld, $partner_user) {

        $user = PartnerUsers::where('id', $partner_user)->with('user:ID,name')->with('partner.user:ID,name')->first();

        $transactions = [];

        $expenses = DB::select("SELECT
                              FROM_UNIXTIME(`time`, '%Y-%m-%d') as day,
                              sum(amount) expense,
                              min(time) first_timestamp
                            FROM b_expense
                            WHERE id_user = ?
                            GROUP BY day
                            ORDER BY day DESC", [$user->user->id]);
        $partner_share = $user->transactions()->partner_share;

        foreach ($expenses as $expense){
            $date = $expense->day;
            if(!isset($transactions[$date]))
                $transactions[$date] = array('expense' => 0, 'first_timestamp' => 0);
            $total_share = 0;
            foreach($partner_share[$date] as $share) {
                $total_share += array_values($share)[0];
            }

            $transactions[$date]['expense'] = $expense->expense;
            $transactions[$date]['first_timestamp'] = $expense->first_timestamp;
            $transactions[$date]['share'] = [
                'shares' => $partner_share[$date],
                'total' => $total_share
            ];
        }

        $title = 'Покупки пользователя '.$user->user->name;

        return view('admin.partner-transactions')->with('title', $title)->with('transactions', $transactions);
    }

    public function partnerInvoices(Request $request) {

        $invoices = PartnerInvoice::with('partner.user:ID,name')->with('partnerUser.user:ID,name')->get();

        $title = 'Счета на оплату';

        return view('admin.partner-invoices')->with('title', $title)->with('invoices', $invoices);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearValidator()
    {
        DB::table('validator_files')->truncate();
        DB::table('validator_numbers')->truncate();
        return view('admin.validator');
    }

    public function tarrifs(Request $request, $domain, $tld, $service = null) {

        if($request->isMethod('post')) {

            if($request->has('tarrif')) {

                $tarrif = Tarrif::updateOrCreate(
                    ['prefix' => $request->prefix],
                    $request->except('_token', 'prefix')
                );

            } else if($request->has('message_tarrif')) {
                $tarrif = MessageTarrif::updateOrCreate(
                    ['prefix' => $request->prefix],
                    $request->except('_token', 'prefix', 'tarrif')
                );
            } else {
                $names = $request->names;
                $descriptions = collect($request->descriptions);
                $values = collect($request->values);

                foreach($names as $key => $name) {
                    $setting = Setting::updateOrCreate(
                        ['name' => $name],
                        ['description' => $descriptions->shift(), 'value' => $values->shift()]
                    );
                }
            }

            return redirect()->back();
        }

        if($service == 'message') {

            $settings_kzt = Setting::where('name', 'like', 'message_%_kzt')->get();
            $settings_rub = Setting::where('name', 'like', 'message_%_rub')->get();

            $tarrifs = MessageTarrif::all();

            return view('admin.message-tarrifs')->with(['settings_kzt' => $settings_kzt, 'settings_rub' => $settings_rub,  'tarrifs' => $tarrifs]);
        } else if($service == 'autocall') {

            $settings_kzt = Setting::where('name', 'like', 'autocall_%_kzt')->get();
            $settings_rub = Setting::where('name', 'like', 'autocall_%_rub')->get();

            $tarrifs = Tarrif::all();

            return view('admin.autocall-tarrifs')->with(['settings_kzt' => $settings_kzt, 'settings_rub' => $settings_rub, 'tarrifs' => $tarrifs]);
        } else {
            return 'admin.tarrifs';
        }
    }

    public function tarrifsDelete(Request $request, $domain, $tld, $type, $id) {

        if($type == 'autocall') {
            Tarrif::destroy($id);
        } else {
            MessageTarrif::destroy($id);
        }

        return redirect()->back();
    }

    public function rentNumbers(Request $request) {

        $users = User::select('id', 'email', 'name')->with('partner')->get();

        return view('admin.rent-numbers')->with('users', $users);
    }

    public function rentNumbersAdd(Request $request, $domain, $tld, $id) {

        $user = User::find($id);


        if($request->isMethod('post')) {

            RentNumber::create([
                'user_id' => $id,
                'number' => $request->number
            ]);

        }

        $rent_numbers = RentNumber::where('user_id', $id)->get();
        return view('admin.rent-numbers-add')->with(['user' => $user, 'rent_numbers' => $rent_numbers]);
    }

    public function rentNumbersDelete(Request $request, $domain, $tld, $id) {

        $rent_number = RentNumber::find($id);

        $rent_number->delete();

        return redirect()->back();
    }

    public function notification(Request $request) {

        if($request->isMethod('post')) {

            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $image = time().'.'.$file->getClientOriginalExtension();

                $file->move('images/notifications', $image);
            }

            $notification = new Notification();

            $notification->user_id = auth()->user()->id;
            $notification->title = $request->title;
            $notification->message = $request->message;
            $notification->type = $request->type;
            $notification->email = isset($request->email)?1:0;
            $notification->image = isset($image)?$image:null;

            if($request->preview == 'preview') {

                $user_email = auth()->user()->email;

                $template = 'admin.notification_email';
                $subject = 'Новое уведомление.';
                $data = [
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'image' => $notification->image,
                ];

                Mail::to($user_email)->send(new Mailable($template, $subject, $data));

                return new Mailable($template, $subject, $data);

            } else {

                $users = User::select('email')->get()->pluck('email');

                $notification->total_sent = $users->count();

                $notification->save();

                if(isset($request->email)) {
                    foreach ($users as $user) {
                        ProcessEmail::dispatch($user, $notification);
                    }
                }

                return redirect()->back();
            }
        }

        $all_notifications = Notification::with('user:ID,email')->orderBy('created_at', 'desc')->get();
        return view('admin.notification', ['all_notifications' => $all_notifications]);
    }

    public function notificationDelete(Request $request, $domain, $tld, $id) {
        Notification::destroy($id);

        return redirect()->back();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function loginAs(Request $request, $domain, $tld, $id)
    {
        if(User::isUserAdmin(Auth::user()->id)) {
            $user = User::find($id);

            if(empty($user->remember_token)){
                $token = bin2hex(random_bytes(60));
                $user->remember_token = $token;
                $user->save();
            }
            return redirect('https://cp.u-marketing.org/setting/auth/'.$user->remember_token);
        }

        return redirect('/');
    }




    public function payinfo(Request $request)
    {
        if(!$request['page']) {
            $request['page'] = 1;
        }
        $payments = Payment::where('payment_from', '!=', 'bp')->orderBy('time', 'desc')->paginate(50);
    
        return view('admin.pay_info')->with(['payments' => $payments, 'active' => 'umar']);
    }

    public function payinfo2(Request $request)
    {
        if(!$request['page']) {
            $request['page'] = 1;
        }
        $payments = Payment::where('payment_from', 'bp')->orderBy('time', 'desc')->paginate(50);

        foreach($payments as $payment) {
            $payment->id_user = 'BITRIX';
        }

        return view('admin.pay_info')->with(['payments' => $payments, 'active' => 'bp']);
    }

    public function autocalls(Request $request, $archive = null) {
        
       
        View::share('title', 'Автозвонки');

        

        $user = User::bitrixUser();
        $uid = $user->id;
        
        $autocalls = Autocall::where('is_integration', 0);
       
        if($archive) {
            $autocalls->where('status', Autocall::STATUS_ARCHIVE);
        } else {
            $autocalls->where('status', '!=' , Autocall::STATUS_ARCHIVE);
        }

        if($request->has('user_id') && count($request->user_id) > 0) {
            $autocalls = $autocalls->whereIn('user_id', $request->user_id);
        }

        $autocalls = $autocalls->orderBy('created_at', 'desc')->paginate(50);

 
        $stat_info = DB::table('b_statistics')
                        ->select('id_belong', DB::raw('count(*) as cnt'));
                        
                        
        
        


        $cont = []; // Количество контактов

        foreach ($autocalls as $key => $autocall) {
            $sum_count = 0;
            $group_id_array = explode(",", $autocall->group_id);
            foreach ($group_id_array as $key => $group_id) {
                $contacts_count = Contact::where('id_group', $group_id)->count();
                $sum_count += $contacts_count;

            }
            $cont[$autocall->id] = $sum_count;
        }


        $stat_success = $this->getQuantity(1, $request);
        $stat_busy = $this->getQuantity(3, $request);
        $stat_error = $this->getQuantity(-1, $request);


        $users = DB::select("SELECT users.ID, users.created_at, users.email FROM users");

        return view('admin.autocalls.index')
            ->with('autocalls', $autocalls)
            ->with('stat_success', $stat_success)
            ->with('stat_busy', $stat_busy)
            ->with('stat_error', $stat_error)
            ->with('cont', $cont)
            ->with('users', $users)
            ->with('user_ids', [])
            ->with('archive', $archive);

    }


    private function getQuantity($status = 0, Request $request) {
        
        // <option value="">Не выбран</option>
        // <option value="0">В очереди</option>
        // <option value="-1">Ошибка</option>
        // <option value="1">Успешно</option>
        // <option value="6">Абонент не ответил</option>
        // <option value="3">Абонент занят</option>
        // <option value="4">Отключен</option>
        // <option value="5">Отклонено</option>

        $stat_info = DB::table('b_statistics')
                        ->select('id_belong', DB::raw('count(*) as cnt'));

        if($request->has('user_id') && count($request->user_id) > 0) {
            $stat_info = $stat_info->whereIn('id_user', $request->user_id);
        }
        
        $stat_info = $stat_info->where('status', $status)
            ->where('type', Autocall::$VOICE_TYPE)
            ->groupBy('id_belong')
            ->get();

        $stat = []; //  Количество успешных
        foreach ($stat_info as $row) {
            $stat[$row->id_belong] = $row->cnt;
        }

        return $stat;
    }   
}
