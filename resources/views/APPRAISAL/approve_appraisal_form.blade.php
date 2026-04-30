<div>
    <form action="save_appraisal_data_by_amhr" id="appraisal-form-submit" method="post">
        @csrf
        <div class="row">
            <div class="col-md-4 px-2 mb-3">
                <label for="APPRAISAL_CODE" class="form-label">Apprasial Code<span
                        class="text-danger">*</span></label>
                <input type="text" class="form-control shadow-none" id="APPRAISAL_CODE" name="APPRAISAL_CODE" value="{{ $TXN_CODE }}" readonly required>
                <input type="hidden" class="form-control shadow-none" id="APPRAISAL_SYS_ID" name="APPRAISAL_SYS_ID" value="{{ $SYS_ID }}" readonly required>
                <input type="hidden" class="form-control shadow-none" name="FORM_TYPE" value="APPROVE">
            </div>
            <div class="col-md-4 px-2 mb-3">
                <label for="EMP_NAME" class="form-label">Employee Name<span
                        class="text-danger">*</span></label>
                <input type="hidden" class="form-control shadow-none" id="EMP_CODE" name="EMP_CODE" value="{{ $APRSL_EMP->EMP_CODE }}">
                <input type="text" class="form-control shadow-none" id="EMP_NAME" name="EMP_NAME" value="{{ $APRSL_EMP->EMP_NAME }}" readonly required>
            </div>
            <div class="col-md-4 px-2 mb-3">
                <label for="EMP_POSITION" class="form-label">Employee Position<span
                        class="text-danger">*</span></label>
                <input type="hidden" class="form-control shadow-none" id="EMP_POSITION_CODE" name="EMP_POSITION_CODE" value="{{ $EMP_POSI_CODE }}" readonly required>
                <input type="text" class="form-control shadow-none" id="EMP_POSITION" name="EMP_POSITION" value="{{ $POSITION }}" readonly required>
            </div>
            <div class="col-md-4 px-2 mb-3">
                <label for="EMP_DEPARTMENT" class="form-label">Employee Department<span
                        class="text-danger">*</span></label>
                <input type="hidden" class="form-control shadow-none" id="EMP_DEPARTMENT_CODE" name="EMP_DEPARTMENT_CODE" value="{{ $EMP_DEPT_CODE }}" readonly required>
                <input type="text" class="form-control shadow-none" id="EMP_DEPARTMENT" name="EMP_DEPARTMENT" value="{{ $DEPARTMENT_NAME }}" readonly required>
            </div>
            <div class="col-md-4 px-2 mb-3">
                <label for="EVAL_NAME" class="form-label">Supervisor Name<span
                        class="text-danger">*</span></label>
                <input type="hidden" class="form-control shadow-none" id="EVAL_CODE" name="EVAL_CODE" value="{{ $LOGIN_USER_ID }}" readonly>
                <input type="text" class="form-control shadow-none" id="EVAL_NAME" name="EVAL_NAME" value="{{ $APRSL_EMP->REPORTING_TO }}" readonly required>
            </div>
            <div class="col-md-4 px-2 mb-3">
                <label for="EVAL_DATE" class="form-label">Appraisal Date<span
                        class="text-danger">*</span></label>
                <input type="text" class="form-control shadow-none" id="EVAL_DATE" name="EVAL_DATE" value="{{ date('d-M-Y',strtotime($APRSL_DATE)) }}" readonly required>
            </div>
        </div>
        <hr>


        @if($KPI_CODE && !$FINAL_SUBMIT && !$IS_APPROVED_ALREADY)

        <div id="EVALUATION_PART">
            <div class="row justify-content-between">
                <h5 class="col">Start Evaluation</h5>
                <p class="form-label text-end col">Evaluation Progress: {{ $PROGRESS }}%</p>
            </div>
            <div class="status-bar">
                <div class="status-progress-bar" style="width:{{ $PROGRESS }}%"></div>
            </div>

            <div class="mt-4">
                <h6>{{ $LAST_KPI_INDEX + 1 }}. {{ $KPI_DESC[$KPI_CODE[0]] }}</h6>
                <input type="hidden" name="KPI_CODE" value="{{ $KPI_CODE[0] }}">

                @if(!empty($EC_CODE))

                @foreach($EC_CODE as $index => $code)

                <div class="row flex-row mb-2">

                    {{-- EC Label --}}
                    <div class="col-4 p-1">
                        <label for="EC{{ $code }}" class="form-label">
                            {{ $EC_DESC[$code] ?? 'N/A' }} {{-- keyed by $code --}}
                            <span class="text-danger">*</span> :
                        </label>
                    </div>

                    {{-- Slider --}}
                    <div class="col-4 p-1">
                        <!-- <div class="row">
                            <div class="col-10">

                                <input type="hidden"
                                    name="EC_MAX_SCORE_{{ $code }}"
                                    value="{{ $EC_MAX[$code] ?? 0 }}"> {{-- keyed by $code --}}

                                <input type="range"
                                    class="custom-slider"
                                    style="display:none;"
                                    min="{{ $EC_MIN[$code] ?? 0 }}" {{--  keyed by $code --}}
                                    max="{{ $EC_MAX[$code] ?? 0 }}" {{--  keyed by $code --}}
                                    id="EC{{ $code }}"
                                    name="EC_{{ $code }}"
                                    value="{{ $EC_MIN[$code] ?? 0 }}"> {{-- keyed by $code --}}

                                {{-- Score Steps --}}
                                <div class="score-steps">
                                    @for($i = $EC_MIN[$code] ?? 0; $i <= $EC_MAX[$code] ?? 0; $i++)
                                        <span class="score-step"
                                        onclick="setScore({{ $i }}, 'EC{{ $code }}', this)">
                                        {{ $i }}
                                        </span>
                                        @endfor
                                </div>

                            </div>

                            {{-- Score Badge --}}
                            <div class="col-2">
                                <span class="score-badge d-none" id="score{{ $code }}">0</span>
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="col-10">
                                <input type="hidden"
                                    name="EC_MAX_SCORE_{{ $code }}"
                                    value="{{ $EC_MAX[$code] ?? 0 }}">

                                <div class="slider-wrap" id="wrap_{{ $code }}">
                                    {{--  Floating score bubble on thumb --}}
                                    <div class="score-thumb-label" id="thumb_{{ $code }}">
                                        {{ $EC_MIN[$code] ?? 0 }}
                                    </div>

                                    <input type="range"
                                        class="kpi-slider"
                                        id="EC{{ $code }}"
                                        name="EC_{{ $code }}"
                                        min="{{ $EC_MIN[$code] ?? 0 }}"
                                        max="{{ $EC_MAX[$code] ?? 0 }}"
                                        step="1"
                                        value="{{ $EC_MIN[$code] ?? 0 }}">

                                    <!-- <div class="score-meta">
                                        <span>{{ $EC_MIN[$code] ?? 0 }} (min)</span>
                                        <span id="cur_{{ $code }}">
                                            {{ $EC_MIN[$code] ?? 0 }} / {{ $EC_MAX[$code] ?? 0 }}
                                        </span>
                                        <span>{{ $EC_MAX[$code] ?? 0 }} (max)</span>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Remark --}}
                    <div class="col-4 p-1">
                        <input type="text"
                            class="form-control shadow-none"
                            id="REMARK{{ $code }}"
                            name="REMARK_{{ $code }}"
                            placeholder="Remark"
                            required>
                    </div>

                </div>
                @endforeach

                @else
                <p class="text-danger">No EC data found.</p>
                @endif
            </div>

            <div class="text-end mt-4">
                <button type="submit"
                    class="button rounded-0 mb-4"
                    id="SAVE_APPRAISAL_DATA"
                    name="SAVE_APPRAISAL">
                    Next <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>

        @elseif($KPI_CODE && ($FINAL_SUBMIT || $IS_APPROVED_ALREADY))

        <h4>Evaluation Completed</h4>
        <div class="row border fw-bold">
            <div class="col-8 border-end px-2">KPI Description</div>
            <div class="col-2 border-end px-2 text-center">Max Score</div>
            <div class="col-2 px-2 text-center">Total Score</div>
        </div>

        @php
        $GRAND_MAX = 0;
        $GRAND_TOTAL = 0;
        $KPI_COUNT = 0;
        $LAST_KPI_CODE='';
        @endphp

        @foreach($KPI_CODE as $index => $KPI)

        @php
        $KPI_MAX_SCORE = 0;
        $KPI_TOTAL_SCORE = 0;
        $KPI_COUNT++;
        $ecList = $EC_DATA[$KPI] ?? [];
        $LAST_KPI_CODE = $KPI;
        @endphp

        @foreach($ecList as $ec)
        @php
        $KPI_MAX_SCORE += $ec['max'] ?? 0;
        $KPI_TOTAL_SCORE += $ec['given_score'] ?? 0;
        @endphp
        @endforeach

        @php
        $GRAND_MAX += $KPI_MAX_SCORE;
        $GRAND_TOTAL += $KPI_TOTAL_SCORE;
        @endphp

        <div class="row border" style="font-weight:500;">
            <div class="col-8 border-end px-2">
                {{ $KPI_COUNT }}. {{ $KPI_DESC[$KPI] ?? 'N/A' }}
            </div>
            <div class="col-2 border-end px-2 text-center">
                {{ $KPI_MAX_SCORE }}
            </div>
            <div class="col-2 px-2 text-center">
                {{ $KPI_TOTAL_SCORE }}
            </div>
        </div>

        @endforeach
        <input type="hidden" name="KPI_CODE" value="{{ $LAST_KPI_CODE }}">


        <div class="row border fw-bold">
            <div class="col-8 border-end px-2 text-end">Total:</div>
            <div class="col-2 border-end px-2 text-center">{{ $GRAND_MAX }}</div>
            <div class="col-2 px-2 text-center">{{ $GRAND_TOTAL }}</div>
        </div>

        @if(!$IS_APPROVED_ALREADY)
        <div class="row mt-4">
            <div class="col-md-4 px-2 mb-3">
                <label for="APPR_APP_SUGGESTION" class="form-label">Appraisal Suggestion<span
                        class="text-danger">*</span></label>
                <select class="form-select" id="APPR_APP_SUGGESTION" name="APPR_APP_SUGGESTION" required>
                    <option value="">Select</option>
                    <option value="Promotion">Promotion</option>
                    <option value="Bonus">Bonus</option>
                    <option value="Increment">Increment</option>
                    <option value="Improvement Plan">Improvement Plan</option>
                </select>
            </div>
            <div class="col-md-8 px-2 mb-3">
                <label for="APPR_APP_REMARK" class="form-label">Appraisal Remark<span
                        class="text-danger">*</span></label>
                <input type="text" class="form-control shadow-none" id="APPR_APP_REMARK" name="APPR_APP_REMARK" value="" required>
            </div>
        </div>

        @if($IS_HR)
        <div class="row mt-2">
            <div class="col-md-6 px-2 mb-3">
                <label for="APPR_AREA_OF_IMPRO" class="form-label">Areas Of Improvement<span
                        class="text-danger">*</span></label>
                <input type="text" class="form-control shadow-none" id="APPR_AREA_OF_IMPRO" name="APPR_AREA_OF_IMPRO" value="" required>
            </div>
            <div class="col-md-6 px-2 mb-3">
                <label for="APPR_DEV_PLAN" class="form-label">Development Plan<span
                        class="text-danger">*</span></label>
                <input type="text" class="form-control shadow-none" id="APPR_DEV_PLAN" name="APPR_DEV_PLAN" value="" required>
            </div>
        </div>
        @endif


        @if($APAH_EVAL_STATUS!=='2')
        <div class="text-end mt-4">
            <button type="submit" class="button rounded-0 mb-4" id="SAVE_APPRAISAL_DATA" name="SUBMIT_APPRAISAL">Approve</button>
        </div>
        @endif

        @endif

        @elseif(empty($KPI_CODE) && $FORM_TYPE=='SUGGEST_APPRAISAL')

        <h5>No KPI's assinged to this job add some to start evaluation.</h5>

        @endif

    </form>

</div>