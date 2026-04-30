<form action="{{ route('appraisal.kpi-add-update') }}" method="post" id="form-add">
    @csrf
    <div class="row align-items-center">
        <div class="col-md-3 px-2 mb-3">
            <label for="KPI_CODE" class="form-label">KPI Code<span
                    class="text-danger">*</span></label>
            <input type="text" class="form-control shadow-none" id="KPI_CODE" name="KPI_CODE" value="{{ $TXN_CODE }}" readonly required>
        </div>
        <div class="col-md-9 px-2 mb-3">
            <label for="KPI_NAME" class="form-label">KPI Name <span
                    class="text-danger">*</span></label>
            <input type="text" class="form-control shadow-none" id="KPI_NAME" name="KPI_NAME" value="{{ $KPI_NAME }}" required>
        </div>


        <div class="col-md-3 px-2 mb-3 ">
            <div class="form-check form-switch">
                <input class="form-check-input shadow-none" type="checkbox" id="in-active" name="STATUS" value="in-active" {{ $KPI_STATUS=='F'?"checked":"" }}>
                <label class="form-check-label" for="in-active">In-Active</label>
            </div>
        </div>


        <label for="" class="form-label">Evaluated By:</label>
        <div class="col-md-4 px-2 mb-3 ">
            <div class="form-check form-switch">
                <input class="form-check-input shadow-none" type="checkbox" id="APPROVE_BY_SUP" name="APPROVE_BY_SUP" value="YES" {{ $APP_BY_SUP=='Y'?"checked":"" }}>
                <label class="form-check-label" for="APPROVE_BY_SUP">Supervisor</label>
            </div>
        </div>
        <div class="col-md-4 px-2 mb-3 ">
            <div class="form-check form-switch">
                <input class="form-check-input shadow-none" type="checkbox" id="APPROVE_BY_AM" name="APPROVE_BY_AM" value="YES" {{ $APP_BY_AM=='Y'?"checked":"" }}>
                <label class="form-check-label" for="APPROVE_BY_AM">Area Manager</label>
            </div>
        </div>
        <div class="col-md-4 px-2 mb-3 ">
            <div class="form-check form-switch">
                <input class="form-check-input shadow-none" type="checkbox" id="APPROVE_BY_HR" name="APPROVE_BY_HR" value="YES" {{ $APP_BY_HR=='Y'?"checked":"" }}>
                <label class="form-check-label" for="APPROVE_BY_HR">HR</label>
            </div>
        </div>
        <div class="col-12 text-start">
            <button type="submit" class="button rounded-0 mb-4" name="ACTION_BUTTON" value="{{ $FORM_TYPE }}">{{ $FORM_BUTTON_NAME }}</button>
        </div>
    </div>
</form>