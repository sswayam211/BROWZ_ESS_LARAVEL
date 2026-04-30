<form action="">
    <input type="hidden" name="EMP_CODE" value="{{ $EMP_DATA->EMP_CODE }}">
    <input type="hidden" id="EMP_NAME" value="{{ $EMP_DATA->EMP_NAME }}">

    <div class="appraisal-layout">

        {{-- Title --}}
        <div class="layout-heading" style="border:1px solid black;">
            <h4 class="text-center p-1 mb-0 fw-bolder">Opto Evaluation 2026</h4>
        </div>

        {{-- General Info --}}
        <div class="layout-general-info">
            <div class="layout-heading bg-brown">
                <h5 class="mb-0">General Information</h5>
            </div>
            <div class="row">
                <div class="col-8">Name</div>
                <div class="col-4" id="EMP_NAME_DISPLAY">{{ $EMP_DATA->EMP_NAME }}</div>

                <div class="col-8">Job Title</div>
                <div class="col-4">{{ $EMP_DATA->POSITION }}</div>

                <div class="col-8">Department</div>
                <div class="col-4">{{ $EMP_DATA->DEPARTMENT_NAME }}</div>

                <div class="col-8">Evaluation Period</div>
                <div class="col-4"></div>

                <div class="col-8">Supervisor Name</div>
                <div class="col-4">{{ $SUPERVISOR_NAME }}</div>

                <div class="col-8">Evaluation Date</div>
                <div class="col-4">{{ date('d-M-Y') }}</div>
            </div>
        </div>

        {{-- Ranking Info --}}
        <div class="ranking-details">
            <span style="padding: 0px 10px;">
                60%= Very Poor | 70% = Poor | 80% = Good | 90% = Very Good | 100% = Excellent
            </span>
        </div>

        {{-- KPI Scores --}}
        @foreach($groupedData as $kpiCode => $records)

            <div class="layout-evaluation-details">
                <div class="layout-heading bg-brown">
                    <h6 class="mb-0">{{ $records[0]->KPI_DESC }}</h6>
                </div>

                @foreach($records as $row)
                    <div class="row">
                        <div class="col-8">{{ $row->KPI_EC_DESC }}</div>
                        <div class="col-2">{{ $row->KPI_EC_MAX_RANGE }}</div>
                        <div class="col-2">{{ $row->APAS_KPI_EC_GIVEN_SCORE }}</div>
                    </div>
                @endforeach

            </div>

        @endforeach

        {{-- Final Score --}}
        <div class="layout-final-score">
            <div class="row bg-brown">
                <div class="col-8">Total Score</div>
                <div class="col-2">{{ $MAX_SCORE }}</div>
                <div class="col-2">{{ $TOTAL_SCORE }}</div>
            </div>
            <div class="row">
                <div class="col-8">Performance Level-High-Meduim-Low</div>
                <div class="col-4">{{ $PERFORMANCE_LEVEL }}</div>

                <div class="col-8">Areas for Improvement</div>
                <div class="col-4">{{ $TXN_DATA->APAH_HR_APP_AOI }}</div>

                <div class="col-8">Development Plan</div>
                <div class="col-4">{{ $TXN_DATA->APAH_HR_APP_DEV_PLAN }}</div>

                <div class="col-8">Final Recommendation</div>
                <div class="col-4">Approved</div>

                <div class="col-8 bg-brown">Appraisal Outcome</div>
                <div class="col-4 fw-bold">{{ $TXN_DATA->APAH_HR_APP_SUGGESTION }}</div>
            </div>
        </div>

    </div>
</form>