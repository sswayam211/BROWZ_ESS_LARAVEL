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
                        <option value="Pending" selected>Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Issued">Issued</option>
                        <option value="Rejected">Rejected</option>
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
                                <th style="text-align:center;">Appraisal Code</th>
                                <th style="text-align:center;">Employee</th>
                                <th style="text-align:center;">Date</th>
                                <th style="text-align:center;">Status</th>
                                <th style="text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $srNo = 1; @endphp

                            @foreach($appraisals as $row)

                            @php
                            $status = $row->APAH_TXN_STATUS;
                            $CURR_AUTH_NO = $row->EAU_SEQ_NO;
                            $color = '';
                            $text = '';

                            if ($status == '2') {
                            $color = 'green';
                            $text = 'Approved';

                            } elseif ($status == '1') {

                            if (
                            ($CURR_AUTH_NO == '2' && empty($row->APAH_S2_APP_UID)) ||
                            ($CURR_AUTH_NO == '3' && empty($row->APAH_HR_APP_UID))
                            ) {
                            $color = 'blue';
                            $text = 'Pending';

                            } elseif ($CURR_AUTH_NO == '2' && !empty($row->APAH_S2_APP_UID)) {
                            $color = 'blue';
                            $text = 'HR-Pending';
                            }
                            }

                            //  Format appraisal code
                            $appraisalCode = $row->APAH_TXN_CODE . date('Y') . '_' . str_pad($row->APAH_TXN_NO, 3, '0', STR_PAD_LEFT);
                            @endphp

                            <tr>
                                <td style="text-align:center;">{{ $srNo++ }}</td>
                                <td style="text-align:center;">{{ $appraisalCode }}</td>
                                <td style="text-align:center;">{{ $row->EMP_NAME }}({{ $row->APAH_EMP_CODE }})</td>
                                <td style="text-align:center;">
                                    {{ \Carbon\Carbon::parse($row->APAH_TXN_DT)->format('d-M-Y') }}
                                </td>
                                <td style="text-align:start; color: {{ $color }}; font-weight:bold;">
                                    {{ $text }}
                                </td>
                                <td style="text-align:start; white-space:nowrap;">

                                    {{-- View Button --}}
                                    <a class="btn-sm show-modal-2"
                                        href="{{ route('ApproveAppraisalForm', ['status' => 'appr', 'appr_code' => $row->APAH_SYS_ID]) }}"
                                        title="Approve">
                                        <span style="color:green;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </a>&nbsp;

                                    {{-- Download Button (only when approved) --}}
                                    @if($status == '2')
                                    <button type="button" class="border-0 btn-sm mx-1 DOWNLOAD_LETTER"
                                        name="{{ $row->APAH_SYS_ID }}"
                                        title="Download">
                                        <i class="fa-solid fa-download"></i>
                                    </button>
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
    // filter script 
    document.addEventListener("DOMContentLoaded", function() {
        var statusFilter = document.getElementById("status_filter");

        // Initialize DataTable
        var dataTable;
        if (!$.fn.DataTable.isDataTable('#hr_table')) {
            dataTable = $('#hr_table').DataTable({
                "ordering": true,
                "paging": true
            });
        } else {
            dataTable = $('#hr_table').DataTable();
        }

        function filterTable() {
            var selectedStatus = statusFilter.value.toLowerCase();

            if (selectedStatus === 'all') {
                // Show all data
                dataTable.column(4).search('').draw();
            } else {
                // Filter exact match in column 12 (index 11)
                dataTable.column(4).search('^' + selectedStatus + '$', true, false).draw();
            }
        }

        // By default select "Pending"
        statusFilter.value = 'Pending';
        filterTable();

        // Filter on change
        statusFilter.addEventListener("change", filterTable);
    });
</script>


<!-- download letter -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- <script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.DOWNLOAD_LETTER').forEach(function(element) {
        element.addEventListener('click', function() {

            var APPR_CODE = this.getAttribute('name');

            var formData = new FormData();
            formData.append('download_appr_code', APPR_CODE);
            formData.append('_token', CSRF_TOKEN);

            fetch('{{ route("appraisal.download") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {

                    //  Parse HTML to get employee name
                    const parser = new DOMParser();
                    const htmlDoc = parser.parseFromString(data, 'text/html');
                    const empNameEl = htmlDoc.querySelector('#EMP_NAME');
                    let emp_name = empNameEl ? empNameEl.value : 'Employee';

                    generatePDF(data, emp_name, 'Y');
                })
                .catch(error => console.error('Download Error:', error));
        });
    });


    function generatePDF(htmlString, empName, saveYN = 'N') {
        const {
            jsPDF
        } = window.jspdf;

        //  Create hidden iframe to render with Bootstrap
        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.top = '-9999px';
        iframe.style.left = '-9999px';
        iframe.style.width = '900px';
        iframe.style.height = '1200px';
        iframe.style.border = 'none';
        document.body.appendChild(iframe);

        //  Write full HTML with Bootstrap into iframe
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        iframeDoc.open();
        iframeDoc.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <link rel="stylesheet"
                    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; background: #fff; }
                    .bg-brown { background-color: #8B4513 !important; color: white !important; padding: 5px 10px; }
                    .appraisal-layout { width: 100%; }
                    .layout-heading h4, .layout-heading h5, .layout-heading h6 { color: inherit; }
                </style>
            </head>
            <body>
                ${htmlString}
            </body>
            </html>
        `);
        iframeDoc.close();

        //  Wait for Bootstrap CSS to load then capture
        iframe.onload = function() {
            const contentEl = iframe.contentDocument.body;

            html2canvas(contentEl, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff',
                width: 860,
                windowWidth: 900
            }).then(canvas => {

                const imgData = canvas.toDataURL('image/png');
                const doc = new jsPDF('p', 'mm', 'a4');
                const pageWidth = doc.internal.pageSize.getWidth();
                const pageHeight = doc.internal.pageSize.getHeight();
                const imgWidth = pageWidth;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;

                let yPosition = 0;

                //  Multi-page support
                while (yPosition < imgHeight) {
                    if (yPosition > 0) doc.addPage();
                    doc.addImage(imgData, 'PNG', 0, -yPosition, imgWidth, imgHeight);
                    yPosition += pageHeight;
                }

                if (saveYN === 'Y') {
                    doc.save('Appraisal of ' + empName + '.pdf');
                } else {
                    const pdfUrl = doc.output('bloburl');
                    window.open(pdfUrl, '_blank');
                }

                //  Cleanup
                document.body.removeChild(iframe);
            });
        };
    }
</script> -->


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