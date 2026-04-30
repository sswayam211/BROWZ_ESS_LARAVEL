<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class Header extends Component
{
    public $employee;

    public function __construct()
    {
        $employeeId = session('user_id');

        $this->employee = DB::table('PM_EMP_KEY')
            ->where('EMP_CODE', $employeeId)
            ->first();
    }

    public function render()
    {
        $LOGIN_USER_ID = session('user_id');
        // FINDING APPRAISAL PENDING NO's
        $APRSL_PENDING_NO = DB::select("
                                            SELECT H.*
                                            FROM PT_APPRAISAL_APPL_HEAD H
                                            JOIN EMP_AUTH_USER A 
                                                ON A.EAU_EMP_CODE = H.APAH_EMP_CODE
                                            WHERE A.EAU_CODE = '$LOGIN_USER_ID'
                                            AND A.EAU_FRZ_FLAG = 'N'
                                            AND A.EAU_SEQ_NO IN (2,3)
                                            AND (

                                                /************* SUPERVISOR 2 FLOW *************/
                                                (
                                                    A.EAU_SEQ_NO = 2
                                                    AND (
                                                            -- Case 1: S1 EXISTS and must be APPROVED
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

                                                            -- Case 2: S1 does NOT exist → No S1 Approval Required
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

                                                /************* HR FLOW *************/
                                                OR
                                                (
                                                    A.EAU_SEQ_NO = 3
                                                    AND (
                                                            -- S1 condition → if exists, must be approved
                                                            (
                                                                NOT EXISTS (
                                                                    SELECT 1 FROM EMP_AUTH_USER EA1
                                                                    WHERE EA1.EAU_EMP_CODE = H.APAH_EMP_CODE
                                                                    AND EA1.EAU_SEQ_NO = 1
                                                                    AND EA1.EAU_FRZ_FLAG = 'N'
                                                                )
                                                                OR (H.APAH_S1_APP_UID IS NOT NULL AND H.APAH_S1_REJ_UID IS NULL)
                                                            )
                                                        )
                                                    AND (
                                                            -- S2 condition → if exists, must be approved
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

                                            )
                                            ORDER BY H.APAH_CR_DT DESC
        ");


        return view('components.header',compact('APRSL_PENDING_NO'));
    }
}
