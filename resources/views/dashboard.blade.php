<x-header></x-header>


<style>
    .page-1 {
        margin-bottom: 0px;
    }

    @media screen and (max-width:560px) {
        .page-1 {
            margin-bottom: 0px;
        }
    }
</style>



<div class="row p-lg-5 p-md-4 p-1 py-4 page-1">
    <div class="col-lg-2">
        <div class="user-img d-lg-block d-none text-center">
            <img class="bg-white img-fluid" src="http://browzhr.com/andtradinghrms/public/{{ $employee->EMP_PHOTO }}" alt="User_Img">
            <div class="status mt-1 d-flex justify-content-md-center align-items-center">
                <div class="dot me-2 bg-green"></div>
                <p class=" me-2">Active</p>
            </div>
        </div>
    </div>
    <div class="col-lg-10">
        <div class="user-info">
            <!--<div class="qr text-end d-flex justify-content-lg-end justify-content-between" style="min-height: 100px;"  style="background:green;">
                <div class="user-img d-lg-none d-block text-end">
                    <img class="bg-white mx-4" height="100px" src="user.png" alt="User_Img">
                    <div class="status mt-1 d-flex justify-content-center align-items-center">
                        <div class="dot me-2 bg-green"></div>
                        <p class=" me-2">Active</p>
                    </div>
                </div>
                 <img class="d-none" height="100px" src="image/qr.png" />
            </div>-->
            <div class="row px-3">
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <div class="ask">
                        Employee Name&nbsp;:
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-sm-8">
                    <div class="ans bold">
                        {{ $employee->EMP_NAME }}&nbsp;&nbsp;({{ $employee->EMP_CODE }})
                        <input type="hidden" value="" id="CHEK_EMPS_ALTERNATE_EMAIL_ID">
                    </div>
                </div>
            </div>
            <div class="row px-3">
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <div class="ask">
                        QID&nbsp;:
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-sm-8">
                    <div class="ans">
                        {{ $employee->EMPD_NUMBER }}
                    </div>
                </div>
            </div>
            <div class="row px-3">
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <div class="ask">
                        Joined on&nbsp;:
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-sm-8">
                    <div class="ans">
                        {{ \Carbon\Carbon::parse($employee->EMP_JOIN_DT)->format('d M Y') }}
                    </div>
                </div>
            </div>
            <div class="row px-3">
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <div class="ask">
                        Company&nbsp;:
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-sm-8">
                    <div class="ans">
                        {{ $employee->COMP_NAME }}
                    </div>
                </div>
            </div>
            <div class="row px-3">
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <div class="ask">
                        Sponsor&nbsp;:
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-sm-8">
                    <div class="ans">
                        {{ $employee->DIVN_NAME }}
                    </div>
                </div>
            </div>
            <div class="row px-3">
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <div class="ask">
                        Location&nbsp;:
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-sm-8">
                    <div class="ans">
                        {{ $employee->DEPT_NAME }}
                    </div>
                </div>
            </div>


            <div class="row px-3">
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <div class="ask">
                        Position&nbsp;:
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-sm-8">
                    <div class="ans">
                        {{ $employee->JOB_TITLE_DESC }}
                    </div>
                </div>
            </div>
            <div class="row px-3">
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <div class="ask">
                        Email&nbsp;:
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-sm-8">
                    <div class="ans">
                        {{ $employee->EMPS_PRESENT_EMAIL_ADD }}
                    </div>
                </div>
            </div>
            <div class="row px-3">
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <div class="ask">
                        Nationality&nbsp;:
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-sm-8">
                    <div class="ans">
                        {{ $employee->CNTRY_NATIONALITY }}
                    </div>
                </div>
            </div>
            <div class="row px-3">
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <div class="ask">
                        Contact no.&nbsp;:
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-sm-8">
                    <div class="ans">
                        {{ $employee->EMPS_PRESENT_MOBILE_NO }}
                    </div>
                </div>
            </div>
            <div class="row px-3">
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <div class="ask">
                        DOB&nbsp;:
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-sm-8">
                    <div class="ans">
                        {{ \Carbon\Carbon::parse($employee->EMP_BIRTH_DT)->format('d M Y') }}
                    </div>
                </div>
            </div>
            <div class="row px-3">
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <div class="ask">
                        Ticket Entitlement&nbsp;:
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-sm-8">
                    <div class="ans">
                        {{ $employee->EMP_FLEXI_VAL14 }}
                    </div>
                </div>
            </div>
            <div class="row px-3">
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <div class="ask">
                        Address&nbsp;:
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-sm-8">
                    <div class="ans">
                        {{ $employee->EMPS_PRESENT_ADD_1 }}, {{ $employee->EMPS_PRESENT_ADD_2 }}, {{ $employee->EMPS_PRESENT_ADD_3 }},
                    </div>
                </div>
            </div>
            <div class="row px-3">
                <div class="col-xl-3 col-lg-4 col-sm-4">
                    <div class="ask">
                        Manager&nbsp;:
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-sm-8">
                    <div class="ans">
                        {{ $employee->REPORTING_TO }}&nbsp;&nbsp;-&nbsp;&nbsp;{{ $employee->EMP_REPORTING_TO }}
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>



<x-footer></x-footer>