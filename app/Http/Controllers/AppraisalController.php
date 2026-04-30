<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// use Carbon\Carbon;

// using to directly fetch data without models
use Illuminate\Support\Facades\DB;
use PDO;
use Symfony\Component\VarDumper\VarDumper;

use function Symfony\Component\Clock\now;
use function Termwind\render;

class AppraisalController extends Controller
{
    // verify login
    public function verifyLogin(Request $request)
    {
        $request->validate([
            'LOGIN_ID' => 'required | string',
            'LOGIN_PASSWORD' => 'required'
        ]);

        $user = Login::where('USER_ID', $request->input('LOGIN_ID'))
            ->where('USER_DISABLE_FLAG', 'N')
            ->first();

        if (!$user) {
            return back()->withErrors(['login' => 'Invalid User Id']);
        }

        if (! Hash::check($request->LOGIN_PASSWORD, $user->USER_PASSWD)) {
            return back()->withErrors(['login' => 'Invalid Password'])->withInput();
        }

        // Fetch employee data to get company code
        $employee = DB::table('PM_EMP_KEY')
            ->where('EMP_CODE', $user->USER_ID)
            ->first();

        // login success (custom session)
        session([
            'user_id' => $user->USER_ID,
            'logged_in' => true,
            'user' => $user->toArray(),
            'company_code' => $employee->EMP_COMP_CODE ?? null,
        ]);

        $remember = $request->has('REMEMBER_ME');
        $rememberDuration = 60 * 24 * 30; // 30 days
        $response = redirect()->route('dashboard');

        if ($remember) {
            $response = $response
                ->withCookie(cookie('LOGIN_ID', $request->LOGIN_ID, $rememberDuration))
                ->withCookie(cookie('LOGIN_PASSWORD', $request->LOGIN_PASSWORD, $rememberDuration))
                ->withCookie(cookie('REMEMBER_ME', '1', $rememberDuration));
        } else {
            $response = $response
                ->withCookie(cookie()->forget('LOGIN_ID'))
                ->withCookie(cookie()->forget('LOGIN_PASSWORD'))
                ->withCookie(cookie()->forget('REMEMBER_ME'));
        }

        return $response;
    }

    // logout user
    public function logout()
    {
        session_start();
        session_destroy();
        return redirect('/');
    }

    /**
     * Display dashboard with employee data
     */
    public function dashboard()
    {
        // Check if user is logged in
        if (!session('logged_in')) {
            return redirect('/')->withErrors(['login' => 'Please log in first']);
        }

        // Get the logged-in employee ID
        $employeeId = session('user_id');

        $employee = DB::table('PM_EMP_KEY')
            ->select(
                'PM_EMP_KEY.*',
                'FM_COMPANY.COMP_NAME',
                'PM_JOB_TITLE.JOB_TITLE_DESC',
                'FM_DIVISION.DIVN_NAME',
                'PM_EMP_PERS.EMPS_PRESENT_MOBILE_NO',
                'PM_EMP_PERS.EMPS_PRESENT_ADD_1',
                'PM_EMP_PERS.EMPS_PRESENT_ADD_2',
                'PM_EMP_PERS.EMPS_PRESENT_ADD_3',
                'PM_EMP_PERS.EMPS_PRESENT_EMAIL_ADD',
                'PM_COUNTRY.CNTRY_NATIONALITY',
                'PM_EMP_DOCUMENT.EMPD_NUMBER',
                'FM_DEPARTMENT.DEPT_NAME',
                'KEY.EMP_NAME AS REPORTING_TO'
            )
            ->leftJoin('FM_COMPANY', 'FM_COMPANY.COMP_CODE', '=', 'PM_EMP_KEY.EMP_COMP_CODE')
            ->leftJoin('PM_JOB_TITLE', 'PM_JOB_TITLE.JOB_TITLE_CODE', '=', 'PM_EMP_KEY.EMP_JOB_TITLE_CODE')
            ->leftJoin('FM_DIVISION', 'FM_DIVISION.DIVN_CODE', '=', 'PM_EMP_KEY.EMP_DIVN_CODE')
            ->leftJoin('PM_EMP_PERS', 'PM_EMP_PERS.EMPS_CODE', '=', 'PM_EMP_KEY.EMP_CODE')
            ->leftJoin('PM_COUNTRY', 'PM_COUNTRY.CNTRY_CODE', '=', 'PM_EMP_KEY.EMP_FLEXI_VAL9')

            ->leftJoin('PM_EMP_DOCUMENT', function ($join) {
                $join->on('PM_EMP_DOCUMENT.EMPD_CODE', '=', 'PM_EMP_KEY.EMP_CODE')
                    ->where('PM_EMP_DOCUMENT.EMPD_DOCU_CODE', '=', 'D002'); // AND condition
            })

            ->leftJoin('FM_DEPARTMENT', 'FM_DEPARTMENT.DEPT_CODE', '=', 'PM_EMP_KEY.EMP_DEPT_CODE')
            ->leftJoin('PM_EMP_KEY AS KEY', 'KEY.EMP_CODE', '=', 'PM_EMP_KEY.EMP_REPORTING_TO')
            ->where('PM_EMP_KEY.EMP_CODE', $employeeId)
            ->first();




        // return view('dashboard', [
        //     'employee' => $employee
        // ]);
        return view('dashboard', compact('employee'));
    }


    // display kpis already added to user
    public function ShowKPIData()
    {
        $KPI_DATA = DB::table('PM_APPRAISAL_KPI')
            ->get();
        return view('APPRAISAL.MASTER.KPI_MASTER', compact('KPI_DATA'));
    }


    // show kpi form
    public function ShowKpiForm()
    {

        $FORM_TYPE = request('STATUS');
        $KPI_SYS_ID = request('KPI_SYS_ID') ?? NULL;
        $TXN_CODE = '';
        $KPI_NAME = '';
        $KPI_STATUS = '';
        $FORM_NAME = '';
        $FORM_BUTTON_NAME = '';
        $APP_BY_SUP = '';
        $APP_BY_AM = '';
        $APP_BY_HR = '';

        if ($FORM_TYPE == 'ADD') {
            $KPI_DATA = DB::table('PM_APPRAISAL_KPI')
                ->orderBy('KPI_SYS_ID', 'DESC')
                ->first();

            $TXN_CODE = str_pad($KPI_DATA->KPI_SYS_ID + 1, 3, '0', STR_PAD_LEFT);
            $FORM_NAME = 'App Appraisal KPI';
            $FORM_BUTTON_NAME = 'App KPI';
        } else {
            $KPI_DATA = DB::table('PM_APPRAISAL_KPI')
                ->where('KPI_SYS_ID', $KPI_SYS_ID)
                ->first();

            $TXN_CODE = str_pad($KPI_DATA->KPI_SYS_ID, 3, '0', STR_PAD_LEFT);;
            $KPI_NAME = $KPI_DATA->KPI_DESC;
            $KPI_STATUS = $KPI_DATA->KPI_FRZ_FLAG;
            $FORM_NAME = 'Update Appraisal KPI';
            $FORM_BUTTON_NAME = 'Update KPI';
            $APP_BY_SUP = $KPI_DATA->KPI_APPROVE_BY_SUP;
            $APP_BY_AM = $KPI_DATA->KPI_APPROVE_BY_AM;
            $APP_BY_HR = $KPI_DATA->KPI_APPROVE_BY_HR;
        }

        return view('APPRAISAL.MASTER.KPI_MASTER_FORM', compact(
            'TXN_CODE',
            'KPI_NAME',
            'KPI_STATUS',
            'FORM_NAME',
            'FORM_BUTTON_NAME',
            'FORM_TYPE',
            'APP_BY_SUP',
            'APP_BY_AM',
            'APP_BY_HR',
        ));
    }


    // add new / update kpi data
    public function AddUpdateKpiData(Request $request)
    {
        $LOGIN_USER_ID = session('user_id');
        $KPI_SYS_ID  = $request->input('KPI_CODE');
        $KPI_NAME    = $request->input('KPI_NAME');
        $KPI_STATUS  = $request->input('STATUS');
        $APPROVE_BY_SUP  = $request->input('APPROVE_BY_SUP');
        $APPROVE_BY_AM  = $request->input('APPROVE_BY_AM');
        $APPROVE_BY_HR  = $request->input('APPROVE_BY_HR');
        $ACTION_TYPE = $request->input('ACTION_BUTTON');

        $KPI_STATUS = ($KPI_STATUS == 'in-active' ? 'F' : 'N');
        $APPROVE_BY_SUP = ($APPROVE_BY_SUP == 'YES' ? 'Y' : 'N');
        $APPROVE_BY_AM = ($APPROVE_BY_AM == 'YES' ? 'Y' : 'N');
        $APPROVE_BY_HR = ($APPROVE_BY_HR == 'YES' ? 'Y' : 'N');

        try {
            if ($ACTION_TYPE == 'ADD') {
                DB::table('PM_APPRAISAL_KPI')->insert([
                    'KPI_SYS_ID'  => $KPI_SYS_ID,
                    'KPI_DESC'    => $KPI_NAME,
                    'KPI_FRZ_FLAG' => $KPI_STATUS,
                    'KPI_CR_DT' => date('Y-m-d h:i:s'),
                    'KPI_CR_UID' => $LOGIN_USER_ID,
                    'KPI_APPROVE_BY_SUP' => $APPROVE_BY_SUP,
                    'KPI_APPROVE_BY_AM' => $APPROVE_BY_AM,
                    'KPI_APPROVE_BY_HR' => $APPROVE_BY_HR,

                ]);
                $MESSAGE = 'KPI_ADDED';
            } else {
                DB::table('PM_APPRAISAL_KPI')
                    ->where('KPI_SYS_ID', $KPI_SYS_ID)
                    ->update([
                        'KPI_DESC'    => $KPI_NAME,
                        'KPI_FRZ_FLAG' => $KPI_STATUS,
                        'KPI_UPD_DT' => date('Y-m-d h:i:s'),
                        'KPI_UPD_UID' => $LOGIN_USER_ID,
                        'KPI_APPROVE_BY_SUP' => $APPROVE_BY_SUP,
                        'KPI_APPROVE_BY_AM' => $APPROVE_BY_AM,
                        'KPI_APPROVE_BY_HR' => $APPROVE_BY_HR,
                    ]);
                $MESSAGE = 'KPI_UPDATED';
            }
        } catch (\Exception $e) {
            $MESSAGE = $ACTION_TYPE == 'ADD' ? 'KPI_ADD_FAIL' : 'KPI_UPD_FAIL';
        }

        // laravel user session flash
        session()->flash('SESSION_MESSAGE', $MESSAGE);

        return redirect()->route('appraisal.kpi-master'); // redirect back
    }


    // show appraisal to supervisor
    public function showAppraisal()
    {
        $employeeId = session('user_id');

        //  Get employee codes where SEQ_NO = 1 (direct reports)
        $EMP_CODES = DB::table('EMP_AUTH_USER')
            ->where('EAU_CODE', $employeeId)
            ->where('EAU_SEQ_NO', '1')
            ->pluck('EAU_EMP_CODE')
            ->toArray();

        if (empty($EMP_CODES)) {
            return view('APPRAISAL.appraisal', ['subordinates' => collect()]);
        }

        //  Fetch all employees with joins in one query
        $subordinates = DB::table('PM_EMP_KEY')
            ->select(
                'PM_EMP_KEY.EMP_CODE',
                'PM_EMP_KEY.EMP_NAME',
                'PM_EMP_KEY.EMP_JOIN_DT',
                'FM_DEPARTMENT.DEPT_NAME',
                'FM_COMPANY.COMP_NAME',
                'PM_JOB_TITLE.JOB_TITLE_DESC',
                'PM_JOB_TITLE.JOB_TITLE_APPRAISAL_START_MONTH',
                'PM_JOB_TITLE.JOB_TITLE_APPRAISAL_FREQ',
                'PM_JOB_TITLE.JOB_TITLE_MAX_APPRAISAL_DURATION',
                'FM_DIVISION.DIVN_NAME',
            )
            ->leftJoin('FM_DEPARTMENT', 'FM_DEPARTMENT.DEPT_CODE', '=', 'PM_EMP_KEY.EMP_DEPT_CODE')
            ->leftJoin('PM_JOB_TITLE', 'PM_JOB_TITLE.JOB_TITLE_CODE', '=', 'PM_EMP_KEY.EMP_JOB_TITLE_CODE')
            ->leftJoin('FM_DIVISION', 'FM_DIVISION.DIVN_CODE', '=', 'PM_EMP_KEY.EMP_DIVN_CODE')
            ->leftJoin('FM_COMPANY', 'FM_COMPANY.COMP_CODE', '=', 'PM_EMP_KEY.EMP_COMP_CODE')
            ->whereIn('PM_EMP_KEY.EMP_CODE', $EMP_CODES)
            ->orderBy('PM_EMP_KEY.EMP_CODE', 'ASC')
            ->get();

        $TODAYS_DATE = $TODAYS_DATE = date('Y-m-d');

        foreach ($subordinates as $emp) {

            //  Skip employee if DOJ is missing
            if (empty($emp->EMP_JOIN_DT)) {
                $emp->skip = true;
                continue;
            }

            $EMP_APPR_START_MONTH = (int) $emp->JOB_TITLE_APPRAISAL_START_MONTH;
            $EMP_APPR_FREQ        = $emp->JOB_TITLE_APPRAISAL_FREQ;

            //  Frequency in months
            $FREQ_MONTHS = match ($EMP_APPR_FREQ) {
                'Q'     => 3,
                'Y'     => 12,
                default => 1,  // 'M' or anything else
            };

            //  Determine cycle generation start year based on DOJ
            $joinYear  = (int) date('Y', strtotime($emp->EMP_JOIN_DT));
            $joinMonth = (int) date('m', strtotime($emp->EMP_JOIN_DT));

            // If employee joined AFTER the appraisal start month,
            // first eligible cycle is next year
            $checkYear  = ($joinMonth > $EMP_APPR_START_MONTH) ? $joinYear + 1 : $joinYear;
            $checkMonth = $EMP_APPR_START_MONTH;

            //  Generate all cycle dates from DOJ onwards
            $allCycleDates = [];

            for ($i = 0; $i < 200; $i++) {
                $cycleDate       = date('Y-m-d', mktime(0, 0, 0, $checkMonth, 1, $checkYear));
                $allCycleDates[] = $cycleDate;

                $checkMonth += $FREQ_MONTHS;
                while ($checkMonth > 12) {
                    $checkMonth -= 12;
                    $checkYear++;
                }

                // Stop once we pass today
                if ($cycleDate > $TODAYS_DATE) break;
            }

            //  Find the latest cycle start date <= today
            $CYCLE_START_DATE = null;
            foreach (array_reverse($allCycleDates) as $cycleDate) {
                if ($cycleDate <= $TODAYS_DATE) {
                    $CYCLE_START_DATE = $cycleDate;
                    break;
                }
            }

            //  Skip if no valid cycle has started yet
            if (!$CYCLE_START_DATE) {
                $emp->skip = true;
                continue;
            }

            $NEXT_CYCLE_START = date(
                'Y-m-d',
                strtotime("+{$FREQ_MONTHS} months", strtotime($CYCLE_START_DATE))
            );

            //  Default UI state
            $emp->skip           = false;
            $emp->color          = 'blue';
            $emp->text           = '';
            $emp->start          = 1;
            $emp->startText      = 'Start';
            $emp->startTextColor = 'red';
            $emp->next_appraisal = $NEXT_CYCLE_START;
            $emp->APAH_SYS_ID    = '';

            //  Check for existing appraisal transaction in current cycle
            $appraisalTxn = DB::table('PT_APPRAISAL_APPL_HEAD')
                ->where('APAH_EMP_CODE', $emp->EMP_CODE)
                ->whereBetween('APAH_TXN_DT', [$CYCLE_START_DATE, $NEXT_CYCLE_START])
                ->first();

            $EVAL_STATUS     = '';
            $APAH_TXN_STATUS = '';
            $APAH_S1_APP_UID = '';
            $APAH_S2_APP_UID = '';
            $APAH_HR_APP_UID = '';

            if ($appraisalTxn) {
                $emp->APAH_SYS_ID    = $appraisalTxn->APAH_SYS_ID;
                $EVAL_STATUS         = $appraisalTxn->APAH_EVAL_STATUS;
                $APAH_TXN_STATUS     = $appraisalTxn->APAH_TXN_STATUS;
                $APAH_S1_APP_UID     = $appraisalTxn->APAH_S1_APP_UID;
                $APAH_S2_APP_UID     = $appraisalTxn->APAH_S2_APP_UID;
                $APAH_HR_APP_UID     = $appraisalTxn->APAH_HR_APP_UID;

                // Has a draft/in-progress appraisal
                $emp->startText      = 'Draft';
                $emp->startTextColor = '#fa6e07';
            }

            //  Fetch approver sequence numbers for this employee
            $ALL_SEQ_NO_ARRY = DB::table('EMP_AUTH_USER')
                ->where('EAU_EMP_CODE', $emp->EMP_CODE)
                ->pluck('EAU_SEQ_NO')
                ->toArray();

            // var_dump($ALL_SEQ_NO_ARRY);
            // echo $ALL_SEQ_NO_ARRY[1];

            //  Final status logic
            if ($APAH_TXN_STATUS === '2') {

                if ($TODAYS_DATE >= $NEXT_CYCLE_START) {
                    // Next cycle has started — reset to allow new appraisal
                    $emp->startText      = 'Start';
                    $emp->startTextColor = 'red';
                    $emp->color          = 'blue';
                    $emp->text           = '';
                } else {
                    // Fully approved, next cycle not started yet
                    $emp->startText      = 'Completed';
                    $emp->startTextColor = 'green';
                    $emp->color          = 'green';
                    $emp->text           = 'Approved';
                }
            } elseif (!empty($APAH_S1_APP_UID) && empty($APAH_S2_APP_UID) && ($ALL_SEQ_NO_ARRY[1] ?? '') == '2') {
                // Self-eval done, waiting for Area Manager
                $emp->startText      = 'Completed';
                $emp->startTextColor = 'green';
                $emp->color          = 'blue';
                $emp->text           = 'AM-Pending';
            } elseif (!empty($APAH_S1_APP_UID) && empty($APAH_HR_APP_UID) && ($ALL_SEQ_NO_ARRY[1] ?? '') == '3') {
                // Self-eval done, waiting for HR approval
                $emp->startText      = 'Completed';
                $emp->startTextColor = 'green';
                $emp->color          = 'blue';
                $emp->text           = 'HR-Pending';
            }
        }

        //  Filter out skipped employees before passing to view
        $subordinates = $subordinates->filter(fn($emp) => empty($emp->skip))->values();

        return view('APPRAISAL.appraisal', compact('subordinates'));
    }


    // appraisal form data for employee
    public function ShowAppraisalForm()
    {

        $FORM_TYPE = 'SUGGEST_APPRAISAL';
        $APRSL_EMP_CODE = request('APRSL_EMP_CODE');
        $LOGIN_USER_ID = session('user_id');
        $LOGIN_COMPANY_CODE = session('company_code');

        // variable to check if the evaluation already started
        $EVAL_STARTED = false;


        // Fetch employee data
        $APRSL_EMP = DB::table('PM_EMP_KEY')
            ->select(
                'PM_EMP_KEY.EMP_CODE',
                'PM_EMP_KEY.EMP_NAME',
                'EMP.EMP_NAME AS REPORTING_TO',
                'FM_DEPARTMENT.DEPT_NAME',
                'FM_COMPANY.COMP_NAME',
                'PM_JOB_TITLE.JOB_TITLE_DESC',
                'PM_JOB_TITLE.JOB_TITLE_CODE',
                'PM_JOB_TITLE.JOB_TITLE_APPRAISAL_START_MONTH',
                'PM_JOB_TITLE.JOB_TITLE_MAX_APPRAISAL_DURATION',
                'PM_JOB_TITLE.JOB_TITLE_APPRAISAL_FREQ',
                'FM_DIVISION.DIVN_NAME',
                'PM_EMP_KEY.EMP_DEPT_CODE'
            )
            ->leftJoin('FM_DEPARTMENT', 'FM_DEPARTMENT.DEPT_CODE', '=', 'PM_EMP_KEY.EMP_DEPT_CODE')
            ->leftJoin('PM_JOB_TITLE', 'PM_JOB_TITLE.JOB_TITLE_CODE', '=', 'PM_EMP_KEY.EMP_JOB_TITLE_CODE')
            ->leftJoin('FM_DIVISION', 'FM_DIVISION.DIVN_CODE', '=', 'PM_EMP_KEY.EMP_DIVN_CODE')
            ->leftJoin('FM_COMPANY', 'FM_COMPANY.COMP_CODE', '=', 'PM_EMP_KEY.EMP_COMP_CODE')
            ->leftJoin('PM_EMP_KEY AS EMP', 'EMP.EMP_CODE', '=', 'PM_EMP_KEY.EMP_REPORTING_TO')
            ->where('PM_EMP_KEY.EMP_CODE', $APRSL_EMP_CODE)
            ->first();

        // fetching txn no and increading it
        $TXN_NO = DB::table('PM_TXN_DOC_RANGE')
            ->select('TXND_CURR_NO')
            ->where('TXND_TXN_CODE', '=', 'APRSL')
            ->where('TXND_COMP_CODE', $LOGIN_COMPANY_CODE)
            ->first();

        // Generate transaction code
        if (!$TXN_NO) {
            return redirect()->back()->with('error', 'Transaction range not found');
        }

        $TXN_CODE = 'APRSL' . date('Y') . '_' . str_pad(($TXN_NO->TXND_CURR_NO + 1), 3, '0', STR_PAD_LEFT);;
        $SYS_ID = '';


        // Extract employee position code and get position name
        $EMP_POSI_CODE = $APRSL_EMP->JOB_TITLE_CODE ?? null;
        $POSITION = $APRSL_EMP->JOB_TITLE_DESC ?? 'N/A';

        // Get department info
        $EMP_DEPT_CODE = $APRSL_EMP->EMP_DEPT_CODE ?? null;
        $DEPARTMENT_NAME = $APRSL_EMP->DEPT_NAME ?? 'N/A';


        // Fetch KPI data for this job title
        $linkedData = DB::table('PM_KPI_ROLE_ASSINGMENT')
            ->select('KRA_KPI_SYS_ID', 'KRA_EC_SYS_ID')
            ->where('KRA_JOB_CODE', $EMP_POSI_CODE)
            ->get();

        $ALL_KPI_CODE_ARRAY = array();
        $ALL_ECS_CODE_ARRAY = array();

        foreach ($linkedData as $data) {
            $ALL_KPI_CODE_ARRAY[] = $data->KRA_KPI_SYS_ID;
            $ALL_ECS_CODE_ARRAY[$data->KRA_KPI_SYS_ID] = $data->KRA_EC_SYS_ID;
        }


        // FILTERING KPI TO FIND KPI FOR SUPERVISOR
        $All_KPI_TO_SUP = array();
        foreach ($ALL_KPI_CODE_ARRAY as $KPI) {
            // echo $KPI;
            $SUP_KPIS = DB::table('PM_APPRAISAL_KPI')
                ->where('KPI_SYS_ID', $KPI)
                ->where('KPI_APPROVE_BY_SUP', 'Y')
                ->where('KPI_FRZ_FLAG', 'N')
                ->first();

            if ($SUP_KPIS) {
                $All_KPI_TO_SUP[] = $SUP_KPIS->KPI_SYS_ID;
            }
        }

        // VAR_DUMP($All_KPI_TO_SUP);






        // DATA FOR EVALUTION START CHECK
        $TODAYS_DATE       = date('Y-m-d');
        $GAP_BTW_DATES     = $APRSL_EMP->JOB_TITLE_MAX_APPRAISAL_DURATION ?? NULL;
        $EXP_DIFF_IN_DATES = date('Y-m-d', strtotime('-' . $GAP_BTW_DATES . ' days'));

        // CHECKING IF THE EVALUATION ALREADY STARTED
        $CHECK_EVAL = DB::table('PT_APPRAISAL_APPL_HEAD')
            ->where('APAH_EMP_CODE', $APRSL_EMP_CODE)
            ->whereBetween('APAH_TXN_DT', [$EXP_DIFF_IN_DATES, $TODAYS_DATE])
            ->first();

        $SYS_ID = '';
        // $TXN_CODE = '';
        $TXN_NO = '';
        $LAST_KPI_EVAL_CODE = '';
        $APAH_EVAL_STATUS = '';
        if ($CHECK_EVAL) {
            $EVAL_STARTED = true;
            $TXN_CODE = $CHECK_EVAL->APAH_TXN_CODE . date('Y') . '_' . str_pad($CHECK_EVAL->APAH_TXN_NO, 3, '0', STR_PAD_LEFT);
            $LAST_KPI_EVAL_CODE = $CHECK_EVAL->APAH_LAST_EVAL_KPI_CODE;
            $TXN_NO = $CHECK_EVAL->APAH_TXN_NO;
            $SYS_ID = $CHECK_EVAL->APAH_SYS_ID;
            $APAH_EVAL_STATUS = $CHECK_EVAL->APAH_EVAL_STATUS;
        }

        $LAST_KPI_INDEX = 0;
        $NEXT_KPI_EVAL_CODE = '';

        if ($EVAL_STARTED == true) {
            $LAST_KPI_INDEX = array_search($LAST_KPI_EVAL_CODE, $All_KPI_TO_SUP);
            $NEXT_KPI_EVAL_CODE = $All_KPI_TO_SUP[$LAST_KPI_INDEX + 1] ?? null;
            $LAST_KPI_INDEX += 1;
        } else {
            $NEXT_KPI_EVAL_CODE = $All_KPI_TO_SUP[0] ?? null;
        }

        $ECS_CODE = $ALL_ECS_CODE_ARRAY[$NEXT_KPI_EVAL_CODE] ?? null;


        $FINAL_SUBMIT = false;

        // kpi details
        $KPI_CODE = array();
        $KPI_DESC = array();

        // SEPERATING EC CODE 
        $EC_DESC = array();
        $EC_CODE = array();
        $EC_MAX = array();
        $EC_MIN = array();
        $EC_GIVEN_SCORE = array();
        $EC_DATA = array();


        // checking if the apprasisal is already submiited
        $IS_SUBMITED_ALREADY = false;
        if (!empty($CHECK_EVAL->APAH_S1_APP_UID)) {
            $IS_SUBMITED_ALREADY = true;
        }


        // echo $LAST_KPI_INDEX;
        if ($LAST_KPI_INDEX === count($All_KPI_TO_SUP) || $APAH_EVAL_STATUS == '2' || $IS_SUBMITED_ALREADY) {
            $FINAL_SUBMIT = true;

            foreach ($ALL_KPI_CODE_ARRAY as $kpiCode) {

                $kpiData = DB::table('PM_APPRAISAL_KPI')
                    ->where('KPI_SYS_ID', $kpiCode)
                    ->where('KPI_FRZ_FLAG', 'N')
                    ->first();

                if (!$kpiData) continue;

                $KPI_CODE[] = $kpiData->KPI_SYS_ID;
                $KPI_DESC[$kpiData->KPI_SYS_ID] = $kpiData->KPI_DESC ?? null;

                //  Group EC data under each KPI code
                $EC_CODE_ARRAY = explode(',', $ALL_ECS_CODE_ARRAY[$kpiCode]);

                foreach ($EC_CODE_ARRAY as $ecCode) {
                    $ecCode = trim($ecCode);

                    // Fetch ALL scores for this EC (all authorities)
                    $ecScores = DB::table('PT_APPRAISAL_APPL_SCORES')
                        ->join('PM_APPRAISAL_KPI_EC', 'PM_APPRAISAL_KPI_EC.KPI_EC_SYS_ID', '=', 'PT_APPRAISAL_APPL_SCORES.APAS_KPI_EC_CODE')
                        ->where('PT_APPRAISAL_APPL_SCORES.APAS_KPI_EC_CODE', $ecCode)
                        ->where('PT_APPRAISAL_APPL_SCORES.APAS_KPI_CODE', $kpiCode)
                        ->where('PT_APPRAISAL_APPL_SCORES.APAS_REF_SYS_ID', $SYS_ID)
                        ->where('PT_APPRAISAL_APPL_SCORES.APAS_REF_TXN_NO', $TXN_NO)
                        // ->where('PT_APPRAISAL_APPL_SCORES.APAS_COMP_CODE', $LOGIN_COMPANY_CODE)
                        ->get();

                    foreach ($ecScores as $ecData) {
                        $EC_DATA[$kpiCode][] = [
                            'code'        => $ecData->KPI_EC_SYS_ID,
                            'desc'        => $ecData->KPI_EC_DESC,
                            'max'         => $ecData->KPI_EC_MAX_RANGE,
                            'min'         => $ecData->KPI_EC_MIN_RANGE,
                            'given_score' => $ecData->APAS_KPI_EC_GIVEN_SCORE,
                            'eval_by'     => $ecData->APAS_CR_UID,
                        ];
                    }
                }
            }
        } else {
            // Fetch KPI data
            $kpiData = DB::table('PM_APPRAISAL_KPI')
                ->where('KPI_SYS_ID', $NEXT_KPI_EVAL_CODE)
                ->where('KPI_FRZ_FLAG', 'N')
                ->first();

            $KPI_CODE[] = $kpiData->KPI_SYS_ID ?? null;
            $KPI_DESC[$kpiData->KPI_SYS_ID] = $kpiData->KPI_DESC ?? null;

            // Separate EC codes
            $EC_CODE_ARRAY = explode(',', $ECS_CODE);

            foreach ($EC_CODE_ARRAY as $ecCode) {
                $ecCode = trim($ecCode); //  trim spaces

                $ecData = DB::table('PM_APPRAISAL_KPI_EC')
                    ->where('KPI_EC_SYS_ID', $ecCode)
                    ->where('KPI_EC_FRZ_FLAG', 'N')
                    ->first();

                if ($ecData) {
                    //  All arrays keyed by EC SYS_ID consistently
                    $EC_CODE[]                       = $ecData->KPI_EC_SYS_ID;
                    $EC_DESC[$ecData->KPI_EC_SYS_ID] = $ecData->KPI_EC_DESC;
                    $EC_MAX[$ecData->KPI_EC_SYS_ID]  = $ecData->KPI_EC_MAX_RANGE;
                    $EC_MIN[$ecData->KPI_EC_SYS_ID]  = $ecData->KPI_EC_MIN_RANGE;
                }
            }
        }







        // calculating form evaluation percentage
        $PROGRESS = count($All_KPI_TO_SUP) > 0 ? round((($LAST_KPI_INDEX) / count($All_KPI_TO_SUP)) * 100, 2) : 0;


        return view('APPRAISAL.appraisal_form', compact(
            'APRSL_EMP',
            'TXN_CODE',
            'SYS_ID',
            'EMP_POSI_CODE',
            'POSITION',
            'EMP_DEPT_CODE',
            'DEPARTMENT_NAME',
            'LOGIN_USER_ID',
            'KPI_CODE',
            'KPI_DESC',
            'EC_CODE',
            'EC_DESC',
            'EC_MAX',
            'EC_MIN',
            'EC_DATA',
            'EC_GIVEN_SCORE',
            'PROGRESS',
            'FINAL_SUBMIT',
            'LAST_KPI_INDEX',
            'APAH_EVAL_STATUS',
            'FORM_TYPE',
            'IS_SUBMITED_ALREADY',
        ));
    }


    // function to save appraisal data
    public function SaveAppraisalDataBySup(Request $request)
    {

        $companyCode = session('company_code');
        $userId      = session('user_id');

        $EMP_CODE          = $request->EMP_CODE;
        $EMP_POSITION_CODE = $request->EMP_POSITION_CODE;
        $kpiCode           = $request->KPI_CODE;

        $EVAL_STARTED     = false;
        $APRSL_TXN_CODE   = '';
        $APRSL_NO         = '';
        $APAH_SYS_ID      = '';
        $APAH_EVAL_STATUS = '1';
        $APRH_TXN_DT      = date('Y-m-d');

        DB::beginTransaction();

        try {

            // Total KPIs for this job
            $TOTAL_KPIS_TO_THIS_JOB = DB::table('PM_KPI_ROLE_ASSINGMENT')
                ->where('KRA_JOB_CODE', $EMP_POSITION_CODE)
                ->count();

            // Fetch job details
            $JOB_DATA = DB::table('PM_JOB_TITLE')
                ->where('JOB_TITLE_CODE', $EMP_POSITION_CODE)
                ->first();

            if (!$JOB_DATA) {
                throw new \Exception('Job title not found');
            }


            $APP_MAX_DURATION     = $JOB_DATA->JOB_TITLE_MAX_APPRAISAL_DURATION;
            $TODAYS_DATE          = date('Y-m-d');
            $EXP_DIFF_IN_DATES    = date('Y-m-d', strtotime('-' . $APP_MAX_DURATION . ' days'));
            $EVAL_END_BEFORE_DATA = date('Y-m-d', strtotime('+' . $APP_MAX_DURATION . ' days'));
            $NEXT_EVAL_START_DATE = date('Y-m-d', strtotime('+' . $APP_MAX_DURATION . ' days'));

            // Check if appraisal already started
            $CHECK_EVAL = DB::table('PT_APPRAISAL_APPL_HEAD')
                ->where('APAH_EMP_CODE', $EMP_CODE)
                ->whereBetween('APAH_TXN_DT', [$EXP_DIFF_IN_DATES, $TODAYS_DATE])
                ->first();

            if ($CHECK_EVAL) {
                $EVAL_STARTED   = true;
                $APRSL_TXN_CODE = $CHECK_EVAL->APAH_TXN_CODE;
                $APRSL_NO       = $CHECK_EVAL->APAH_TXN_NO;
                $APAH_SYS_ID    = $CHECK_EVAL->APAH_SYS_ID;
            }

            if (!$EVAL_STARTED) {

                $txnRange = DB::table('PM_TXN_DOC_RANGE')
                    ->where('TXND_TXN_CODE', 'APRSL')
                    ->where('TXND_COMP_CODE', $companyCode)
                    ->first();

                $APRSL_NO       = ($txnRange->TXND_CURR_NO ?? 0) + 1;
                $APRSL_TXN_CODE = 'APRSL';

                $APAH_SYS_ID = DB::table('PT_APPRAISAL_APPL_HEAD')->insertGetId([
                    'APAH_COMP_CODE'            => $companyCode,
                    'APAH_TXN_CODE'             => $APRSL_TXN_CODE,
                    'APAH_TXN_NO'               => $APRSL_NO,
                    'APAH_TXN_DT'               => $APRH_TXN_DT,
                    'APAH_EMP_CODE'             => $EMP_CODE,
                    'APAH_REF_FROM'             => 'D',
                    'APAH_EVAL_STATUS'          => $APAH_EVAL_STATUS,
                    'APAH_TXN_STATUS'           => '1',
                    'APAH_EVAL_START_DATE'      => $APRH_TXN_DT,
                    'APAH_EVAL_END_BEFORE_DATE' => $EVAL_END_BEFORE_DATA,
                    'APAH_NEXT_EVAL_START_DATE' => $NEXT_EVAL_START_DATE,
                    'APAH_TOTAL_KPIS'           => $TOTAL_KPIS_TO_THIS_JOB,
                    'APAH_LAST_EVAL_KPI_CODE'   => $kpiCode,
                    'APAH_CR_DT'                => DB::raw('CURRENT_TIMESTAMP'),
                    'APAH_CR_UID'               => $userId,
                ]);

                DB::table('PM_TXN_DOC_RANGE')
                    ->where('TXND_TXN_CODE', 'APRSL')
                    ->where('TXND_COMP_CODE', $companyCode)
                    ->update(['TXND_CURR_NO' => $APRSL_NO]);
            } else {

                // Update KPI code in head on every save
                DB::table('PT_APPRAISAL_APPL_HEAD')
                    ->where('APAH_SYS_ID', $APAH_SYS_ID)
                    ->update([
                        'APAH_EVAL_STATUS'        => '1',
                        'APAH_LAST_EVAL_KPI_CODE' => $kpiCode,
                        'APAH_UPD_DT'             => DB::raw('CURRENT_TIMESTAMP'),
                        'APAH_UPD_UID'            => $userId,
                    ]);
            }

            // Save EC scores with alphanumeric pattern
            foreach ($request->all() as $key => $value) {
                if (preg_match('/^EC_[A-Za-z0-9]+$/', $key)) {

                    $ecCode     = str_replace('EC_', '', $key);
                    $score      = intval($value);
                    $remark     = $request->input('REMARK_' . $ecCode, '');
                    $ecMaxScore = intval($request->input('EC_MAX_SCORE_' . $ecCode, 0));

                    DB::table('PT_APPRAISAL_APPL_SCORES')->insert([
                        'APAS_COMP_CODE'           => $companyCode,
                        'APAS_TXN_CODE'            => $APRSL_TXN_CODE,
                        'APAS_TXN_NO'              => $APRSL_NO,
                        'APAS_TXN_DT'              => $APRH_TXN_DT,
                        'APAS_EMP_CODE'            => $EMP_CODE,
                        'APAS_REF_FROM'            => 'D',
                        'APAS_REF_TXN_CODE'        => $APRSL_TXN_CODE,
                        'APAS_REF_TXN_NO'          => $APRSL_NO,
                        'APAS_REF_SYS_ID'          => $APAH_SYS_ID,
                        'APAS_KPI_CODE'            => $kpiCode,
                        'APAS_KPI_EC_CODE'         => $ecCode,
                        'APAS_KPI_EC_MAX_SCORE'    => $ecMaxScore,
                        'APAS_KPI_EC_GIVEN_SCORE'  => $score,
                        'APAS_KPI_EC_GIVEN_REMARK' => $remark,
                        'APAS_CR_DT'               => DB::raw('CURRENT_TIMESTAMP'),
                        'APAS_CR_UID'              => $userId,
                    ]);
                }
            }

            //  Handle submit inside transaction before commit
            if ($request->has('SUBMIT_APPRAISAL')) {
                DB::table('PT_APPRAISAL_APPL_HEAD')
                    ->where('APAH_SYS_ID', $APAH_SYS_ID)
                    ->update([
                        'APAH_EVAL_STATUS' => '2',
                        'APAH_LAST_EVAL_KPI_CODE' => NULL,
                        'APAH_S1_APP_UID'  => $userId,
                        'APAH_S1_APP_DT'   => now(),
                        'APAH_UPD_UID'     => $userId,
                        'APAH_UPD_DT'      => DB::raw('CURRENT_TIMESTAMP'),
                    ]);
            }

            //  Commit everything together
            DB::commit();

            echo $request->has('SUBMIT_APPRAISAL') ? 'APPRAISAL_SUBMITED' : 'APPRAISAL_SAVED';
        } catch (\Exception $e) {
            DB::rollBack();
            echo 'APPRAISAL_SAVE_FAILED: ' . $e->getMessage();
        }
    }


    // show appraisal suggestions to am and hr
    public function ShowAppraisalSuggestions()
    {

        $LOGIN_USER_ID = session('user_id');

        $appraisals = DB::select("
                            SELECT *
                            FROM PT_APPRAISAL_APPL_HEAD H
                            JOIN EMP_AUTH_USER A 
                                ON A.EAU_EMP_CODE = H.APAH_EMP_CODE
                            JOIN PM_EMP_KEY EMP
                                ON EMP.EMP_CODE = H.APAH_EMP_CODE
                            WHERE A.EAU_CODE = '$LOGIN_USER_ID'
                            AND A.EAU_FRZ_FLAG = 'N'
                            AND A.EAU_SEQ_NO IN (2,3)
                            AND (

                                /************* SUPERVISOR 2 FLOW *************/
                                (
                                    A.EAU_SEQ_NO = 2
                                    AND (
                                        (
                                            (
                                                EXISTS (
                                                    SELECT 1 FROM EMP_AUTH_USER EA1
                                                    WHERE EA1.EAU_EMP_CODE = H.APAH_EMP_CODE
                                                    AND EA1.EAU_SEQ_NO = 1
                                                    AND EA1.EAU_FRZ_FLAG = 'N'
                                                )
                                                AND H.APAH_S1_APP_UID IS NOT NULL
                                                AND H.APAH_S1_REJ_UID IS NULL
                                            )
                                            OR
                                            (
                                                NOT EXISTS (
                                                    SELECT 1 FROM EMP_AUTH_USER EA1
                                                    WHERE EA1.EAU_EMP_CODE = H.APAH_EMP_CODE
                                                    AND EA1.EAU_SEQ_NO = 1
                                                    AND EA1.EAU_FRZ_FLAG = 'N'
                                                )
                                            )
                                        )
                                        AND H.APAH_S2_APP_UID IS NULL
                                        AND H.APAH_S2_REJ_UID IS NULL
                                    )

                                    OR

                                    -- Already Approved
                                    (H.APAH_S2_APP_UID = '$LOGIN_USER_ID')
                                )

                                /************* HR FLOW *************/
                                OR
                                (
                                    A.EAU_SEQ_NO = 3
                                    AND (
                                        (
                                            (
                                                NOT EXISTS (
                                                    SELECT 1 FROM EMP_AUTH_USER EA1
                                                    WHERE EA1.EAU_EMP_CODE = H.APAH_EMP_CODE
                                                    AND EA1.EAU_SEQ_NO = 1
                                                    AND EA1.EAU_FRZ_FLAG = 'N'
                                                )
                                                OR (H.APAH_S1_APP_UID IS NOT NULL AND H.APAH_S1_REJ_UID IS NULL)
                                            )
                                            AND (
                                                (
                                                    NOT EXISTS (
                                                        SELECT 1 FROM EMP_AUTH_USER EA2
                                                        WHERE EA2.EAU_EMP_CODE = H.APAH_EMP_CODE
                                                        AND EA2.EAU_SEQ_NO = 2
                                                        AND EA2.EAU_FRZ_FLAG = 'N'
                                                    )
                                                    OR (H.APAH_S2_APP_UID IS NOT NULL AND H.APAH_S2_REJ_UID IS NULL)
                                                )
                                            )
                                            AND H.APAH_HR_APP_UID IS NULL
                                            AND H.APAH_HR_REJ_UID IS NULL
                                        )

                                        OR

                                        -- Already Approved
                                        (H.APAH_HR_APP_UID = '$LOGIN_USER_ID')
                                    )
                                )
                            )
                            ORDER BY H.APAH_CR_DT DESC
        ");

        return view('APPRAISAL.Approve_Appraisal', compact('appraisals'));
    }

    // show approve appraisal form function
    // public function ShowApproveAppraisalForm()
    // {
    //     $FORM_TYPE = 'APPROVE_APPRAISAL';

    //     $APAH_SYS_ID = request('appr_code');

    //     $APPRAISAL_DATA = DB::table('PT_APPRAISAL_APPL_HEAD')
    //         ->where('APAH_SYS_ID', $APAH_SYS_ID)
    //         ->first();

    //     $APRSL_EMP_CODE = $APPRAISAL_DATA->APAH_EMP_CODE;

    //     $LOGIN_USER_ID = session('user_id');
    //     $LOGIN_COMPANY_CODE = session('company_code');

    //     // variable to check if the evaluation already started
    //     $EVAL_STARTED = false;


    //     // Fetch employee data
    //     $APRSL_EMP = DB::table('PM_EMP_KEY')
    //         ->select(
    //             'PM_EMP_KEY.EMP_CODE',
    //             'PM_EMP_KEY.EMP_NAME',
    //             'PM_EMP_KEY.EMP_REVIEW_BY',
    //             'EMP.EMP_NAME AS REPORTING_TO',
    //             'FM_DEPARTMENT.DEPT_NAME',
    //             'FM_COMPANY.COMP_NAME',
    //             'PM_JOB_TITLE.JOB_TITLE_DESC',
    //             'PM_JOB_TITLE.JOB_TITLE_CODE',
    //             'PM_JOB_TITLE.JOB_TITLE_APPRAISAL_START_MONTH',
    //             'PM_JOB_TITLE.JOB_TITLE_MAX_APPRAISAL_DURATION',
    //             'PM_JOB_TITLE.JOB_TITLE_APPRAISAL_FREQ',
    //             'FM_DIVISION.DIVN_NAME',
    //             'PM_EMP_KEY.EMP_DEPT_CODE'
    //         )
    //         ->leftJoin('FM_DEPARTMENT', 'FM_DEPARTMENT.DEPT_CODE', '=', 'PM_EMP_KEY.EMP_DEPT_CODE')
    //         ->leftJoin('PM_JOB_TITLE', 'PM_JOB_TITLE.JOB_TITLE_CODE', '=', 'PM_EMP_KEY.EMP_JOB_TITLE_CODE')
    //         ->leftJoin('FM_DIVISION', 'FM_DIVISION.DIVN_CODE', '=', 'PM_EMP_KEY.EMP_DIVN_CODE')
    //         ->leftJoin('FM_COMPANY', 'FM_COMPANY.COMP_CODE', '=', 'PM_EMP_KEY.EMP_COMP_CODE')
    //         ->leftJoin('PM_EMP_KEY AS EMP', 'EMP.EMP_CODE', '=', 'PM_EMP_KEY.EMP_REPORTING_TO')
    //         ->where('PM_EMP_KEY.EMP_CODE', $APRSL_EMP_CODE)
    //         ->first();

    //     // fetching txn no and increading it
    //     $TXN_NO = DB::table('PT_APPRAISAL_APPL_HEAD')
    //         ->where('APAH_SYS_ID', $APAH_SYS_ID)
    //         ->first();

    //     $TXN_CODE = 'APRSL' . date('Y') . '_' . str_pad(($TXN_NO->APAH_TXN_NO), 3, '0', STR_PAD_LEFT);;
    //     $SYS_ID = $APAH_SYS_ID;

    //     $HR_ID = $APRSL_EMP->EMP_REVIEW_BY;
    //     $IS_HR_ID = false;

    //     if ($HR_ID == $LOGIN_USER_ID) {
    //         $IS_HR_ID = true;
    //     }

    //     // checking if the application already approved
    //     $IS_APPROVED = false;
    //     $APPR_APP_REMARK = '';
    //     $APPR_APP_SUGGESTION = '';
    //     $APPR_AREA_OF_IMPRO = '';
    //     $APPR_DEV_PLAN = '';
    //     $SEQ_NO = DB::table('EMP_AUTH_USER')
    //         ->where('EAU_EMP_CODE', $APRSL_EMP_CODE)
    //         ->where('EAU_CODE', $LOGIN_USER_ID)
    //         ->first();

    //     $APP_SEQ_ =  (int)$SEQ_NO->EAU_SEQ_NO;
    //     if ($APP_SEQ_ == 2 && !empty($TXN_NO->APAH_S2_APP_UID)) {
    //         $IS_APPROVED = true;
    //         $APPR_APP_REMARK = $TXN_NO->APAH_S2_APP_REMARK;
    //         $APPR_APP_SUGGESTION = $TXN_NO->APAH_S2_APP_SUGGESTION;
    //     } elseif ($APP_SEQ_ == 3 && !empty($TXN_NO->APAH_HR_APP_UID)) {
    //         $IS_APPROVED = true;
    //         $APPR_APP_REMARK = $TXN_NO->APAH_HR_APP_REMARK;
    //         $APPR_APP_SUGGESTION = $TXN_NO->APAH_HR_APP_SUGGESTION;
    //         $APPR_AREA_OF_IMPRO = $TXN_NO->APAH_HR_APP_AOI;
    //         $APPR_DEV_PLAN = $TXN_NO->APAH_HR_APP_DEV_PLAN;
    //     }



    //     $APRSL_DATE = $TXN_NO->APAH_TXN_DT;
    //     $APAH_TXN_NO = $TXN_NO->APAH_TXN_NO;


    //     // Extract employee position code and get position name
    //     $EMP_POSI_CODE = $APRSL_EMP->JOB_TITLE_CODE ?? null;
    //     $POSITION = $APRSL_EMP->JOB_TITLE_DESC ?? 'N/A';

    //     // Get department info
    //     $EMP_DEPT_CODE = $APRSL_EMP->EMP_DEPT_CODE ?? null;
    //     $DEPARTMENT_NAME = $APRSL_EMP->DEPT_NAME ?? 'N/A';

    //     // FETCHING SCORES
    //     $APPRASAL_SCORES = DB::table('PT_APPRAISAL_APPL_SCORES')
    //         ->leftJoin('PM_APPRAISAL_KPI', 'PM_APPRAISAL_KPI.KPI_SYS_ID', '=', 'PT_APPRAISAL_APPL_SCORES.APAS_KPI_CODE')
    //         ->leftJoin('PM_APPRAISAL_KPI_EC', 'PM_APPRAISAL_KPI_EC.KPI_EC_SYS_ID', '=', 'PT_APPRAISAL_APPL_SCORES.APAS_KPI_EC_CODE')
    //         ->where('APAS_REF_SYS_ID', $APAH_SYS_ID)
    //         ->where('APAS_REF_TXN_NO', $APAH_TXN_NO)
    //         ->get();

    //     $KPI_CODE = array();
    //     $KPI_DESC = array();
    //     $EC_DATA = array();

    //     if ($APPRASAL_SCORES->isNotEmpty()) {
    //         foreach ($APPRASAL_SCORES as $score) {

    //             // Adding KPI code only if not already added
    //             if (!in_array($score->APAS_KPI_CODE, $KPI_CODE)) {
    //                 $KPI_CODE[] = $score->APAS_KPI_CODE;
    //                 $KPI_DESC[$score->APAS_KPI_CODE] = $score->KPI_DESC;
    //             }

    //             // Group EC data under each KPI
    //             $EC_DATA[$score->APAS_KPI_CODE][] = [
    //                 'CODE'        => $score->APAS_KPI_EC_CODE,
    //                 'DESC'        => $score->KPI_EC_DESC,
    //                 'GIVEN_SCORE' => $score->APAS_KPI_EC_GIVEN_SCORE,
    //                 'MAX_SCORE'   => $score->APAS_KPI_EC_MAX_SCORE,
    //                 'REMARK'      => $score->APAS_KPI_EC_GIVEN_REMARK,
    //             ];
    //         }
    //     }


    //     return view('APPRAISAL.approve_appraisal_form', compact(
    //         'FORM_TYPE',
    //         'APRSL_EMP',
    //         'TXN_CODE',
    //         'SYS_ID',
    //         'EMP_POSI_CODE',
    //         'POSITION',
    //         'EMP_DEPT_CODE',
    //         'DEPARTMENT_NAME',
    //         'LOGIN_USER_ID',
    //         'APRSL_DATE',
    //         'KPI_CODE',
    //         'KPI_DESC',
    //         'EC_DATA',
    //         'IS_HR_ID',
    //         'IS_APPROVED',
    //         'APPR_APP_REMARK',
    //         'APPR_APP_SUGGESTION',
    //         'APPR_AREA_OF_IMPRO',
    //         'APPR_DEV_PLAN',
    //     ));
    // }

    public function ShowApproveAppraisalForm()
    {
        $FORM_TYPE = 'APPROVE_APPRAISAL';

        $APAH_SYS_ID = request('appr_code');

        $APPRAISAL_DATA = DB::table('PT_APPRAISAL_APPL_HEAD')
            ->where('APAH_SYS_ID', $APAH_SYS_ID)
            ->first();

        $APRSL_EMP_CODE = $APPRAISAL_DATA->APAH_EMP_CODE;

        $LOGIN_USER_ID = session('user_id');
        $LOGIN_COMPANY_CODE = session('company_code');

        // variable to check if the evaluation already started
        $EVAL_STARTED = false;


        // Fetch employee data
        $APRSL_EMP = DB::table('PM_EMP_KEY')
            ->select(
                'PM_EMP_KEY.EMP_CODE',
                'PM_EMP_KEY.EMP_NAME',
                'PM_EMP_KEY.EMP_REVIEW_BY',
                'EMP.EMP_NAME AS REPORTING_TO',
                'FM_DEPARTMENT.DEPT_NAME',
                'FM_COMPANY.COMP_NAME',
                'PM_JOB_TITLE.JOB_TITLE_DESC',
                'PM_JOB_TITLE.JOB_TITLE_CODE',
                'PM_JOB_TITLE.JOB_TITLE_APPRAISAL_START_MONTH',
                'PM_JOB_TITLE.JOB_TITLE_MAX_APPRAISAL_DURATION',
                'PM_JOB_TITLE.JOB_TITLE_APPRAISAL_FREQ',
                'FM_DIVISION.DIVN_NAME',
                'PM_EMP_KEY.EMP_DEPT_CODE'
            )
            ->leftJoin('FM_DEPARTMENT', 'FM_DEPARTMENT.DEPT_CODE', '=', 'PM_EMP_KEY.EMP_DEPT_CODE')
            ->leftJoin('PM_JOB_TITLE', 'PM_JOB_TITLE.JOB_TITLE_CODE', '=', 'PM_EMP_KEY.EMP_JOB_TITLE_CODE')
            ->leftJoin('FM_DIVISION', 'FM_DIVISION.DIVN_CODE', '=', 'PM_EMP_KEY.EMP_DIVN_CODE')
            ->leftJoin('FM_COMPANY', 'FM_COMPANY.COMP_CODE', '=', 'PM_EMP_KEY.EMP_COMP_CODE')
            ->leftJoin('PM_EMP_KEY AS EMP', 'EMP.EMP_CODE', '=', 'PM_EMP_KEY.EMP_REPORTING_TO')
            ->where('PM_EMP_KEY.EMP_CODE', $APRSL_EMP_CODE)
            ->first();

        // fetching txn no and increading it
        $TXN_NO = DB::table('PT_APPRAISAL_APPL_HEAD')
            ->where('APAH_SYS_ID', $APAH_SYS_ID)
            ->first();

        $TXN_CODE = 'APRSL' . date('Y') . '_' . str_pad(($TXN_NO->APAH_TXN_NO), 3, '0', STR_PAD_LEFT);;
        $SYS_ID = $APAH_SYS_ID;

        $HR_ID = $APRSL_EMP->EMP_REVIEW_BY;
        $IS_HR_ID = false;

        if ($HR_ID == $LOGIN_USER_ID) {
            $IS_HR_ID = true;
        }

        $APRSL_DATE = $TXN_NO->APAH_TXN_DT;
        $APAH_TXN_NO = $TXN_NO->APAH_TXN_NO;


        // Extract employee position code and get position name
        $EMP_POSI_CODE = $APRSL_EMP->JOB_TITLE_CODE ?? null;
        $POSITION = $APRSL_EMP->JOB_TITLE_DESC ?? 'N/A';

        // Get department info
        $EMP_DEPT_CODE = $APRSL_EMP->EMP_DEPT_CODE ?? null;
        $DEPARTMENT_NAME = $APRSL_EMP->DEPT_NAME ?? 'N/A';


        // need these things
        $KPI_CODE = array();
        $KPI_DESC = array();
        $EC_DATA = array();
        $IS_HR_ID = '';
        $IS_APPROVED = '';
        $APPR_APP_REMARK = '';
        $APPR_APP_SUGGESTION = '';
        $APPR_AREA_OF_IMPRO = '';
        $APPR_DEV_PLAN = '';


        // new part to make new approval form
        $KPI_CODE_2 = '';
        $KPI_DESC_2 = '';
        $EC_DATA_2 = '';


        // Fetch KPI data for this job title
        $linkedData = DB::table('PM_KPI_ROLE_ASSINGMENT')
            ->select('KRA_KPI_SYS_ID', 'KRA_EC_SYS_ID')
            ->where('KRA_JOB_CODE', $EMP_POSI_CODE)
            ->get();

        $ALL_KPI_CODE_ARRAY = array();
        $ALL_ECS_CODE_ARRAY = array();

        foreach ($linkedData as $data) {
            $ALL_KPI_CODE_ARRAY[] = $data->KRA_KPI_SYS_ID;
            $ALL_ECS_CODE_ARRAY[$data->KRA_KPI_SYS_ID] = $data->KRA_EC_SYS_ID;
        }


        // FINDING WHO IS EVALUATING THE EMPLOYEE
        $EVALUATOR_IS = DB::table('EMP_AUTH_USER')
            ->select('EAU_SEQ_NO')
            ->where('EAU_EMP_CODE', $APRSL_EMP_CODE)
            ->where('EAU_CODE', $LOGIN_USER_ID)
            ->first();


        // echo $EVALUATOR_IS->EAU_SEQ_NO;
        $IS_HR = false;
        $IS_APPROVED_ALREADY = false;

        $EVALUATOR_SEQ_NO = $EVALUATOR_IS->EAU_SEQ_NO;
        if ($EVALUATOR_SEQ_NO == '2' && $APPRAISAL_DATA->APAH_S2_APP_UID != null) {
            $IS_APPROVED_ALREADY = true;
        } elseif ($EVALUATOR_SEQ_NO == '3' && $APPRAISAL_DATA->APAH_HR_APP_UID != null) {
            $IS_APPROVED_ALREADY = true;
        }

        // var_dump($IS_APPROVED_ALREADY);



        $KPI_TO_THIS_ID = array();
        foreach ($ALL_KPI_CODE_ARRAY as $KPI) {
            if ($EVALUATOR_IS->EAU_SEQ_NO == '2') {
                $KPI_ASS_DATA = DB::table('PM_APPRAISAL_KPI')
                    ->select('KPI_SYS_ID')
                    ->where('KPI_APPROVE_BY_AM', 'Y')
                    ->where('KPI_SYS_ID', $KPI)
                    ->first();
                if ($KPI_ASS_DATA) {
                    $KPI_TO_THIS_ID[] = $KPI_ASS_DATA->KPI_SYS_ID;
                }
            } elseif ($EVALUATOR_IS->EAU_SEQ_NO == '3') {
                $IS_HR = true;
                $KPI_ASS_DATA = DB::table('PM_APPRAISAL_KPI')
                    ->select('KPI_SYS_ID')
                    ->where('KPI_APPROVE_BY_HR', 'Y')
                    ->where('KPI_SYS_ID', $KPI)
                    ->first();
                if ($KPI_ASS_DATA) {
                    $KPI_TO_THIS_ID[] = $KPI_ASS_DATA->KPI_SYS_ID;
                }
            }
        }


        // var_dump($KPI_TO_THIS_ID);

        // LAST KPI EVALUDATED DETAILS
        $LAST_KPI_EVAL_CODE = $APPRAISAL_DATA->APAH_LAST_EVAL_KPI_CODE ?? '';
        $APAH_EVAL_STATUS = $APPRAISAL_DATA->APAH_EVAL_STATUS;


        $LAST_KPI_INDEX = 0;
        $NEXT_KPI_EVAL_CODE = '';

        if ($LAST_KPI_EVAL_CODE != null) {
            $LAST_KPI_INDEX = array_search($LAST_KPI_EVAL_CODE, $KPI_TO_THIS_ID);
            $NEXT_KPI_EVAL_CODE = $KPI_TO_THIS_ID[$LAST_KPI_INDEX + 1] ?? null;
            $LAST_KPI_INDEX += 1;
        } else {
            $NEXT_KPI_EVAL_CODE = $KPI_TO_THIS_ID[0] ?? null;
        }

        // echo $NEXT_KPI_EVAL_CODE;


        $ECS_CODE = $ALL_ECS_CODE_ARRAY[$NEXT_KPI_EVAL_CODE] ?? null;

        $FINAL_SUBMIT = false;


        // kpi details
        $KPI_CODE = array();
        $KPI_DESC = array();

        // SEPERATING EC CODE 
        $EC_DESC = array();
        $EC_CODE = array();
        $EC_MAX = array();
        $EC_MIN = array();
        $EC_GIVEN_SCORE = array();
        $EC_DATA = array();

        // echo $LAST_KPI_INDEX;
        if ($LAST_KPI_INDEX === count($KPI_TO_THIS_ID) || $IS_APPROVED_ALREADY) {
            $FINAL_SUBMIT = true;

            foreach ($ALL_KPI_CODE_ARRAY as $kpiCode) {

                $kpiData = DB::table('PM_APPRAISAL_KPI')
                    ->where('KPI_SYS_ID', $kpiCode)
                    ->where('KPI_FRZ_FLAG', 'N')
                    ->first();

                if (!$kpiData) continue;

                $KPI_CODE[] = $kpiData->KPI_SYS_ID;
                $KPI_DESC[$kpiData->KPI_SYS_ID] = $kpiData->KPI_DESC ?? null;

                //  Group EC data under each KPI code
                $EC_CODE_ARRAY = explode(',', $ALL_ECS_CODE_ARRAY[$kpiCode]);

                foreach ($EC_CODE_ARRAY as $ecCode) {
                    $ecCode = trim($ecCode);

                    // Fetch ALL scores for this EC (all authorities)
                    $ecScores = DB::table('PT_APPRAISAL_APPL_SCORES')
                        ->join('PM_APPRAISAL_KPI_EC', 'PM_APPRAISAL_KPI_EC.KPI_EC_SYS_ID', '=', 'PT_APPRAISAL_APPL_SCORES.APAS_KPI_EC_CODE')
                        ->where('PT_APPRAISAL_APPL_SCORES.APAS_KPI_EC_CODE', $ecCode)
                        ->where('PT_APPRAISAL_APPL_SCORES.APAS_KPI_CODE', $kpiCode)
                        ->where('PT_APPRAISAL_APPL_SCORES.APAS_REF_SYS_ID', $SYS_ID)
                        ->where('PT_APPRAISAL_APPL_SCORES.APAS_REF_TXN_NO', $APAH_TXN_NO)
                        // ->where('PT_APPRAISAL_APPL_SCORES.APAS_COMP_CODE', $LOGIN_COMPANY_CODE)
                        ->get();

                    foreach ($ecScores as $ecData) {
                        $EC_DATA[$kpiCode][] = [
                            'code'        => $ecData->KPI_EC_SYS_ID,
                            'desc'        => $ecData->KPI_EC_DESC,
                            'max'         => $ecData->KPI_EC_MAX_RANGE,
                            'min'         => $ecData->KPI_EC_MIN_RANGE,
                            'given_score' => $ecData->APAS_KPI_EC_GIVEN_SCORE,
                            'eval_by'     => $ecData->APAS_CR_UID,
                        ];
                    }
                }
            }
        } else {
            // Fetch KPI data
            $kpiData = DB::table('PM_APPRAISAL_KPI')
                ->where('KPI_SYS_ID', $NEXT_KPI_EVAL_CODE)
                ->where('KPI_FRZ_FLAG', 'N')
                ->first();

            $KPI_CODE[] = $kpiData->KPI_SYS_ID ?? null;
            $KPI_DESC[$kpiData->KPI_SYS_ID] = $kpiData->KPI_DESC ?? null;

            // Separate EC codes
            $EC_CODE_ARRAY = explode(',', $ECS_CODE);

            foreach ($EC_CODE_ARRAY as $ecCode) {
                $ecCode = trim($ecCode); //  trim spaces

                $ecData = DB::table('PM_APPRAISAL_KPI_EC')
                    ->where('KPI_EC_SYS_ID', $ecCode)
                    ->where('KPI_EC_FRZ_FLAG', 'N')
                    ->first();

                if ($ecData) {
                    //  All arrays keyed by EC SYS_ID consistently
                    $EC_CODE[]                       = $ecData->KPI_EC_SYS_ID;
                    $EC_DESC[$ecData->KPI_EC_SYS_ID] = $ecData->KPI_EC_DESC;
                    $EC_MAX[$ecData->KPI_EC_SYS_ID]  = $ecData->KPI_EC_MAX_RANGE;
                    $EC_MIN[$ecData->KPI_EC_SYS_ID]  = $ecData->KPI_EC_MIN_RANGE;
                }
            }
        }



        // calculating form evaluation percentage
        $PROGRESS = count($KPI_TO_THIS_ID) > 0 ? round((($LAST_KPI_INDEX) / count($KPI_TO_THIS_ID)) * 100, 2) : 0;


        return view('APPRAISAL.approve_appraisal_form', compact(
            'APRSL_EMP',
            'TXN_CODE',
            'SYS_ID',
            'EMP_POSI_CODE',
            'POSITION',
            'EMP_DEPT_CODE',
            'DEPARTMENT_NAME',
            'LOGIN_USER_ID',
            'KPI_CODE',
            'KPI_DESC',
            'EC_CODE',
            'EC_DESC',
            'EC_MAX',
            'EC_MIN',
            'EC_DATA',
            'EC_GIVEN_SCORE',
            'PROGRESS',
            'FINAL_SUBMIT',
            'LAST_KPI_INDEX',
            'APAH_EVAL_STATUS',
            'FORM_TYPE',
            'APRSL_DATE',
            'IS_HR',
            'IS_APPROVED_ALREADY',
        ));
    }

    // save approveed apprasial data 
    // public function SaveAppraisalDataByAMHR(Request $request)
    // {

    //     $LOGIN_USER_ID = session('user_id');
    //     $APAH_SYS_ID = request('APPRAISAL_SYS_ID');
    //     $EMP_CODE = request('EMP_CODE');
    //     $APPR_APP_SUGGESTION = request('APPR_APP_SUGGESTION');
    //     $APPR_APP_REMARK = request('APPR_APP_REMARK');
    //     $APPR_AREA_OF_IMPRO = request('APPR_AREA_OF_IMPRO');
    //     $APPR_DEV_PLAN = request('APPR_DEV_PLAN');

    //     $SEQ_NO = DB::table('EMP_AUTH_USER')
    //         ->where('EAU_EMP_CODE', $EMP_CODE)
    //         ->where('EAU_CODE', $LOGIN_USER_ID)
    //         ->first();

    //     $APP_SEQ_ =  (int)$SEQ_NO->EAU_SEQ_NO;

    //     if ($APP_SEQ_ == 2) {
    //         $UPDATE = DB::table('PT_APPRAISAL_APPL_HEAD')
    //             ->where('APAH_SYS_ID', $APAH_SYS_ID)
    //             ->update([
    //                 'APAH_S2_APP_UID'                => $LOGIN_USER_ID,
    //                 'APAH_S2_APP_REMARK'             => $APPR_APP_REMARK,
    //                 'APAH_S2_APP_SUGGESTION'         => $APPR_APP_SUGGESTION,
    //                 'APAH_S2_APP_DT'                 => now(),
    //                 'APAH_UPD_DT'                    => DB::raw('CURRENT_TIMESTAMP'),
    //                 'APAH_UPD_UID'                   => $LOGIN_USER_ID,
    //             ]);
    //     } elseif ($APP_SEQ_ == 3) {
    //         $UPDATE = DB::table('PT_APPRAISAL_APPL_HEAD')
    //             ->where('APAH_SYS_ID', $APAH_SYS_ID)
    //             ->update([
    //                 'APAH_TXN_STATUS'                => '2',
    //                 'APAH_HR_APP_UID'                => $LOGIN_USER_ID,
    //                 'APAH_HR_APP_REMARK'             => $APPR_APP_REMARK,
    //                 'APAH_HR_APP_SUGGESTION'         => $APPR_APP_SUGGESTION,
    //                 'APAH_HR_APP_AOI'                => $APPR_AREA_OF_IMPRO,
    //                 'APAH_HR_APP_DEV_PLAN'           => $APPR_DEV_PLAN,
    //                 'APAH_HR_APP_DT'                 => now(),
    //                 'APAH_UPD_DT'                    => DB::raw('CURRENT_TIMESTAMP'),
    //                 'APAH_UPD_UID'                   => $LOGIN_USER_ID,
    //             ]);
    //     }

    //     if ($UPDATE) {
    //         echo 'APPRAISAL_APPROVED';
    //     } else {
    //         echo "FAILED_TO_APPROVE_APPRAISAL";
    //     }
    // }
    // new code for diffrent type of appraisal 
    public function SaveAppraisalDataByAMHR(Request $request)
    {

        $companyCode         = session('company_code');
        $LOGIN_USER_ID       = session('user_id');
        $APAH_SYS_ID         = $request->APPRAISAL_SYS_ID;

        $EMP_CODE            = $request->EMP_CODE;
        $EMP_POSITION_CODE   = $request->EMP_POSITION_CODE;
        $KPI_CODE            = $request->KPI_CODE;

        $APPR_APP_SUGGESTION   = $request->APPR_APP_SUGGESTION ?? '';
        $APPR_APP_REMARK       = $request->APPR_APP_REMARK ?? '';
        $APPR_AREA_OF_IMPRO    = $request->APPR_AREA_OF_IMPRO ?? '';
        $APPR_DEV_PLAN         = $request->APPR_DEV_PLAN ?? '';


        $APRSL_TXN_CODE      = '';
        $APRSL_NO            = '';
        $APRH_TXN_DT         = date('Y-m-d');

        DB::beginTransaction();

        try {

            $CHECK_EVAL = DB::table('PT_APPRAISAL_APPL_HEAD')
                ->where('APAH_EMP_CODE', $EMP_CODE)
                ->where('APAH_SYS_ID', $APAH_SYS_ID)
                ->first();

            if ($CHECK_EVAL) {
                $APRSL_TXN_CODE = $CHECK_EVAL->APAH_TXN_CODE;
                $APRSL_NO       = $CHECK_EVAL->APAH_TXN_NO;
            }

            // Update KPI code in head on every save
            DB::table('PT_APPRAISAL_APPL_HEAD')
                ->where('APAH_SYS_ID', $APAH_SYS_ID)
                ->update([
                    'APAH_EVAL_STATUS'        => '1',
                    'APAH_LAST_EVAL_KPI_CODE' => $KPI_CODE,
                    'APAH_UPD_DT'             => DB::raw('CURRENT_TIMESTAMP'),
                    'APAH_UPD_UID'            => $LOGIN_USER_ID,
                ]);



            // Save EC scores with alphanumeric pattern
            foreach ($request->all() as $key => $value) {
                if (preg_match('/^EC_[A-Za-z0-9]+$/', $key)) {

                    $ecCode     = str_replace('EC_', '', $key);
                    $score      = intval($value);
                    $remark     = $request->input('REMARK_' . $ecCode, '');
                    $ecMaxScore = intval($request->input('EC_MAX_SCORE_' . $ecCode, 0));

                    DB::table('PT_APPRAISAL_APPL_SCORES')->insert([
                        'APAS_COMP_CODE'           => $companyCode,
                        'APAS_TXN_CODE'            => $APRSL_TXN_CODE,
                        'APAS_TXN_NO'              => $APRSL_NO,
                        'APAS_TXN_DT'              => $APRH_TXN_DT,
                        'APAS_EMP_CODE'            => $EMP_CODE,
                        'APAS_REF_FROM'            => 'D',
                        'APAS_REF_TXN_CODE'        => $APRSL_TXN_CODE,
                        'APAS_REF_TXN_NO'          => $APRSL_NO,
                        'APAS_REF_SYS_ID'          => $APAH_SYS_ID,
                        'APAS_KPI_CODE'            => $KPI_CODE,
                        'APAS_KPI_EC_CODE'         => $ecCode,
                        'APAS_KPI_EC_MAX_SCORE'    => $ecMaxScore,
                        'APAS_KPI_EC_GIVEN_SCORE'  => $score,
                        'APAS_KPI_EC_GIVEN_REMARK' => $remark,
                        'APAS_CR_DT'               => DB::raw('CURRENT_TIMESTAMP'),
                        'APAS_CR_UID'              => $LOGIN_USER_ID,
                    ]);
                }
            }


            $IS_HR = false;

            //  Handle submit inside transaction before commit
            if ($request->has('SUBMIT_APPRAISAL')) {


                // FINDING WHO IS EVALUATOR
                $EVALUATOR_IS = DB::table('EMP_AUTH_USER')
                    ->select('EAU_SEQ_NO')
                    ->where('EAU_EMP_CODE', $EMP_CODE)
                    ->where('EAU_CODE', $LOGIN_USER_ID)
                    ->first();

                if ($EVALUATOR_IS->EAU_SEQ_NO == '2') {
                    DB::table('PT_APPRAISAL_APPL_HEAD')
                        ->where('APAH_SYS_ID', $APAH_SYS_ID)
                        ->update([
                            'APAH_EVAL_STATUS' => '2',
                            'APAH_LAST_EVAL_KPI_CODE' => NULL,
                            'APAH_S2_APP_UID'  => $LOGIN_USER_ID,
                            'APAH_S2_APP_DT'   => now(),

                            'APAH_S2_APP_REMARK'   => $APPR_APP_REMARK,
                            'APAH_S2_APP_SUGGESTION'   => $APPR_APP_SUGGESTION,

                            'APAH_UPD_UID'     => $LOGIN_USER_ID,
                            'APAH_UPD_DT'      => DB::raw('CURRENT_TIMESTAMP'),
                        ]);
                } elseif ($EVALUATOR_IS->EAU_SEQ_NO == '3') {
                    $IS_HR = true;
                    DB::table('PT_APPRAISAL_APPL_HEAD')
                        ->where('APAH_SYS_ID', $APAH_SYS_ID)
                        ->update([
                            'APAH_EVAL_STATUS' => '2',
                            'APAH_TXN_STATUS' => '2',
                            'APAH_LAST_EVAL_KPI_CODE' => NULL,
                            'APAH_HR_APP_UID'  => $LOGIN_USER_ID,
                            'APAH_HR_APP_DT'   => now(),

                            'APAH_HR_APP_REMARK'   => $APPR_APP_REMARK,
                            'APAH_HR_APP_SUGGESTION'   => $APPR_APP_SUGGESTION,
                            'APAH_HR_APP_AOI'   => $APPR_AREA_OF_IMPRO,
                            'APAH_HR_APP_DEV_PLAN'   => $APPR_DEV_PLAN,

                            'APAH_UPD_UID'     => $LOGIN_USER_ID,
                            'APAH_UPD_DT'      => DB::raw('CURRENT_TIMESTAMP'),
                        ]);
                }
            }

            //  Commit everything together
            DB::commit();

            if ($IS_HR) {
                echo $request->has('SUBMIT_APPRAISAL') ? 'APPRAISAL_APPROVED' : 'APPRAISAL_SAVED';
            } else {
                echo $request->has('SUBMIT_APPRAISAL') ? 'APPRAISAL_SUBMITED' : 'APPRAISAL_SAVED';
            }

            //--
        } catch (\Exception $e) {
            DB::rollBack();
            echo 'APPRAISAL_SAVE_FAILED: ' . $e->getMessage();
        }
    }


    // download approved appraisal final print
    public function DownloadAppraisalFinalPrint(Request $request)
    {
        $APPR_SYS_ID = $request->download_appr_code;

        if (empty($APPR_SYS_ID)) {
            return redirect()->back()->with('error', 'Appraisal code not found');
        }

        //  Fetch appraisal head data
        $TXN_DATA = DB::table('PT_APPRAISAL_APPL_HEAD')
            ->where('APAH_SYS_ID', $APPR_SYS_ID)
            ->first();

        if (!$TXN_DATA) {
            return redirect()->back()->with('error', 'Appraisal not found');
        }

        $EMP_CODE    = $TXN_DATA->APAH_EMP_CODE;
        $APAH_TXN_NO = $TXN_DATA->APAH_TXN_NO;
        $APAH_SYS_ID = $TXN_DATA->APAH_SYS_ID;
        $SUP_ID      = $TXN_DATA->APAH_S1_APP_UID;

        //  Fetch employee details with all joins (single query instead of multiple)
        $EMP_DATA = DB::table('PM_EMP_KEY')
            ->select(
                'PM_EMP_KEY.EMP_CODE',
                'PM_EMP_KEY.EMP_NAME',
                'PM_EMP_KEY.EMP_PHOTO',
                'PM_EMP_KEY.EMP_DIVN_CODE',
                'PM_EMP_KEY.EMP_DEPT_CODE',
                'PM_EMP_KEY.EMP_COMP_CODE',
                'PM_EMP_KEY.EMP_JOB_TITLE_CODE',
                'FM_DIVISION.DIVN_NAME  AS SPONSOR_NAME',
                'FM_COMPANY.COMP_NAME   AS COMPANY_NAME',
                'PM_JOB_TITLE.JOB_TITLE_DESC AS POSITION',
                'FM_DEPARTMENT.DEPT_NAME AS DEPARTMENT_NAME'
            )
            ->leftJoin('FM_DIVISION',   'FM_DIVISION.DIVN_CODE',      '=', 'PM_EMP_KEY.EMP_DIVN_CODE')
            ->leftJoin('FM_COMPANY',    'FM_COMPANY.COMP_CODE',       '=', 'PM_EMP_KEY.EMP_COMP_CODE')
            ->leftJoin('PM_JOB_TITLE',  'PM_JOB_TITLE.JOB_TITLE_CODE', '=', 'PM_EMP_KEY.EMP_JOB_TITLE_CODE')
            ->leftJoin('FM_DEPARTMENT', 'FM_DEPARTMENT.DEPT_CODE',    '=', 'PM_EMP_KEY.EMP_DEPT_CODE')
            ->where('PM_EMP_KEY.EMP_CODE', $EMP_CODE)
            ->first();

        //  Fetch supervisor name
        $SUP_DATA = DB::table('PM_EMP_KEY')
            ->select('EMP_NAME')
            ->where('EMP_CODE', $SUP_ID)
            ->first();

        $SUPERVISOR_NAME = $SUP_DATA->EMP_NAME ?? 'N/A';

        //  Fetch scores grouped by KPI
        $SCORES = DB::table('PT_APPRAISAL_APPL_SCORES')
            ->leftJoin('PM_APPRAISAL_KPI',    'PM_APPRAISAL_KPI.KPI_SYS_ID',       '=', 'PT_APPRAISAL_APPL_SCORES.APAS_KPI_CODE')
            ->leftJoin('PM_APPRAISAL_KPI_EC', 'PM_APPRAISAL_KPI_EC.KPI_EC_SYS_ID', '=', 'PT_APPRAISAL_APPL_SCORES.APAS_KPI_EC_CODE')
            ->select(
                'PT_APPRAISAL_APPL_SCORES.*',
                'PM_APPRAISAL_KPI.KPI_DESC',
                'PM_APPRAISAL_KPI_EC.KPI_EC_DESC',
                'PM_APPRAISAL_KPI_EC.KPI_EC_MAX_RANGE',
                'PM_APPRAISAL_KPI_EC.KPI_EC_MIN_RANGE',
            )
            ->where('PT_APPRAISAL_APPL_SCORES.APAS_REF_SYS_ID', $APPR_SYS_ID)
            ->where('PT_APPRAISAL_APPL_SCORES.APAS_TXN_NO', $APAH_TXN_NO)
            ->orderBy('PT_APPRAISAL_APPL_SCORES.APAS_KPI_CODE', 'ASC')
            ->orderBy('PT_APPRAISAL_APPL_SCORES.APAS_KPI_EC_CODE', 'ASC')
            ->get();

        //  Group scores by KPI
        $groupedData = [];
        $MAX_SCORE   = 0;
        $TOTAL_SCORE = 0;

        foreach ($SCORES as $score) {
            $groupedData[$score->APAS_KPI_CODE][] = $score;
            $MAX_SCORE   += (int)$score->KPI_EC_MAX_RANGE;
            $TOTAL_SCORE += (int)$score->APAS_KPI_EC_GIVEN_SCORE;
        }

        //  Calculate performance level
        $PERFORMANCE_LEVEL = 'Low';
        if ($MAX_SCORE > 0) {
            $percentage = ($TOTAL_SCORE / $MAX_SCORE) * 100;
            if ($percentage > 90) {
                $PERFORMANCE_LEVEL = 'High';
            } elseif ($percentage > 80) {
                $PERFORMANCE_LEVEL = 'Medium';
            } else {
                $PERFORMANCE_LEVEL = 'Low';
            }
        }

        return view('APPRAISAL.Appraisal_print', compact(
            'TXN_DATA',
            'EMP_DATA',
            'SUPERVISOR_NAME',
            'groupedData',
            'MAX_SCORE',
            'TOTAL_SCORE',
            'PERFORMANCE_LEVEL',
        ))->render();
    }
}
