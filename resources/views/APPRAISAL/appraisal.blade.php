<x-header />


<!-- data table cdn  -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">

<!-- datepicker cdn  -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css" /> -->


<style>
    table {
        overflow: auto !important;
    }

    /* table td,
    table th {
        padding: 10px !important;
    } */

    th {
        background-color: #857e7e !important;
        color: white !important;
    }

    table.dataTable th.dt-type-numeric,
    table.dataTable th.dt-type-date,
    table.dataTable td.dt-type-numeric,
    table.dataTable td.dt-type-date {
        text-align: left;
    }

    table.dataTable th.dt-type-numeric div.dt-column-header,
    table.dataTable th.dt-type-numeric div.dt-column-footer,
    table.dataTable th.dt-type-date div.dt-column-header,
    table.dataTable th.dt-type-date div.dt-column-footer,
    table.dataTable td.dt-type-numeric div.dt-column-header,
    table.dataTable td.dt-type-numeric div.dt-column-footer,
    table.dataTable td.dt-type-date div.dt-column-header,
    table.dataTable td.dt-type-date div.dt-column-footer {
        flex-direction: row;
    }

    .active>.page-link,
    .page-link.active {
        z-index: 3;
        color: var(--bs-pagination-active-color);
        background-color: #857e7e;
        border-color: #857e7e;
    }

    .back {
        color: white;
        background-color: #5e5c5b;
        border: none;
        padding: 5px 12px;
    }

    .steps {
        flex-wrap: wrap;
    }

    .step-circle {
        display: block;
        height: 60px;
        width: 60px;
        border-radius: 50%;
        text-align: center;
        align-content: center;
    }
</style>


<!-- Quill CSS(WORD EDIT TAB) -->
<!-- <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet"> -->

<style>
    #editor-container-1,
    #editor-container-2 {
        height: 300px;
        background: #fff;
        font-size: 15PX;
    }

    .text-sm {
        font-size: 12px;
    }
</style>

<div class="p-2 mb-md-0 mb-5 pt-md-1 pb-md-0">

    <div class="panel panel-bd lobidrag ">
        <div class="d-flex justify-content-start align-items-center flex-wrap mb-1">

            <div class="btn-group" id=" ">
                <a class="back mx-1" href="dashboard.php">

                    <i class="fa fa-angle-double-left" style="font-size:18px"></i>&nbsp;&nbsp;Back </a>
            </div>

            <div class="btn-group" id=" ">
                <a class="button mx-1">

                    <i class="fa fa-list"></i>&nbsp;&nbsp; Appraisal </a>
            </div>


            <div class="ms-auto  mb-sm-2" style="width: 200px;">
                <div class="col-sm-12 p-sm-0 py-1">
                    <select id="status_filter" class="d-inline-block" style="min-width: 200px;">
                        <option value="All">All</option>
                        <option value="Start" selected>Start</option>
                        <option value="Draft">Draft</option>
                        <option value="Completed">Completed</option>
                        <!-- <option value="Rejected">Rejected</option> -->
                    </select>
                </div>
            </div>

        </div>
        <div class="panel-body" style='min-height:600px;'>
            <div class="panel-body" style='min-height:600px;'>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover js-basic-example dataTable" id="hr_table">
                        <thead>
                            <tr class="info">
                                <th style="text-align:center;">Sr.No</th>
                                <th style="text-align:start;">Employee Name</th>
                                <th style="text-align:center;">Position</th>
                                <th style="text-align:center;">Sponsor</th>
                                <th style="text-align:center;">Department</th>
                                <th style="text-align:center;">Company</th>
                                <th style="text-align:center;">Progress</th>
                                <th style="text-align:center;">App. Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $count = 1; @endphp

                            @foreach($subordinates as $emp)
                            @if($emp->skip) @continue @endif

                            <tr>
                                <td class="text-center">{{ $count++ }}</td>
                                <td>{{ $emp->EMP_NAME }} ({{ $emp->EMP_CODE }})</td>
                                <td>{{ $emp->JOB_TITLE_DESC }}</td>
                                <td>{{ $emp->DIVN_NAME }}</td>
                                <td>{{ $emp->DEPT_NAME }}</td>
                                <td>{{ $emp->COMP_NAME }}</td>

                                {{-- Progress --}}
                                <td style="text-align:start; white-space:nowrap;">
                                    <a class="btn-sm apply-leave-button show-modal-2" style="display:inline-block;color: {{ $emp->startTextColor }};" href="appraisal_form?APRSL_EMP_CODE={{ $emp->EMP_CODE }}">
                                        {{ $emp->startText }}
                                    </a>
                                </td>

                                {{-- App Status --}}
                                <td class="fw-bold" style="text-align:start; color: {{ $emp->color }};">
                                    {{ $emp->text }}
                                    @if($emp->text == 'Approved')
                                    <button class="border-0 btn-sm mx-1 DOWNLOAD_LETTER" name="{{ $emp->APAH_SYS_ID }}" style="display:inline-block;" title="download"><i class="fa-solid fa-download"></i></button>
                                    @endif
                                </td>
                            </tr>

                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>



</div>



<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
<script>
    // filter select 
    $(document).ready(function() {
        const dataTable = $('#hr_table').DataTable({
            ordering: true,
            paging: true
        });

        const $statusFilter = $('#status_filter');

        function applyStatusFilter() {
            const selectedStatus = $statusFilter.val();

            if (!selectedStatus || selectedStatus.toLowerCase() === 'all') {
                dataTable.column(6).search('').draw();
                return;
            }

            // Use simple substring match so filtering works even when the cell contains HTML.
            dataTable.column(6).search(selectedStatus, false, false, true).draw();
        }

        // Default selection
        $statusFilter.val('Start');
        applyStatusFilter();

        $statusFilter.on('change', applyStatusFilter);
    });
</script>


<!-- download letter -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    function convertStringToHtmlElement(htmlString) {
        const tempElement = document.createElement('div');
        tempElement.innerHTML = htmlString;
        return tempElement.firstChild;
    }


    // DOWNLOADING LETTER USING AJAX
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.DOWNLOAD_LETTER').forEach(function(element) {
        element.addEventListener('click', function() {
            // var VIEW_LETTER = this.getAttribute('VIEW_LETTER');
            var APPR_CODE = this.getAttribute('name');
            // console.log(REQ_CODE);
            let bodyHTML = document.body.innerHTML;


            // Prepare data for POST
            var formData = new FormData();

            formData.append('download_appr_code', APPR_CODE);
            formData.append('_token', CSRF_TOKEN);

            // AJAX request using fetch
            fetch('{{ route("appraisal.download") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {

                    let htmlData = convertStringToHtmlElement(data);
                    console.log(htmlData);

                    console.log(htmlData.querySelector('#EMP_NAME').value);

                    // let letter_name = htmlData.parentNode.children[0].value;
                    let emp_name = htmlData.querySelector('#EMP_NAME').value;
                    // console.log(letter_name + ' of ' + emp_name);

                    let saveYN = 'Y';

                    generatePDF(data, emp_name, saveYN);
                })
                .catch(error => console.error('Error:', error));
        });
    });




    // generate pdf function
    function generatePDF(letterData, empName, saveYN = 'N') {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF();

        const contentDiv = document.createElement('div');
        contentDiv.innerHTML = letterData;
        document.body.appendChild(contentDiv);


        doc.html(contentDiv, {
            callback: function(doc) {

                if (saveYN == 'Y') {
                    // saves letter in system
                    doc.save('Apprasial of ' + empName + '.pdf');
                } else {
                    const pdfBlob = doc.output('blob');
                    const pdfUrl = URL.createObjectURL(pdfBlob);

                    // generate blob file
                    const generatedPDF = {
                        blob: pdfBlob,
                        url: pdfUrl,
                        name: `${letterName} of ${empName}.pdf`
                    };

                    window.open(pdfUrl, '_blank');
                }

                document.body.removeChild(contentDiv);
            },
            x: 10,
            y: 20,
            width: 190,
            windowWidth: 800
        });
    }
</script>

<x-appraisal-modal />



<x-footer />